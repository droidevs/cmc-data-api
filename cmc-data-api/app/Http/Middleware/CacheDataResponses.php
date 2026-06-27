<?php

namespace App\Http\Middleware;

use App\Support\Caching\DataResponseCache;
use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CacheDataResponses
{
    public function __construct(private DataResponseCache $cache) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->cache->enabled()) {
            return $next($request);
        }

        if (! $this->cache->shouldRead($request)) {
            return $this->handleWriteOrBypass($request, $next);
        }

        $key = $this->cache->key($request);

        try {
            if ($payload = $this->cache->get($request, $key)) {
                return $this->cache->responseFromPayload($payload, 'HIT');
            }

            return $this->cache->rememberWithLock($request, $key, fn () => $next($request));
        } catch (LockTimeoutException) {
            return tap($next($request), fn (Response $response) => $response->headers->set('X-Data-Cache', 'BYPASS'));
        } catch (Throwable $exception) {
            Log::warning('data_cache.bypassed', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
                'path' => $request->path(),
            ]);

            return tap($next($request), fn (Response $response) => $response->headers->set('X-Data-Cache', 'BYPASS'));
        }
    }

    private function handleWriteOrBypass(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethodCacheable() && $response->getStatusCode() < 400) {
            try {
                $this->cache->flush();
                $response->headers->set('X-Data-Cache-Flushed', 'true');
            } catch (Throwable $exception) {
                Log::warning('data_cache.flush_failed', [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                    'path' => $request->path(),
                ]);
            }
        }

        return $response;
    }
}
