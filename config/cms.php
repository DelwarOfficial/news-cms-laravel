<?php

return [
    'name' => env('APP_NAME', 'NewsCore'),
    'version' => '1.0.0',
    'description' => 'Professional News Content Management System',

    'posts_per_page' => 12,
    'enable_comments' => true,
    'enable_registration' => false,

    'seo' => [
        'default_meta_title' => 'NewsCore - Latest News & Breaking Stories',
        'default_meta_description' => 'Stay updated with the latest breaking news, trending stories, and in-depth analysis.',
    ],
];