<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Store mode
    |--------------------------------------------------------------------------
    |
    | physical — single-seller catalog of shippable goods (this branch).
    | digital  — the original file, licence, and download marketplace.
    |
    | This branch defaults to physical. Deployment must point DB_DATABASE
    | at a database that is not the auto-script-hub.com store.
    |
    */

    'mode' => env('STORE_MODE', 'physical'),

    'name' => env('STORE_NAME', 'ESIPRES'),

    'url' => env('STORE_URL', 'https://esipres.com'),

    'support_email' => env('STORE_SUPPORT_EMAIL', 'support@esipres.com'),

    'admin_email' => env('STORE_ADMIN_EMAIL'),

    'currency' => env('STORE_CURRENCY', 'USD'),

    'meta_description' => env(
        'STORE_META_DESCRIPTION',
        'ESIPRES is a store at esipres.com. Pay by card or PayPal, then the order ships to your address.'
    ),

    'default_payment' => env('STORE_DEFAULT_PAYMENT', 'stripe'),

    'stripe_enabled' => filter_var(env('STORE_STRIPE_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    /*
    | Live charges stay off unless the matching flag is true, the app is in
    | production, and the provider credentials are present. Local and test
    | requests never create a charge.
    */
    'stripe_charges_enabled' => filter_var(env('STRIPE_CHARGES_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'stripe_secret' => env('STRIPE_SECRET'),

    'stripe_key' => env('STRIPE_KEY'),

    'paypal_enabled' => filter_var(env('PAYPAL_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    'paypal_charges_enabled' => filter_var(env('PAYPAL_CHARGES_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_CLIENT_SECRET'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    ],

    /*
    | When true (production deployment env), boot refuses an empty or shared
    | default database name. The auto-script-hub database is always refused
    | outside the test suite, even when this flag is false.
    */
    'enforce_separate_database' => filter_var(env('STORE_ENFORCE_DATABASE', false), FILTER_VALIDATE_BOOLEAN),

    'forbidden_databases' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('STORE_FORBIDDEN_DATABASES', 'autoscripthub,auto_script_hub,auto-script-hub'))
    ))),

    'shipping' => [
        'flat_fee' => (float) env('STORE_SHIPPING_FLAT_FEE', 0),
        'free_over' => env('STORE_FREE_SHIPPING_OVER') === null || env('STORE_FREE_SHIPPING_OVER') === ''
            ? null
            : (float) env('STORE_FREE_SHIPPING_OVER'),
    ],

];
