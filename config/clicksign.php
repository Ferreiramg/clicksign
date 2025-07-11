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
    | production API v3. You can change this to the sandbox URL for testing.
    |
    */

    'base_url' => env('CLICKSIGN_BASE_URL', 'https://app.clicksign.com/api/v3'),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, this will use the Clicksign sandbox environment.
    | Useful for testing and development.
    |
    */

    'sandbox' => env('CLICKSIGN_SANDBOX', false),

    /*
    |--------------------------------------------------------------------------
    | Sandbox URL
    |--------------------------------------------------------------------------
    |
    | The sandbox URL for testing purposes.
    |
    */

    'sandbox_url' => env('CLICKSIGN_SANDBOX_URL', 'https://sandbox.clicksign.com/api/v3'),

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
    | Default settings for envelopes and signing process in API v3.
    |
    */

    'defaults' => [
        'envelope' => [
            'locale' => 'pt-BR',
            'auto_close' => true,
            'remind_interval' => 3,
            'block_after_refusal' => true,
        ],
        
        'signer' => [
            'has_documentation' => true,
            'refusable' => false,
            'group' => 1,
            'communicate_events' => [
                'document_signed' => 'email',
                'signature_request' => 'email',
                'signature_reminder' => 'email'
            ]
        ],
        
        'requirements' => [
            'signature_role' => 'sign',
            'auth_method' => 'email'
        ]
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
