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
        'ESIPRES is a physical products store at esipres.com. Browse items, check out with a delivery address, and pay cash on delivery.'
    ),

    'default_payment' => env('STORE_DEFAULT_PAYMENT', 'cod'),

    'cod_enabled' => filter_var(env('STORE_COD_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    'stripe_enabled' => filter_var(env('STORE_STRIPE_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    /*
    | Live card charges stay off unless this is explicitly true AND the app
    | is running in production. Local and test requests never create a charge.
    */
    'stripe_charges_enabled' => filter_var(env('STRIPE_CHARGES_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'stripe_secret' => env('STRIPE_SECRET'),

    'stripe_key' => env('STRIPE_KEY'),

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
