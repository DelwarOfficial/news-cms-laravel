<?php

namespace App\Services;

use App\Support\ArticleFeed;
use App\Support\FallbackDataService;
use App\Support\FrontendCache;
use Illuminate\Support\Facades\Cache;

class TickerHeadlineService
{
    public function get(int $limit = 10): array
    {
        $limit = max(1, $limit);

        return FrontendCache::remember(
            [FrontendCache::TAG_CONTENT, FrontendCache::TAG_TICKER],
            $this->cacheKey($limit),
            max(600, min(3600, (int) config('homepage.cache.ttl', 600))),
            fn () => ArticleFeed::breakingNews(FallbackDataService::getArticles(), $limit),
        );
    }

    public static function forget(int $limit = 10): void
    {
        Cache::forget((new self())->cacheKey($limit));
        FrontendCache::flushTags([FrontendCache::TAG_CONTENT, FrontendCache::TAG_TICKER]);
    }

    private function cacheKey(int $limit): string
    {
        return "layout:ticker-headlines:v2:{$limit}:" . app()->getLocale();
    }
}
