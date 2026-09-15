<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail configuration
    |--------------------------------------------------------------------------
    |
    | Transactional e-mails are sent with PHPMailer (App\Models\Mail) using the
    | "smtp" mailer settings below. Credentials were previously hard coded in
    | the source code; they now come exclusively from the environment.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    'mailers' => [

        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => (int) env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'no-reply@uxsense.com.br'),
        'name' => env('MAIL_FROM_NAME', env('APP_NAME', 'UXSense')),
    ],

];
