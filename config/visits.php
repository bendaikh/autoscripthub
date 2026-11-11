<?php

return [
    // Set VISIT_TRACKING=false to stop recording without removing the dashboard.
    'enabled' => env('VISIT_TRACKING', true),

    // How long a visitor stays in the realtime list.
    'realtime_minutes' => 5,

    // Days of raw rows to keep when `php artisan visits:prune` runs.
    // 0 means the command refuses to delete anything.
    'retain_days' => (int) env('VISIT_RETAIN_DAYS', 0),

    // Optional DB-IP country lite database. Country headers are used first.
    'geoip_path' => storage_path('app/geoip/dbip-country-lite.mmdb'),
];
