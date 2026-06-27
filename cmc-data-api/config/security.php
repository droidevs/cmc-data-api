<?php

return [

    'request_id_header' => env('SECURITY_REQUEST_ID_HEADER', 'X-Request-Id'),

    'trusted_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('SECURITY_TRUSTED_ORIGINS', env('APP_URL', 'http://localhost')))
    ))),

    'csrf' => [
        'origin_only' => (bool) env('CSRF_ORIGIN_ONLY', false),
        'allow_same_site' => (bool) env('CSRF_ALLOW_SAME_SITE', false),
    ],

    'cross_site_requests' => [
        'enforce_for_api_writes' => (bool) env('SECURITY_ENFORCE_API_ORIGIN', true),
        'require_origin_header' => (bool) env('SECURITY_REQUIRE_API_ORIGIN_HEADER', false),
        'allow_same_site' => (bool) env('SECURITY_ALLOW_SAME_SITE_WRITES', true),
    ],

    'rate_limits' => [
        'api' => [
            'read_per_minute' => (int) env('API_READ_RATE_LIMIT_PER_MINUTE', 120),
            'read_per_hour' => (int) env('API_READ_RATE_LIMIT_PER_HOUR', 1500),
            'write_per_minute' => (int) env('API_WRITE_RATE_LIMIT_PER_MINUTE', 30),
            'write_per_hour' => (int) env('API_WRITE_RATE_LIMIT_PER_HOUR', 300),
            'route_per_minute' => (int) env('API_ROUTE_RATE_LIMIT_PER_MINUTE', 60),
        ],

        'web' => [
            'per_minute' => (int) env('WEB_RATE_LIMIT_PER_MINUTE', 180),
        ],
    ],

    'audit' => [
        'enabled' => (bool) env('AUDIT_LOG_ENABLED', true),
        'channel' => env('AUDIT_LOG_CHANNEL', 'audit'),
        'log_read_failures' => (bool) env('AUDIT_LOG_READ_FAILURES', true),
        'include_sanitized_input' => (bool) env('AUDIT_LOG_INCLUDE_INPUT', false),
        'redacted_fields' => [
            'password',
            'password_confirmation',
            'token',
            'api_token',
            'access_token',
            'refresh_token',
            'authorization',
            '_token',
        ],
        'except_paths' => [
            'up',
            'favicon.ico',
            'robots.txt',
        ],
    ],

];
