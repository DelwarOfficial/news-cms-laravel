<?php

namespace App\Services;

use App\Models\ContentPlacement;
use App\Models\Post;
use App\Models\Division;
use App\Http\Resources\Api\V1\PostResource;
use App\Support\CacheLock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HomepageDataService
{
    private const CACHE_TTL = 300;
    private const CACHE_KEY = 'api:v1:homepage';

    private static array $postResourceCache = [];

    public function get(array $sections, ?string $locale = null): array
    {
        return CacheLock::rememberWithStale(
            self::CACHE_KEY . ':' . ($locale ?? 'bn'),
            self::CACHE_TTL,
            fn () => $this->build($sections, $locale),
        );
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY . ':bn');
        Cache::forget(self::CACHE_KEY . ':en');
    }

    private function build(array $sections, ?string $locale): array
    {
        $hero = $sections['hero'] ?? [];
        $placements = $hero['placements'] ?? [];

        $breakingStories = $this->placementPosts('home.breaking', 10);
        $usedIds = $this->collectIds($breakingStories);

        $featured = $this->placementPosts('home.featured', 1, $usedIds);
        $usedIds = $this->mergeIds($usedIds, $featured);

        $centerGrid = $this->placementPosts('home.sticky', 6, $usedIds);
        $usedIds = $this->mergeIds($usedIds, $centerGrid);

        $leftCol = $this->placementPosts('home.trending', 5, $usedIds);
        $usedIds = $this->mergeIds($usedIds, $leftCol);

        $rightCol = $this->placementPosts('home.editors_pick', 3, $usedIds);

        $categoryFeeds = $sections['category_feeds'] ?? [];
        $sectionData = [];
        foreach ($categoryFeeds as $key => $def) {
            $slugs = $def['slugs'] ?? [];
            $limit = (int) ($def['limit'] ?? 4);
            $sectionData[$key] = $this->categoryPosts($slugs, $limit);
        }

        $localNewsSection = $sections['local_news'] ?? [];
        $localNewsLimit = (int) ($localNewsSection['limit'] ?? 9);
        $localNews = $this->localNewsPosts($localNewsLimit);

        $videoPosts = $this->videoPosts(10);
        $photocardPosts = $this->photocardPosts(20);

        $divisions = $this->getDivisions();

        return [
            'breaking_stories' => $breakingStories,
            'featured' => $featured[0] ?? null,
            'center_grid' => $centerGrid,
            'left_column' => $leftCol,
            'right_column' => $rightCol,
            'category_feeds' => $sectionData,
            'local_news' => $localNews,
            'video_posts' => $videoPosts,
            'photocard_posts' => $photocardPosts,
            'saradesh_divisions' => $divisions,
        ];
    }

    public function getPhotoStoryData(?string $locale = null): array
    {
        $carousel = $this->photocardPosts(10, $locale);
        $popular = $this->popularPosts(8, $locale);

        $latest = collect($carousel)->take(8)->map(fn ($a) => [
            'id' => $a['id'] ?? null,
            'headline' => $a['title'] ?? '',
            'slug' => $a['slug'] ?? '',
            'shoulder' => $a['shoulder'] ?? null,
            'image_url' => $a['image_url'] ?? null,
            'timestamp' => $a['time_ago'] ?? '',
        ])->values()->all();

        $popularItems = collect($popular)->map(fn ($a) => [
            'id' => $a['id'] ?? null,
            'headline' => $a['title'] ?? '',
            'slug' => $a['slug'] ?? '',
            'shoulder' => $a['shoulder'] ?? null,
            'image_url' => $a['image_url'] ?? null,
            'timestamp' => $a['time_ago'] ?? '',
        ])->values()->all();

        return [
            'carousel' => $carousel,
            'latest' => $latest,
            'popular' => $popularItems,
        ];
    }

    public function getTicker(int $limit = 10, ?string $locale = null): array
    {
        return CacheLock::remember(
            'api:v1:ticker:' . $limit,
            120,
            function () use ($limit) {
                return PostResource::collection(
                    Post::withContentRelations()
                        ->published()
                        ->where('is_breaking', true)
                        ->latest('published_at')
                        ->latest('id')
                        ->take($limit)
                        ->get()
                )->toArray(request());
            }
        );
    }

    public function getRelated(int $postId, int $limit = 4, ?string $locale = null): array
    {
        $post = Post::withContentRelations()->find($postId);

        if (! $post) {
            return [];
        }

        $categorySlug = $post->primaryCategory?->slug ?? $post->categories->first()?->slug;

        if (! $categorySlug) {
            return [];
        }

        $related = Post::withContentRelations()
            ->published()
            ->where('id', '!=', $postId)
            ->where(function ($q) use ($categorySlug) {
                $q->whereHas('categories', fn ($cq) => $cq->where('slug', $categorySlug))
                  ->orWhereHas('primaryCategory', fn ($cq) => $cq->where('slug', $categorySlug));
            })
            ->latest('published_at')
            ->latest('id')
            ->take($limit)
            ->get();

        return PostResource::collection($related)->toArray(request());
    }

    public function getCategories(): array
    {
        return CacheLock::remember('api:v1:categories:tree', 300, function () {
            $all = \App\Models\Category::withCount('posts')
                ->where('status', 'active')
                ->orderBy('order')
                ->orderBy('name')
                ->get();

            $parents = $all->whereNull('parent_id')->values();
            $children = $all->whereNotNull('parent_id')->groupBy('parent_id');

            return $parents->map(function ($parent) use ($children) {
                return [
                    'name_bn' => $parent->name_bn ?? $parent->name,
                    'name_en' => $parent->name_en,
                    'slug' => $parent->slug,
                    'children' => collect($children->get($parent->id, []))->map(fn ($c) => [
                        'name_bn' => $c->name_bn ?? $c->name,
                        'name_en' => $c->name_en,
                        'slug' => $c->slug,
                    ])->values()->all(),
                ];
            })->values()->all();
        });
    }

    private function placementPosts(string $key, int $limit, array $exceptIds = []): array
    {
        try {
            $posts = ContentPlacement::query()
                ->active()
                ->where('placement_key', $key)
                ->whereHas('post', fn ($q) => $q->published())
                ->with(['post' => fn ($q) => $q->withContentRelations()])
                ->when($exceptIds !== [], fn ($q) => $q->whereNotIn('post_id', $exceptIds))
                ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
                ->orderBy('sort_order')
                ->latest('updated_at')
                ->take($limit)
                ->get()
                ->pluck('post')
                ->filter();

            return $this->resourceCollection($posts);
        } catch (\Throwable) {
            return [];
        }
    }

    private function categoryPosts(array $slugs, int $limit): array
    {
        try {
            $posts = Post::withContentRelations()
                ->published()
                ->where(function ($q) use ($slugs) {
                    $q->whereHas('categories', fn ($cq) => $cq->whereIn('slug', $slugs))
                      ->orWhereHas('primaryCategory', fn ($cq) => $cq->whereIn('slug', $slugs));
                })
                ->latest('published_at')
                ->latest('id')
                ->take($limit)
                ->get();

            return $this->resourceCollection($posts);
        } catch (\Throwable) {
            return [];
        }
    }

    private function localNewsPosts(int $limit): array
    {
        try {
            $posts = Post::withContentRelations()
                ->published()
                ->whereNotNull('division_id')
                ->whereNotNull('district_id')
                ->whereNotNull('upazila_id')
                ->latest('published_at')
                ->latest('id')
                ->take($limit)
                ->get();

            return $this->resourceCollection($posts);
        } catch (\Throwable) {
            return [];
        }
    }

    private function videoPosts(int $limit): array
    {
        try {
            $posts = Post::withContentRelations()
                ->published()
                ->where('post_format', 'video')
                ->latest('published_at')
                ->latest('id')
                ->take($limit)
                ->get();

            return $this->resourceCollection($posts);
        } catch (\Throwable) {
            return [];
        }
    }

    private function photocardPosts(int $limit, ?string $locale = null): array
    {
        try {
            $posts = Post::withContentRelations()
                ->published()
                ->where('is_photocard', true)
                ->latest('published_at')
                ->latest('id')
                ->take($limit)
                ->get();

            return $this->resourceCollection($posts);
        } catch (\Throwable) {
            return [];
        }
    }

    private function popularPosts(int $limit, ?string $locale = null): array
    {
        try {
            $posts = Post::withContentRelations()
                ->published()
                ->where('view_count', '>', 0)
                ->orderByDesc('view_count')
                ->latest('published_at')
                ->latest('id')
                ->take($limit)
                ->get();

            return $this->resourceCollection($posts);
        } catch (\Throwable) {
            return [];
        }
    }

    private function getDivisions(): array
    {
        try {
            return Division::query()
                ->orderBy('name')
                ->get()
                ->map(fn ($d) => [
                    'name' => $d->name,
                    'name_bn' => $d->name_bangla ?? $d->name_bn ?? $d->name,
                    'slug' => $d->slug,
                ])
                ->values()
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }

    private function resourceCollection(Collection $posts): array
    {
        return $posts->map(fn (Post $post) => $this->toArticleArray($post))->values()->all();
    }

    private function toArticleArray(Post $post): array
    {
        $cacheKey = $post->id;
        if (isset(self::$postResourceCache[$cacheKey])) {
            return self::$postResourceCache[$cacheKey];
        }

        $locale = app()->getLocale();

        $imageUrl = null;
        if ($post->relationLoaded('featuredMedia') && $post->featuredMedia) {
            $imageUrl = $post->featuredMedia->file_url ?: $post->featuredMedia->url;
        }
        $imageUrl ??= $post->featured_image
            ? url('storage/' . ltrim($post->featured_image, '/'))
            : null;

        $category = $this->resolveCategory($post);
        $publishedAt = $post->published_at;

        $result = [
            'id' => $post->id,
            'slug' => $post->slug,
            'title' => $post->titleForLocale($locale),
            'title_bn' => $post->title_bn,
            'title_en' => $post->title_en,
            'shoulder' => $post->shoulder,
            'category_name_bn' => $category['name_bn'] ?? 'অন্যান্য খবর',
            'category_slug' => $category['slug'] ?? 'others-news',
            'excerpt' => $post->excerpt ?? Str::limit(strip_tags((string) ($post->content ?: $post->body)), 170),
            'author_name' => $post->bylineAuthor?->name ?? $post->author?->name ?? 'ঢাকা ম্যাগাজিন ডেস্ক',
            'published_at' => $publishedAt?->toIso8601String(),
            'image_url' => $imageUrl,
            'view_count' => (int) ($post->view_count ?? 0),
            'is_photocard' => (bool) ($post->is_photocard ?? false),
            'tags' => $post->relationLoaded('tags')
                ? $post->tags->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])->values()->all()
                : [],
            'post_format' => $post->post_format ?? 'standard',
        ];

        self::$postResourceCache[$cacheKey] = $result;

        return $result;
    }

    private function resolveCategory(Post $post): array
    {
        $primary = $post->relationLoaded('primaryCategory') ? $post->primaryCategory : null;
        $categories = $post->relationLoaded('categories') ? $post->categories : collect();

        if ($primary) {
            return [
                'name_bn' => $primary->name_bn ?? $primary->name,
                'slug' => $primary->slug,
            ];
        }

        if ($categories->isNotEmpty()) {
            $cat = $categories->firstWhere('pivot.is_primary', true) ?? $categories->first();
            return [
                'name_bn' => $cat->name_bn ?? $cat->name,
                'slug' => $cat->slug,
            ];
        }

        return [
            'name_bn' => 'অন্যান্য খবর',
            'slug' => 'others-news',
        ];
    }

    private function collectIds(array $articles): array
    {
        return array_values(array_unique(array_filter(array_map(fn ($a) => (int) ($a['id'] ?? 0), $articles))));
    }

    private function mergeIds(array $existing, array $articles): array
    {
        return array_values(array_unique(array_merge($existing, $this->collectIds($articles))));
    }
}
