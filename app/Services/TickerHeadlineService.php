<?php

namespace App\Services;

use App\Support\ArticleFeed;
use App\Support\FallbackDataService;
use Illuminate\Support\Facades\Cache;

class TickerHeadlineService
{
    public function get(int $limit = 10): array
    {
        $limit = max(1, $limit);

        return Cache::remember(
            $this->cacheKey($limit),
            now()->addSeconds((int) config('homepage.cache.ttl', 300)),
            fn () => ArticleFeed::breakingNews(FallbackDataService::getArticles(), $limit),
        );
    }

    public static function forget(int $limit = 10): void
    {
        Cache::forget((new self())->cacheKey($limit));
    }

    private function cacheKey(int $limit): string
    {
        return "layout:ticker-headlines:v2:{$limit}";
    }
}
