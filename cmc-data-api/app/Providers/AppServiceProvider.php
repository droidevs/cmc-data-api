<?php

namespace App\Providers;

use App\Support\Security\SecurityContext;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $clientKey = SecurityContext::clientKey($request);
            $routeKey = SecurityContext::routeKey($request);
            $response = $this->rateLimitResponse();

            $limits = $this->isWriteRequest($request)
                ? [
                    Limit::perMinute(config('security.rate_limits.api.write_per_minute', 30))->by($clientKey.':write:minute'),
                    Limit::perHour(config('security.rate_limits.api.write_per_hour', 300))->by($clientKey.':write:hour'),
                    Limit::perMinute(config('security.rate_limits.api.route_per_minute', 60))->by($clientKey.':'.$routeKey.':write:route-minute'),
                ]
                : [
                    Limit::perMinute(config('security.rate_limits.api.read_per_minute', 120))->by($clientKey.':read:minute'),
                    Limit::perHour(config('security.rate_limits.api.read_per_hour', 1500))->by($clientKey.':read:hour'),
                    Limit::perMinute(config('security.rate_limits.api.route_per_minute', 60))->by($clientKey.':'.$routeKey.':read:route-minute'),
                ];

            return array_map(fn (Limit $limit) => $limit->response($response), $limits);
        });

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(config('security.rate_limits.web.per_minute', 180))
                ->by(SecurityContext::clientKey($request).':web')
                ->response($this->rateLimitResponse());
        });
    }

    private function isWriteRequest(Request $request): bool
    {
        return ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true);
    }

    private function rateLimitResponse(): callable
    {
        return function (Request $request, array $headers) {
            $requestId = $request->attributes->get('request_id');

            return response()->json([
                'message' => 'Too many requests. Please wait before trying again.',
                'request_id' => $requestId,
            ], 429, [
                ...$headers,
                config('security.request_id_header', 'X-Request-Id') => $requestId,
            ]);
        };
    }
}
