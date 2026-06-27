<?php

namespace App\Http\Middleware;

use App\Support\Security\SecurityContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $this->write($request, null, $startedAt, $exception);

            throw $exception;
        }

        $this->write($request, $response, $startedAt);

        return $response;
    }

    private function write(Request $request, ?Response $response, float $startedAt, ?Throwable $exception = null): void
    {
        if (! $this->shouldAudit($request, $response, $exception)) {
            return;
        }

        $status = $response?->getStatusCode() ?? 500;
        $context = [
            'request_id' => $request->attributes->get('request_id'),
            'event' => $exception ? 'http_exception' : 'http_request',
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => SecurityContext::routeKey($request),
            'client' => SecurityContext::clientKey($request),
            'user_id' => $request->user()?->getAuthIdentifier(),
            'ip' => SecurityContext::fingerprint((string) $request->ip()),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'status' => $status,
            'duration_ms' => round((microtime(true) - $startedAt) * 1000, 2),
            'request_fields' => array_keys($request->except(config('security.audit.redacted_fields', []))),
            'route_parameters' => $this->routeParameters($request),
        ];

        if (config('security.audit.include_sanitized_input', false)) {
            $context['input'] = SecurityContext::sanitizedInput($request);
        }

        if ($exception) {
            $context['exception'] = $exception::class;
            $context['exception_message'] = $exception->getMessage();
        }

        $level = $status >= 400 || $exception ? 'warning' : 'info';

        Log::channel(config('security.audit.channel', 'audit'))->{$level}('audit.request', $context);
    }

    private function shouldAudit(Request $request, ?Response $response, ?Throwable $exception): bool
    {
        if (! config('security.audit.enabled', true)) {
            return false;
        }

        foreach ((array) config('security.audit.except_paths', []) as $path) {
            if ($request->is($path)) {
                return false;
            }
        }

        if ($exception) {
            return true;
        }

        $status = $response?->getStatusCode() ?? 500;

        if ($status >= 400) {
            return config('security.audit.log_read_failures', true) || ! $this->isReadRequest($request);
        }

        return ! $this->isReadRequest($request);
    }

    private function isReadRequest(Request $request): bool
    {
        return in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);
    }

    private function routeParameters(Request $request): array
    {
        $route = $request->route();

        if (! $route) {
            return [];
        }

        return collect($route->parameters())
            ->map(function ($value) {
                if (is_object($value) && method_exists($value, 'getKey')) {
                    return [
                        'type' => $value::class,
                        'id' => $value->getKey(),
                    ];
                }

                if (is_scalar($value) || $value === null) {
                    return $value;
                }

                return get_debug_type($value);
            })
            ->all();
    }
}
