<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityMiddlewareTest extends TestCase
{
    public function test_cors_preflight_allows_configured_origins(): void
    {
        config([
            'cors.allowed_origins' => ['https://client.example'],
            'security.trusted_origins' => ['https://client.example'],
        ]);

        $response = $this
            ->withHeaders([
                'Origin' => 'https://client.example',
                'Access-Control-Request-Method' => 'POST',
                'Access-Control-Request-Headers' => 'Content-Type, X-Request-Id',
            ])
            ->options('/api/v1/poles');

        $response->assertNoContent();
        $response->assertHeader('Access-Control-Allow-Origin', 'https://client.example');
    }

    public function test_cross_site_api_write_from_untrusted_origin_is_blocked(): void
    {
        Route::middleware('api')->post('/api/security-test-write', fn () => response()->json(['ok' => true]));

        config([
            'cors.allowed_origins' => ['https://client.example'],
            'security.trusted_origins' => ['https://client.example'],
        ]);

        $response = $this
            ->withHeaders([
                'Origin' => 'https://evil.example',
                'Sec-Fetch-Site' => 'cross-site',
            ])
            ->postJson('/api/security-test-write', ['name' => 'blocked']);

        $response->assertForbidden();
        $response->assertJsonPath('message', 'Cross-site write request blocked.');
    }

    public function test_request_id_is_returned_on_responses(): void
    {
        Route::middleware('api')->get('/api/security-test-request-id', fn () => response()->json(['ok' => true]));

        $response = $this
            ->withHeaders(['X-Request-Id' => 'test-request-123'])
            ->getJson('/api/security-test-request-id');

        $response->assertOk();
        $response->assertHeader('X-Request-Id', 'test-request-123');
    }

    public function test_api_rate_limit_returns_json_with_request_id(): void
    {
        Route::middleware('api')->get('/api/security-test-rate-limit', fn () => response()->json(['ok' => true]));

        config([
            'security.rate_limits.api.read_per_minute' => 1,
            'security.rate_limits.api.read_per_hour' => 100,
            'security.rate_limits.api.route_per_minute' => 100,
        ]);

        $this
            ->withHeaders(['X-Request-Id' => 'rate-limit-request-1'])
            ->getJson('/api/security-test-rate-limit')
            ->assertOk();

        $response = $this
            ->withHeaders(['X-Request-Id' => 'rate-limit-request-2'])
            ->getJson('/api/security-test-rate-limit');

        $response->assertTooManyRequests();
        $response->assertJsonPath('request_id', 'rate-limit-request-2');
    }
}
