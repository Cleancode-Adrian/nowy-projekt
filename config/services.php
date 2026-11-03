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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Baner zgód na cookies (Cookiebot CMP). Pusta wartość wyłącza skrypt.
    'cookiebot' => [
        'cbid' => env('COOKIEBOT_CBID', 'fc720780-9d1c-4e63-a0c4-204d38173953'),
        // Język banera wymuszony na polski (bez tego Cookiebot zgaduje z przeglądarki).
        'culture' => env('COOKIEBOT_CULTURE', 'PL'),
    ],

    // Meta Pixel. Wysyła dane dopiero po zgodzie marketingowej w Cookiebot.
    'meta_pixel' => [
        'id' => env('META_PIXEL_ID', '1128855549687710'),
        // Conversions API - bez tokenu wysyłka z serwera jest wyłączona.
        'capi_token' => env('META_CAPI_TOKEN'),
        // Kod z Menedżera zdarzeń -> Testuj zdarzenia (tylko na czas testów).
        'capi_test_code' => env('META_CAPI_TEST_CODE'),
        'graph_version' => env('META_GRAPH_VERSION', 'v21.0'),
    ],

];
