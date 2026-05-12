<?php

return [
    'name' => env('APP_NAME', 'Dhaka Magazine'),
    'version' => '1.0.0',
    'description' => 'Unified NewsCore CMS and Dhaka Magazine frontend application',

    'posts_per_page' => 12,
    'enable_comments' => true,
    'enable_registration' => false,

    'seo' => [
        'default_meta_title' => 'Dhaka Magazine - Latest News & Breaking Stories',
        'default_meta_description' => 'Stay updated with the latest Bangladesh and global news, analysis, features, and local coverage.',
    ],

    'routes' => [
        'admin_prefix' => 'admin',
        'article_prefix' => 'article',
        'legacy_post_prefix' => 'post',
        'category_prefix' => 'category',
        'enable_root_article_slugs' => true,
        'view_increment_route' => 'posts.view',
    ],
];
