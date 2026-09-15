<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Secrets for external services. None of them may be hard coded in the
    | application source; set them in the .env file of each environment.
    |
    */

    'fcm' => [
        // Firebase Cloud Messaging legacy HTTP server key.
        'key' => env('FCM_SERVER_KEY'),
        'endpoint' => env('FCM_ENDPOINT', 'https://fcm.googleapis.com/fcm/send'),
    ],

    'push' => [
        // Shared secret required (GSX-CRON-TOKEN header) to trigger
        // GET /api/push/check from the cron job.
        'cron_token' => env('PUSH_CHECK_TOKEN'),
    ],

];
