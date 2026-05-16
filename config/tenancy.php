<?php

return [
    'enabled' => env('TENANCY_ENABLED', false),

    'central_domains' => [
        env('CENTRAL_DOMAIN', 'dhaka-magazine.test'),
    ],

    'tenant_model' => App\Models\Tenant::class,

    'subdomain_pattern' => env('TENANCY_SUBDOMAIN_PATTERN', '*.dhaka-magazine.test'),

    'queue_prefix' => env('TENANCY_QUEUE_PREFIX', true),

    'media_path' => env('TENANCY_MEDIA_PATH', 'tenants/%s/media'),
];
