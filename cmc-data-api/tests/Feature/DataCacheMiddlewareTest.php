<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DataCacheMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'data_cache.enabled' => true,
            'data_cache.store' => 'array',
            'data_cache.ttl' => 600,
            'data_cache.cache_authenticated_requests' => false,
        ]);

        Cache::store('array')->flush();
    }

    public function test_api_get_responses_are_cached(): void
    {
        Route::middleware('api')->get('/api/cache-test/items', function () {
            static $count = 0;

            return response()->json(['count' => ++$count]);
        });

        $this->getJson('/api/cache-test/items')
            ->assertOk()
            ->assertHeader('X-Data-Cache', 'MISS')
            ->assertJsonPath('count', 1);

        $this->getJson('/api/cache-test/items')
            ->assertOk()
            ->assertHeader('X-Data-Cache', 'HIT')
            ->assertJsonPath('count', 1);
    }

    public function test_successful_writes_flush_cached_api_data(): void
    {
        Route::middleware('api')->get('/api/cache-test/flushable', function () {
            static $count = 0;

            return response()->json(['count' => ++$count]);
        });

        Route::middleware('api')->post('/api/cache-test/flushable', fn () => response()->json(['ok' => true]));

        $this->getJson('/api/cache-test/flushable')
            ->assertOk()
            ->assertHeader('X-Data-Cache', 'MISS')
            ->assertJsonPath('count', 1);

        $this->getJson('/api/cache-test/flushable')
            ->assertOk()
            ->assertHeader('X-Data-Cache', 'HIT')
            ->assertJsonPath('count', 1);

        $this->postJson('/api/cache-test/flushable')
            ->assertOk()
            ->assertHeader('X-Data-Cache-Flushed', 'true');

        $this->getJson('/api/cache-test/flushable')
            ->assertOk()
            ->assertHeader('X-Data-Cache', 'MISS')
            ->assertJsonPath('count', 2);
    }
}
