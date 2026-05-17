<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class FrontendCache
{
    public const TAG_CONTENT = 'content';
    public const TAG_HOMEPAGE = 'homepage';
    public const TAG_POPULAR = 'popular-news';
    public const TAG_TICKER = 'ticker-headlines';
    public const TAG_CATEGORY_FEEDS = 'category-feeds';

    public const HOMEPAGE_KEYS = [
        'homepage:v1:bn',
        'homepage:v1:en',
        'homepage:v1',
    ];

    public const TICKER_KEYS = [
        'layout:ticker-headlines:v2:bn:10',
        'layout:ticker-headlines:v2:en:10',
        'layout:ticker-headlines:v2:bn',
        'layout:ticker-headlines:v2:en',
        'layout:ticker-headlines:v1',
    ];

    public const POPULAR_KEYS = [
        'content:popular-news:v1:bn:5',
        'content:popular-news:v1:en:5',
        'content:popular-news:v1:bn',
        'content:popular-news:v1:en',
        'content:popular-news:v1',
        'layout:popular-news:v1:5',
        'layout:popular-news:v1',
    ];

    public const CATEGORY_KEYS = [
        'layout:site-categories:v1',
        'layout:site-categories:v2',
    ];

    public const SCHEMA_KEYS = [
        'schema_posts_table_ready',
        'schema_location_columns_ready',
        'schema_local_news_columns_ready',
        'schema_category_relationship_ready',
        'schema_location_id_columns_ready',
    ];

    public const LOCATION_KEYS = [
        'bangladesh_location_data',
        'upazila_bn_map',
    ];

    public const AD_SLOT_KEYS = [
        'ads:slot:category-top',
        'ads:slot:category-in-feed',
        'ads:slot:category-bottom',
        'ads:slot:sidebar-rectangle-1',
        'ads:slot:sidebar-half-page',
        'ads:slot:sidebar-rectangle-2',
        'ads:slot:header',
        'ads:slot:sidebar',
        'ads:slot:content_top',
        'ads:slot:content_bottom',
        'ads:slot:footer',
    ];

    public static function flushContent(): void
    {
        self::flushTags([self::TAG_CONTENT]);

        self::forget([
            config('homepage.cache.key', 'homepage:v1'),
            ...self::HOMEPAGE_KEYS,
            ...self::TICKER_KEYS,
            ...self::POPULAR_KEYS,
        ]);
    }

    public static function flushCategories(): void
    {
        self::flushTags([self::TAG_CONTENT, self::TAG_CATEGORY_FEEDS]);

        self::forget([
            ...self::CATEGORY_KEYS,
        ]);

        self::flushContent();
    }

    public static function flushLocations(): void
    {
        self::forget([
            ...self::LOCATION_KEYS,
        ]);

        self::flushContent();
    }

    public static function flushAds(): void
    {
        self::forget(self::AD_SLOT_KEYS);
        self::flushContent();
    }

    public static function flushSchemaReadiness(): void
    {
        self::forget(self::SCHEMA_KEYS);
    }

    public static function forget(array $keys): void
    {
        foreach (array_values(array_unique(array_filter($keys))) as $key) {
            Cache::forget($key);
        }
    }

    public static function flushTags(array $tags): void
    {
        try {
            Cache::tags($tags)->flush();
        } catch (\Throwable) {
            // Tagged cache requires Redis or Memcached. Key fallback remains below.
        }
    }

    public static function remember(array $tags, string $key, int $ttl, callable $callback): mixed
    {
        try {
            return Cache::tags($tags)->remember($key, $ttl, $callback);
        } catch (\Throwable) {
            return Cache::remember($key, $ttl, $callback);
        }
    }
}
