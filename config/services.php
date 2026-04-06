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
    ],

    /*
     * Mapbox: use a public token (pk.*) for client-side maps. Never put a secret token (sk.*) in frontend code.
     * Get tokens at https://account.mapbox.com/access-tokens/
     */
    'mapbox' => [
        'public_token' => env('MAPBOX_PUBLIC_TOKEN', ''),
    ],

    /*
     * Exchange rate API (e.g. currencylayer). Used by currency:fetch-rates to fill currency_rates.
     * EXCHANGERATE_URL = API endpoint; api_key is appended as query param.
     */
    'exchangerate' => [
        'url' => env('EXCHANGERATE_URL'),
        'api_key' => env('EXCHANGERATE_API_KEY'),
    ],

    /*
     * Translation API endpoint for wizard auto-translation.
     * Default uses a free public service for modest usage.
     */
    'translation' => [
        'url' => env('TRANSLATION_API_URL', 'https://api.mymemory.translated.net/get'),
    ],

];
