<?php

namespace App\Support\Caching;

use App\Support\Security\SecurityContext;
use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class DataResponseCache
{
    public function enabled(): bool
    {
        return (bool) config('data_cache.enabled', true);
    }

    public function shouldRead(Request $request): bool
    {
        if (! $this->enabled() || ! $request->isMethodCacheable()) {
            return false;
        }

        if (! $this->matchesConfiguredPath($request, 'paths')) {
            return false;
        }

        if ($this->matchesConfiguredPath($request, 'except_paths')) {
            return false;
        }

        if (! config('data_cache.cache_authenticated_requests', false) && $this->hasCallerIdentity($request)) {
            return false;
        }

        return true;
    }

    public function shouldStore(Response $response): bool
    {
        return in_array($response->getStatusCode(), (array) config('data_cache.cacheable_statuses', [200]), true)
            && $response->getContent() !== false
            && ! $response->isRedirection()
            && ! $response->headers->has('Set-Cookie');
    }

    public function get(Request $request, string $key): ?array
    {
        return $this->repository($request)->get($key);
    }

    public function put(Request $request, string $key, Response $response): void
    {
        $this->repository($request)->put($key, $this->payload($response), $this->ttl());
    }

    public function rememberWithLock(Request $request, string $key, callable $callback): Response
    {
        $lock = Cache::store($this->store())->lock(
            'data-response-cache:lock:'.$key,
            (int) config('data_cache.lock_seconds', 10)
        );

        return $lock->block((int) config('data_cache.lock_wait_seconds', 3), function () use ($request, $key, $callback) {
            if ($payload = $this->get($request, $key)) {
                return $this->responseFromPayload($payload, 'HIT');
            }

            $response = $callback();

            if ($this->shouldStore($response)) {
                $this->put($request, $key, $response);
                $response->headers->set('X-Data-Cache', 'MISS');
            } else {
                $response->headers->set('X-Data-Cache', 'BYPASS');
            }

            return $response;
        });
    }

    public function flush(): bool
    {
        return $this->repository()->flush();
    }

    public function key(Request $request): string
    {
        return 'data-response:'.hash('sha256', json_encode([
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => SecurityContext::routeKey($request),
            'accept' => $request->headers->get('Accept'),
            'identity' => $this->identityKey($request),
        ], JSON_THROW_ON_ERROR));
    }

    public function responseFromPayload(array $payload, string $cacheStatus = 'HIT'): Response
    {
        return response(
            $payload['content'] ?? '',
            (int) ($payload['status'] ?? 200),
            (array) ($payload['headers'] ?? [])
        )->header('X-Data-Cache', $cacheStatus);
    }

    private function repository(?Request $request = null): Repository
    {
        $repository = Cache::store($this->store());

        if ($repository->supportsTags()) {
            return $repository->tags($this->tags($request));
        }

        return $repository;
    }

    private function payload(Response $response): array
    {
        return [
            'status' => $response->getStatusCode(),
            'headers' => $this->cacheableHeaders($response),
            'content' => $response->getContent(),
            'cached_at' => now()->toIso8601String(),
        ];
    }

    private function cacheableHeaders(Response $response): array
    {
        $headers = [];

        foreach ((array) config('data_cache.headers', ['Content-Type']) as $header) {
            if ($response->headers->has($header)) {
                $headers[$header] = $response->headers->get($header);
            }
        }

        return $headers;
    }

    private function tags(?Request $request = null): array
    {
        $tags = [(string) config('data_cache.tag', 'cmc-data')];

        if ($request) {
            $tags[] = 'route:'.SecurityContext::routeKey($request);
        }

        return $tags;
    }

    private function ttl(): int
    {
        return max(1, (int) config('data_cache.ttl', 600));
    }

    private function store(): string
    {
        return (string) config('data_cache.store', 'redis');
    }

    private function matchesConfiguredPath(Request $request, string $configKey): bool
    {
        foreach ((array) config('data_cache.'.$configKey, []) as $path) {
            if ($request->is($path)) {
                return true;
            }
        }

        return false;
    }

    private function hasCallerIdentity(Request $request): bool
    {
        return $request->user() !== null
            || $request->bearerToken() !== null
            || $request->headers->has('X-Api-Key');
    }

    private function identityKey(Request $request): string
    {
        if (! $this->hasCallerIdentity($request)) {
            return 'public';
        }

        return SecurityContext::clientKey($request);
    }
}
