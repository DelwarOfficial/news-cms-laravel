<?php

namespace App\Services;

use App\Models\Post;
use App\Support\ArticleFeed;
use App\Support\FallbackDataService;
use App\Support\SchemaReadyCheck;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PopularNewsService
{
    public function get(int $limit = 5, array $exceptIds = []): array
    {
        $limit = max(1, $limit);

        if ($exceptIds !== []) {
            return $this->build($limit, $exceptIds);
        }

        return Cache::remember(
            $this->cacheKey($limit),
            now()->addSeconds((int) config('homepage.cache.ttl', 300)),
            fn () => $this->build($limit),
        );
    }

    public static function forget(int $limit = 5): void
    {
        Cache::forget((new self())->cacheKey($limit));
    }

    private function build(int $limit, array $exceptIds = []): array
    {
        $posts = $this->databasePopular($limit, $exceptIds);

        if ($posts !== []) {
            return $posts;
        }

        $fallback = ArticleFeed::homepageArticles(FallbackDataService::getArticles(), max(40, $limit + 5));

        $byViews = collect($fallback)
            ->reject(fn (array $article) => $this->articleIdExcluded($article, $exceptIds))
            ->filter(fn (array $article) => isset($article['views']) && (int) $article['views'] > 0)
            ->sortByDesc(fn (array $article) => (int) $article['views'])
            ->take($limit)
            ->values()
            ->all();

        if ($byViews !== []) {
            return $byViews;
        }

        return collect($fallback)
            ->reject(fn (array $article) => $this->articleIdExcluded($article, $exceptIds))
            ->take($limit)
            ->values()
            ->all();
    }

    private function databasePopular(int $limit, array $exceptIds): array
    {
        if (! SchemaReadyCheck::isPostsTableReady()) {
            return [];
        }

        try {
            return Post::query()
                ->withContentRelations()
                ->published()
                ->when($exceptIds !== [], fn ($query) => $query->whereNotIn('id', array_values(array_unique($exceptIds))))
                ->where('view_count', '>', 0)
                ->orderByDesc('view_count')
                ->latest('published_at')
                ->latest('id')
                ->take($limit)
                ->get()
                ->map(fn (Post $post) => ArticleFeed::postToArticleArray($post))
                ->values()
                ->all();
        } catch (\Throwable $exception) {
            Log::warning('Failed to load popular news from database.', [
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    private function cacheKey(int $limit): string
    {
        return "content:popular-news:v1:{$limit}";
    }

    private function articleIdExcluded(array $article, array $exceptIds): bool
    {
        return isset($article['id']) && in_array((int) $article['id'], array_map('intval', $exceptIds), true);
    }
}
