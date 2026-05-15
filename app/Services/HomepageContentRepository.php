<?php

namespace App\Services;

use App\Models\ContentPlacement;
use App\Models\Post;
use App\Support\ArticleFeed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class HomepageContentRepository
{
    private ?bool $placementsReady = null;
    private array $categoryCache = [];
    private array $relationshipCategoryCache = [];

    public function latest(array $fallbackArticles, int $limit = 40): array
    {
        return ArticleFeed::homepageArticles($fallbackArticles, $limit);
    }

    public function placement(string $placementKey, ?string $legacySection, array $fallbackArticles, int $limit, array $exceptIds = []): array
    {
        $placementArticles = $this->placementPosts($placementKey, $limit, $exceptIds)
            ->map(fn (Post $post) => ArticleFeed::postToArticleArray($post))
            ->values()
            ->all();

        if ($placementArticles !== []) {
            return $placementArticles;
        }

        return match ($legacySection) {
            'breaking' => ArticleFeed::breakingNews($fallbackArticles, $limit, $exceptIds),
            'featured' => ArticleFeed::featured($fallbackArticles, $limit, $exceptIds),
            'sticky' => ArticleFeed::sticky($fallbackArticles, $limit, $exceptIds),
            'trending' => ArticleFeed::trending($fallbackArticles, $limit, $exceptIds),
            'editors_pick' => ArticleFeed::editorsPick($fallbackArticles, $limit, $exceptIds),
            default => [],
        };
    }

    public function category(array $slugs, array $fallbackArticles, int $limit): array
    {
        $cacheKey = $this->feedCacheKey($slugs, $limit);

        return $this->categoryCache[$cacheKey]
            ??= ArticleFeed::categoryArticles($slugs, $fallbackArticles, $limit);
    }

    public function relationshipCategory(array $slugs, int $limit, array $fallbackArticles = []): array
    {
        $cacheKey = $this->feedCacheKey($slugs, $limit);

        if (array_key_exists($cacheKey, $this->relationshipCategoryCache)) {
            return $this->relationshipCategoryCache[$cacheKey];
        }

        $articles = ArticleFeed::categoryRelationshipArticles($slugs, $limit);

        if ($articles !== []) {
            return $this->relationshipCategoryCache[$cacheKey] = $articles;
        }

        return $this->relationshipCategoryCache[$cacheKey] = collect($fallbackArticles)
            ->whereIn('category_slug', $slugs)
            ->take($limit)
            ->values()
            ->all();
    }

    public function localNews(array $fallbackArticles, int $limit): array
    {
        return ArticleFeed::localNews($fallbackArticles, $limit);
    }

    public function videoPosts(int $limit = 10): array
    {
        if (! $this->photocardReady()) {
            return [];
        }

        try {
            return Post::query()
                ->withContentRelations()
                ->published()
                ->where('post_format', 'video')
                ->latest('published_at')
                ->latest('id')
                ->take(max(1, $limit))
                ->get()
                ->map(fn (Post $post) => ArticleFeed::postToArticleArray($post))
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public function photocard(int $limit = 20): array
    {
        if (! $this->photocardReady()) {
            return [];
        }

        try {
            return Post::query()
                ->withContentRelations()
                ->published()
                ->photocard()
                ->latest('published_at')
                ->latest('id')
                ->take(max(1, $limit))
                ->get()
                ->map(fn (Post $post) => ArticleFeed::postToArticleArray($post))
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function placementPosts(string $placementKey, int $limit, array $exceptIds = []): Collection
    {
        if (! $this->placementsReady()) {
            return collect();
        }

        try {
            return ContentPlacement::query()
                ->active()
                ->where('placement_key', $placementKey)
                ->whereHas('post', fn (Builder $query) => $query->published())
                ->with(['post' => fn ($query) => $query->withContentRelations()])
                ->when($exceptIds !== [], fn (Builder $query) => $query->whereNotIn('post_id', array_values(array_unique($exceptIds))))
                ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('sort_order')
                ->latest('updated_at')
                ->take($limit)
                ->get()
                ->pluck('post')
                ->filter();
        } catch (\Throwable) {
            return collect();
        }
    }

    private function placementsReady(): bool
    {
        if ($this->placementsReady !== null) {
            return $this->placementsReady;
        }

        try {
            return $this->placementsReady = class_exists(ContentPlacement::class)
                && class_exists(Post::class)
                && Schema::hasTable('content_placements')
                && Schema::hasTable('posts');
        } catch (\Throwable) {
            return $this->placementsReady = false;
        }
    }

    private function photocardReady(): bool
    {
        try {
            return class_exists(Post::class)
                && Schema::hasTable('posts')
                && Schema::hasColumn('posts', 'is_photocard');
        } catch (\Throwable) {
            return false;
        }
    }

    private function feedCacheKey(array $slugs, int $limit): string
    {
        $slugs = array_values(array_unique(array_filter($slugs)));
        sort($slugs);

        return md5(json_encode([$slugs, $limit]));
    }
}
