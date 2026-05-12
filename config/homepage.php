<?php

return [
    'cache' => [
        'enabled' => env('HOMEPAGE_CACHE_ENABLED', true),
        'ttl' => (int) env('HOMEPAGE_CACHE_TTL', 300),
        'key' => 'homepage:v1',
    ],

    'demo_fallback' => [
        'enabled' => env('ENABLE_FALLBACK_CONTENT', env('DEMO_FALLBACK_ENABLED', false)),
    ],

    /*
    |--------------------------------------------------------------------------
    | Homepage content sources
    |--------------------------------------------------------------------------
    |
    | These definitions are the CMS-facing contract for the current Blade UI.
    | A future admin panel can persist the same shape in a database table
    | without changing the templates.
    |
    */
    'sections' => [
        'hero' => [
            'placements' => [
                'breaking' => ['key' => 'home.breaking', 'legacy' => 'breaking', 'limit' => 10],
                'featured' => ['key' => 'home.featured', 'legacy' => 'featured', 'limit' => 1],
                'center_grid' => ['key' => 'home.sticky', 'legacy' => 'sticky', 'limit' => 6],
                'left_column' => ['key' => 'home.trending', 'legacy' => 'trending', 'limit' => 5],
                'right_column' => ['key' => 'home.editors_pick', 'legacy' => 'editors_pick', 'limit' => 3],
            ],
        ],

        'category_feeds' => [
            'bangladesh' => [
                'source' => 'category',
                'slugs' => ['bangladesh', 'national', 'dhaka', 'crime', 'accidents', 'law-justice', 'politics'],
                'limit' => 4,
            ],
            'politics' => [
                'source' => 'category',
                'slugs' => ['politics'],
                'limit' => 7,
            ],
            'world' => [
                'source' => 'relationship-category',
                'slugs' => ['world'],
                'limit' => 6,
            ],
            'sports' => [
                'source' => 'relationship-category',
                'slugs' => ['sports', 'football', 'cricket', 'other-sports'],
                'limit' => 4,
            ],
            'opinion' => [
                'source' => 'category',
                'slugs' => ['opinion'],
                'limit' => 4,
            ],
            'videos' => [
                'source' => 'category',
                'slugs' => ['videos'],
                'limit' => 4,
            ],
            'entertainment' => [
                'source' => 'relationship-category',
                'slugs' => ['entertainment'],
                'limit' => 7,
            ],
            'economy' => [
                'source' => 'category',
                'slugs' => ['economy', 'stock-market', 'banking-insurance', 'industry', 'agriculture'],
                'limit' => 4,
            ],
            'lifestyle' => [
                'source' => 'category',
                'slugs' => ['lifestyle', 'health', 'beauty', 'recipes'],
                'limit' => 4,
            ],
            'jobs' => [
                'source' => 'category',
                'slugs' => ['jobs', 'government-jobs', 'private-jobs'],
                'limit' => 4,
            ],
            'special' => [
                'source' => 'category',
                'slugs' => ['dhaka-magazine-special'],
                'limit' => 5,
            ],
            'religion' => [
                'source' => 'category',
                'slugs' => ['religion'],
                'limit' => 4,
            ],
            'dhaka' => [
                'source' => 'category',
                'slugs' => ['dhaka'],
                'limit' => 4,
            ],
            'education' => [
                'source' => 'category',
                'slugs' => ['education'],
                'limit' => 4,
            ],
            'expatriates' => [
                'source' => 'category',
                'slugs' => ['expatriates'],
                'limit' => 4,
            ],
        ],

        'local_news' => [
            'source' => 'location',
            'limit' => 9,
        ],

        'sports_subcategories' => [
            ['slugs' => ['cricket'], 'label' => 'ক্রিকেট'],
            ['slugs' => ['other-sports'], 'label' => 'অন্যান্য খেলা'],
            ['slugs' => ['football'], 'label' => 'ফুটবল'],
            ['slugs' => ['sports'], 'label' => 'আজকের খেলা'],
        ],
    ],
];
