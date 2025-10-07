<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => Fickrr\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],
	'facebook' => [
        'client_id'     => config('services.facebook.client_id'),
        'client_secret' => config('services.facebook.client_secret'),
        'redirect'      => config('services.facebook.redirect'),
    ],
    'google' => [
        'client_id'     => config('services.google.client_id'),
        'client_secret' => config('services.google.client_secret'),
        'redirect'      => config('services.google.redirect'),
    ],
	'paytm-wallet' => [
        'env' => config('services.paytm-wallet.env'), // values : (local | production)
        'merchant_id' => config('services.paytm-wallet.merchant_id'),
        'merchant_key' => config('services.paytm-wallet.merchant_key'),
        'merchant_website' => config('services.paytm-wallet.merchant_website'),
        'channel' => config('services.paytm-wallet.channel'),
        'industry_type' => config('services.paytm-wallet.industry_type'),
    ],
	
	

];
