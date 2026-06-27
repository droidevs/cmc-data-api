<?php

return [

    'enabled' => (bool) env('DATA_CACHE_ENABLED', true),

    'store' => env('DATA_CACHE_STORE', 'redis'),

    'tag' => env('DATA_CACHE_TAG', 'cmc-data'),

    'ttl' => (int) env('DATA_CACHE_TTL', 600),

    'lock_seconds' => (int) env('DATA_CACHE_LOCK_SECONDS', 10),

    'lock_wait_seconds' => (int) env('DATA_CACHE_LOCK_WAIT_SECONDS', 3),

    'cache_authenticated_requests' => (bool) env('DATA_CACHE_AUTHENTICATED', false),

    'paths' => [
        'api/*',
    ],

    'except_paths' => [
        'api/*/csrf-cookie',
    ],

    'cacheable_statuses' => [200],

    'headers' => [
        'Content-Type',
        'ETag',
        'Last-Modified',
    ],

];
