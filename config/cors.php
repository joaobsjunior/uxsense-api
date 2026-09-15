<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Replaces the raw header() calls that used to live in public/index.php.
    | Restrict CORS_ALLOWED_ORIGINS to the front-end origins in production,
    | e.g. CORS_ALLOWED_ORIGINS=https://www.uxsense.com.br
    |
    */

    'paths' => ['api', 'api/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', '*'))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Origin', 'Content-Type', 'Accept', 'Authorization', 'X-Requested-With', 'X-Auth-Token', 'GSX-DEVICE', 'GSX-CODE', 'GSX-TOKEN', 'GSX-CRON-TOKEN'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
