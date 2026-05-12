<?php

return [
    'database' => [
        'enabled' => env('ADS_DATABASE_ENABLED', true),
        'model' => App\Models\Advertisement::class,
        'cache_prefix' => 'ads:slot:',
    ],

    'defaults' => [
        'label' => 'বিজ্ঞাপন',
        'is_active' => true,
    ],

    'slots' => [
        'category-top' => [
            'name' => 'category-top',
            'label' => 'বিজ্ঞাপন',
            'desktop_size' => '970x90',
            'mobile_size' => '320x100',
            'image_url' => null,
            'target_url' => null,
            'html_code' => null,
            'is_active' => true,
        ],
        'category-in-feed' => [
            'name' => 'category-in-feed',
            'label' => 'বিজ্ঞাপন',
            'desktop_size' => '336x280',
            'mobile_size' => '300x250',
            'image_url' => null,
            'target_url' => null,
            'html_code' => null,
            'is_active' => true,
        ],
        'category-bottom' => [
            'name' => 'category-bottom',
            'label' => 'বিজ্ঞাপন',
            'desktop_size' => '728x90',
            'mobile_size' => '320x100',
            'image_url' => null,
            'target_url' => null,
            'html_code' => null,
            'is_active' => true,
        ],
        'sidebar-rectangle-1' => [
            'name' => 'sidebar-rectangle-1',
            'label' => 'বিজ্ঞাপন',
            'desktop_size' => '300x250',
            'mobile_size' => '300x250',
            'image_url' => null,
            'target_url' => null,
            'html_code' => null,
            'is_active' => true,
        ],
        'sidebar-half-page' => [
            'name' => 'sidebar-half-page',
            'label' => 'বিজ্ঞাপন',
            'desktop_size' => '300x600',
            'mobile_size' => '300x250',
            'image_url' => null,
            'target_url' => null,
            'html_code' => null,
            'is_active' => true,
        ],
        'sidebar-rectangle-2' => [
            'name' => 'sidebar-rectangle-2',
            'label' => 'বিজ্ঞাপন',
            'desktop_size' => '300x250',
            'mobile_size' => '300x250',
            'image_url' => null,
            'target_url' => null,
            'html_code' => null,
            'is_active' => true,
        ],
    ],
];
