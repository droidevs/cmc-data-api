<?php

namespace App\Support\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SecurityContext
{
    public static function requestId(Request $request): string
    {
        $header = (string) config('security.request_id_header', 'X-Request-Id');
        $incoming = $request->headers->get($header);

        if (is_string($incoming) && preg_match('/^[A-Za-z0-9._:-]{8,128}$/', $incoming) === 1) {
            return $incoming;
        }

        return (string) Str::uuid();
    }

    public static function clientKey(Request $request): string
    {
        if ($user = $request->user()) {
            return 'user:'.$user->getAuthIdentifier();
        }

        if ($token = $request->bearerToken()) {
            return 'bearer:'.self::fingerprint($token);
        }

        if ($apiKey = $request->header('X-Api-Key')) {
            return 'api-key:'.self::fingerprint($apiKey);
        }

        return 'ip:'.self::fingerprint((string) $request->ip());
    }

    public static function routeKey(Request $request): string
    {
        $route = $request->route();

        if ($route?->getName()) {
            return $route->getName();
        }

        if ($route && method_exists($route, 'uri')) {
            return $route->uri();
        }

        return trim($request->path(), '/') ?: '/';
    }

    public static function fingerprint(string $value): string
    {
        $key = (string) config('app.key', 'local');

        return substr(hash_hmac('sha256', $value, $key), 0, 24);
    }

    public static function sanitizedInput(Request $request): array
    {
        $input = $request->except(config('security.audit.redacted_fields', []));

        return Arr::map($input, function ($value) {
            if (is_array($value)) {
                return '[array:'.count($value).']';
            }

            if (is_bool($value) || is_int($value) || is_float($value) || $value === null) {
                return $value;
            }

            return Str::limit((string) $value, 120, '...');
        });
    }

    public static function trustedOrigins(): array
    {
        return array_values(array_unique(array_filter([
            ...((array) config('security.trusted_origins', [])),
            ...((array) config('cors.allowed_origins', [])),
        ])));
    }

    public static function isTrustedOrigin(?string $origin): bool
    {
        if (! is_string($origin) || $origin === '') {
            return false;
        }

        $origin = rtrim($origin, '/');

        foreach (self::trustedOrigins() as $trustedOrigin) {
            $trustedOrigin = rtrim((string) $trustedOrigin, '/');

            if ($trustedOrigin === '*' || Str::is($trustedOrigin, $origin)) {
                return true;
            }
        }

        foreach ((array) config('cors.allowed_origins_patterns', []) as $pattern) {
            if (@preg_match($pattern, $origin) === 1) {
                return true;
            }
        }

        return false;
    }
}
