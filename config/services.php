<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
    ],

    'monday' => [
        'token' => env('MONDAY_API_TOKEN'),
        'url' => env('MONDAY_API_URL', 'https://api.monday.com/v2'),
        'portfolio_board_id' => env('MONDAY_PORTFOLIO_BOARD_ID', '8914323121'), // Your ID here
    ],

    // Add this to your config/services.php
    'tracking' => [
        'url' => env('TRACKING_API_URL', 'https://tracking.contractorgrowthnetwork.com'),
        'timeout' => env('TRACKING_API_TIMEOUT', 30),
    ],

    'keywords_com' => [
        'api_key' => env('KEYWORDS_COM_API_KEY'),
        'url' => env('KEYWORDS_COM_API_URL', 'https://app.keyword.com/api/v2'),
    ],


];
