<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Clicksign Access Token
    |--------------------------------------------------------------------------
    |
    | Your Clicksign API access token. You can get this from your Clicksign
    | account settings. Make sure to keep this secure and never commit it
    | to version control.
    |
    */

    'access_token' => env('CLICKSIGN_ACCESS_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Clicksign API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Clicksign API. By default, this points to the
    | production API. You can change this to the sandbox URL for testing.
    |
    */

    'base_url' => env('CLICKSIGN_BASE_URL', 'https://app.clicksign.com/api/v1'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The secret key used to verify webhook signatures from Clicksign.
    | This helps ensure that webhook requests are actually coming from
    | Clicksign and not from malicious sources.
    |
    */

    'webhook_secret' => env('CLICKSIGN_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for document creation and signing process.
    |
    */

    'defaults' => [
        'skip_email' => false,
        'ordered' => false,
        'delivery_method' => 'email',
        'authentication_method' => 'email',
    ],

    /*
    |--------------------------------------------------------------------------
    | Timeout Settings
    |--------------------------------------------------------------------------
    |
    | HTTP timeout settings for API requests.
    |
    */

    'timeout' => [
        'connect' => 10,
        'request' => 30,
    ],

];
