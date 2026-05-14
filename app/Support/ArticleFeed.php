<?php

namespace App\Support;

use App\Helpers\DateHelper;
use App\Models\ContentPlacement;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ArticleFeed
{
    private static array $publicPostCache = [];
    private static array $locationIdCache = [];

    private const SECTION_META = [
        'breaking' => ['placement' => 'home.breaking', 'flag' => 'is_breaking', 'order' => 'breaking_news_order', 'scope' => 'breaking'],
        'featured' => ['placement' => 'home.featured', 'flag' => 'is_featured', 'order' => 'featured_order', 'scope' => 'featured'],
        'sticky' => ['placement' => 'home.sticky', 'flag' => 'is_sticky', 'order' => 'sticky_order', 'scope' => 'sticky'],
        'trending' => ['placement' => 'home.trending', 'flag' => 'is_trending', 'order' => 'trending_order', 'scope' => 'trending'],
        'editors_pick' => ['placement' => 'home.editors_pick', 'flag' => 'is_editors_pick', 'order' => 'editors_pick_order', 'scope' => 'editorsPick'],
    ];

    public static function homepageArticles(?array $fallbackArticles = null, int $limit = 40): array
    {
        $fallbackArticles ??= FallbackDataService::getArticles();

        $posts = self::publicPosts($limit)
            ->map(fn (Post $post) => self::postToArticleArray($post))
            ->values();

        return $posts->isNotEmpty()
            ? $posts->all()
            : collect($fallbackArticles)->take($limit)->values()->all();
    }

    public static function breakingNews(?array $fallbackArticles = null, int $limit = 10, array $exceptIds = []): array
    {
        return self::homepageSection('breaking', $fallbackArticles, $limit, $exceptIds);
    }

    public static function featured(?array $fallbackArticles = null, int $limit = 1, array $exceptIds = []): array
    {
        return self::homepageSection('featured', $fallbackArticles, $limit, $exceptIds);
    }

    public static function sticky(?array $fallbackArticles = null, int $limit = 6, array $exceptIds = []): array
    {
        return self::homepageSection('sticky', $fallbackArticles, $limit, $exceptIds);
    }

    public static function trending(?array $fallbackArticles = null, int $limit = 5, array $exceptIds = []): array
    {
        return self::homepageSection('trending', $fallbackArticles, $limit, $exceptIds);
    }

    public static function editorsPick(?array $fallbackArticles = null, int $limit = 3, array $exceptIds = []): array
    {
        return self::homepageSection('editors_pick', $fallbackArticles, $limit, $exceptIds);
    }

    public static function localNews(?array $fallbackArticles = null, int $limit = 9): array
    {
        $fallbackArticles ??= FallbackDataService::getArticles();

        $posts = self::localNewsPosts($limit)
            ->map(fn (Post $post) => self::postToArticleArray($post))
            ->unique('id')
            ->values();

        if ($posts->isNotEmpty()) {
            return $posts->all();
        }

        if (SchemaReadyCheck::hasLocalNewsColumns()) {
            return [];
        }

        return self::legacyLocalNewsSection(self::homepageArticles($fallbackArticles))
            ->take($limit)
            ->values()
            ->all();
    }

    public static function categoryArticles(array $categorySlugs, ?array $fallbackArticles = null, int $limit = 30, ?string $division = null, ?string $district = null, ?string $upazila = null): array
    {
        $fallbackArticles ??= FallbackDataService::getArticles();

        $posts = self::categoryPosts($categorySlugs, $limit, $division, $district, $upazila)
            ->map(fn (Post $post) => self::postToArticleArray($post))
            ->values();

        if (self::hasLocationFilter($division, $district, $upazila)) {
            return $posts->all();
        }

        return $posts->isNotEmpty()
            ? $posts->all()
            : collect($fallbackArticles)->whereIn('category_slug', $categorySlugs)->take($limit)->values()->all();
    }

    public static function categoryRelationshipArticles(array $categorySlugs, int $limit = 30, ?string $division = null, ?string $district = null, ?string $upazila = null): array
    {
        return self::categoryPosts($categorySlugs, $limit, $division, $district, $upazila, true)
            ->map(fn (Post $post) => self::postToArticleArray($post))
            ->values()
            ->all();
    }

    public static function findArticle(string $slug, ?array $fallbackArticles = null): ?array
    {
        $fallbackArticles ??= FallbackDataService::getArticles();

        if (! SchemaReadyCheck::isPostsTableReady()) {
            return collect($fallbackArticles)->firstWhere('slug', $slug);
        }

        try {
            $post = Post::query()
                ->withContentRelations()
                ->published()
                ->where(function (Builder $query) use ($slug) {
                    $query->where('slug', $slug);

                    foreach (['slug_en', 'slug_bn'] as $column) {
                        if (self::postHasColumn($column)) {
                            $query->orWhere($column, $slug);
                        }
                    }
                })
                ->first();
        } catch (\Throwable $exception) {
            Log::error("Failed to find article [{$slug}]: ".$exception->getMessage());
            $post = null;
        }

        return $post
            ? self::postToArticleArray($post, true)
            : collect($fallbackArticles)->firstWhere('slug', $slug);
    }

    public static function allForRelated(?array $fallbackArticles = null, int $limit = 80): array
    {
        return self::homepageArticles($fallbackArticles, $limit);
    }

    public static function postToArticleArray(Post $post, bool $includeBody = false): array
    {
        $category = PostCategoryResolver::categoryFor($post);
        $publishedAt = $post->published_at ?: $post->created_at ?: now();
        $title = self::postTitle($post);
        $excerpt = self::postExcerpt($post);
        $bodyText = self::postBodyText($post);
        $imageUrl = self::resolveImageUrl($post);

        $article = [
            'id' => $post->id,
            'slug' => $post->slug,
            'url' => route('article.id_slug', ['postId' => $post->id, 'slug' => $post->slug]),
            'canonical_url' => route('article.id', $post->id),
            'title' => $title,
            'headline' => $title,
            'shoulder' => $post->shoulder,
            'category' => $category['name_bn'] ?? PostCategoryResolver::fallbackCategory()['name_bn'],
            'category_slug' => $category['slug'] ?? PostCategoryResolver::FALLBACK_SLUG,
            'category_url' => PostCategoryResolver::categoryRoute($category),
            'excerpt' => $excerpt,
            'author' => $post->bylineAuthor?->name ?: $post->author?->name ?: ($post->source_name ?: 'ঢাকা ম্যাগাজিন ডেস্ক'),
            'date' => DateHelper::getBengaliDate($publishedAt),
            'show_author' => (bool) ($post->show_author ?? true),
            'show_publish_date' => (bool) ($post->show_publish_date ?? true),
            'allow_comments' => (bool) ($post->allow_comments ?? true),
            'time_ago' => DateHelper::timeAgo($publishedAt),
            'timestamp' => DateHelper::timeAgo($publishedAt),
            'published_at' => $publishedAt,
            'updated_at' => $post->updated_at,
            'image_url' => $imageUrl,
            'image_alt' => $post->featured_image_alt ?: $title,
            'image_caption' => $post->featured_image_caption,
            'views' => (int) ($post->view_count ?? 0),
            'reading_time' => (int) ($post->reading_time ?? 1),
            'comment_count' => (int) ($post->comment_count ?? 0),
            'is_breaking' => (bool) ($post->is_breaking ?? false),
            'is_featured' => (bool) ($post->is_featured ?? false),
            'is_sticky' => (bool) ($post->is_sticky ?? false),
            'is_photocard' => (bool) ($post->is_photocard ?? false),
            'is_trending' => (bool) ($post->is_trending ?? false),
            'is_editors_pick' => (bool) ($post->is_editors_pick ?? false),
            'location' => self::locationLabel($post),
            'division' => $post->division?->name,
            'district' => $post->district?->name,
            'upazila' => $post->upazila?->name,
            'tags' => $post->relationLoaded('tags') ? $post->tags->pluck('name')->values()->all() : [],
            'meta_title' => $post->meta_title ?: $title,
            'meta_description' => $post->meta_description ?: Str::limit(strip_tags($excerpt), 155, ''),
            'stored_canonical_url' => $post->canonical_url,
            'og_image' => $post->og_image ?: $imageUrl,
        ];

        if ($includeBody) {
            $article['body_html'] = $post->bodyHtmlForLocale(app()->getLocale());
            $article['body'] = collect(preg_split('/\R{2,}/', $bodyText))
                ->map(fn (string $paragraph) => trim(strip_tags($paragraph)))
                ->filter()
                ->values()
                ->all();
        }

        return $article;
    }

    public static function resolveImageUrl(Post $post): string
    {
        if ($post->relationLoaded('featuredMedia') && $post->featuredMedia) {
            $mediaPath = $post->featuredMedia->file_url
                ?? $post->featuredMedia->file_path
                ?? $post->featuredMedia->path
                ?? null;

            if ($mediaPath) {
                return ImageResolver::imageUrl($mediaPath);
            }
        }

        foreach (['image_path', 'featured_image', 'og_image'] as $field) {
            if (! empty($post->{$field})) {
                return ImageResolver::imageUrl($post->{$field});
            }
        }

        return ImageResolver::placeholderImageUrl();
    }

    private static function publicPosts(int $limit): Collection
    {
        if (! SchemaReadyCheck::isPostsTableReady()) {
            return collect();
        }

        $cacheKey = "latest:{$limit}";

        if (array_key_exists($cacheKey, self::$publicPostCache)) {
            return self::$publicPostCache[$cacheKey];
        }

        try {
            return self::$publicPostCache[$cacheKey] = Post::query()
                ->withContentRelations()
                ->published()
                ->latest('published_at')
                ->latest('id')
                ->take(max(1, $limit))
                ->get();
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch public posts: '.$exception->getMessage());
            return self::$publicPostCache[$cacheKey] = collect();
        }
    }

    private static function homepageSection(string $section, ?array $fallbackArticles, int $limit, array $exceptIds): array
    {
        $fallbackArticles ??= FallbackDataService::getArticles();
        $meta = self::SECTION_META[$section] ?? null;

        if (! $meta) {
            return [];
        }

        $posts = self::placementPosts($meta['placement'], $limit, $exceptIds);

        if ($posts->isEmpty()) {
            $posts = self::flaggedSectionPosts($meta, $limit, $exceptIds);
        }

        $articles = $posts
            ->map(fn (Post $post) => self::postToArticleArray($post))
            ->values();

        if ($articles->isNotEmpty()) {
            return $articles->all();
        }

        return collect(self::legacyHomepageSection($section, self::homepageArticles($fallbackArticles), $fallbackArticles))
            ->reject(fn (array $article) => self::articleIdExcluded($article, $exceptIds))
            ->take($limit)
            ->values()
            ->all();
    }

    private static function placementPosts(string $placementKey, int $limit, array $exceptIds = []): Collection
    {
        if (! Schema::hasTable('content_placements')) {
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
                ->take(max(1, $limit))
                ->get()
                ->pluck('post')
                ->filter();
        } catch (\Throwable $exception) {
            Log::warning('Failed to fetch content placements.', [
                'placement_key' => $placementKey,
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    private static function flaggedSectionPosts(array $meta, int $limit, array $exceptIds = []): Collection
    {
        if (! self::postHasColumn($meta['flag'])) {
            return collect();
        }

        try {
            $query = Post::query()
                ->withContentRelations()
                ->published()
                ->where($meta['flag'], true)
                ->when($exceptIds !== [], fn (Builder $query) => $query->whereNotIn('id', array_values(array_unique($exceptIds))));

            if (! empty($meta['order']) && self::postHasColumn($meta['order'])) {
                $query->orderByRaw("CASE WHEN {$meta['order']} IS NULL THEN 1 ELSE 0 END")
                    ->orderBy($meta['order']);
            }

            return $query
                ->latest('published_at')
                ->latest('id')
                ->take(max(1, $limit))
                ->get();
        } catch (\Throwable $exception) {
            Log::warning('Failed to fetch flagged section posts.', [
                'flag' => $meta['flag'],
                'message' => $exception->getMessage(),
            ]);

            return collect();
        }
    }

    private static function categoryPosts(array $categorySlugs, int $limit, ?string $division = null, ?string $district = null, ?string $upazila = null, bool $relationshipOnly = false): Collection
    {
        if (! SchemaReadyCheck::isPostsTableReady()) {
            return collect();
        }

        try {
            if (self::hasLocationFilter($division, $district, $upazila) && ! SchemaReadyCheck::hasLocationColumns()) {
                return collect();
            }

            $query = Post::query()
                ->withContentRelations()
                ->published();

            self::applyCategoryFilter($query, $categorySlugs, $relationshipOnly);
            self::applyLocationFilter($query, $division, $district, $upazila);

            return $query
                ->latest('published_at')
                ->latest('id')
                ->take(max(1, $limit))
                ->get();
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch category posts: '.$exception->getMessage());
            return collect();
        }
    }

    private static function localNewsPosts(int $limit): Collection
    {
        if (! SchemaReadyCheck::hasLocalNewsColumns()) {
            return collect();
        }

        try {
            return Post::query()
                ->withContentRelations()
                ->published()
                ->whereNotNull('division_id')
                ->whereNotNull('district_id')
                ->latest('published_at')
                ->latest('id')
                ->take(max(1, $limit))
                ->get();
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch local news posts: '.$exception->getMessage());
            return collect();
        }
    }

    private static function applyCategoryFilter(Builder $query, array $categorySlugs, bool $relationshipOnly): void
    {
        $categorySlugs = array_values(array_filter($categorySlugs));

        if ($categorySlugs === []) {
            return;
        }

        if (SchemaReadyCheck::hasCategoryRelationship()) {
            $query->where(function (Builder $categoryQuery) use ($categorySlugs) {
                $categoryQuery
                    ->whereHas('categories', fn (Builder $relationQuery) => $relationQuery->whereIn('slug', $categorySlugs))
                    ->orWhereHas('primaryCategory', fn (Builder $relationQuery) => $relationQuery->whereIn('slug', $categorySlugs));
            });

            return;
        }

        if ($relationshipOnly) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->where(function (Builder $legacyQuery) use ($categorySlugs) {
            foreach (['category_slug', 'subcategory_slug'] as $column) {
                if (self::postHasColumn($column)) {
                    $legacyQuery->orWhereIn($column, $categorySlugs);
                }
            }
        });
    }

    private static function applyLocationFilter(Builder $query, ?string $division = null, ?string $district = null, ?string $upazila = null): void
    {
        if (! self::hasLocationFilter($division, $district, $upazila)) {
            return;
        }

        if (SchemaReadyCheck::hasLocationIdColumns()) {
            $divisionId = self::divisionId($division);
            $districtId = self::districtId($district, $divisionId);
            $upazilaId = self::upazilaId($upazila, $districtId);

            if (($division && ! $divisionId) || ($district && ! $districtId) || ($upazila && ! $upazilaId)) {
                $query->whereRaw('1 = 0');
                return;
            }

            $query
                ->when($divisionId, fn (Builder $query) => $query->where('division_id', $divisionId))
                ->when($districtId, fn (Builder $query) => $query->where('district_id', $districtId))
                ->when($upazilaId, fn (Builder $query) => $query->where('upazila_id', $upazilaId));
        }
    }

    private static function divisionId(?string $division): ?int
    {
        return self::locationId('division', $division, fn () => DB::table('divisions')
            ->where('name', $division)
            ->orWhere('name_bangla', $division)
            ->orWhere('slug', $division)
            ->value('id'));
    }

    private static function districtId(?string $district, ?int $divisionId): ?int
    {
        return self::locationId("district:{$divisionId}", $district, fn () => DB::table('districts')
            ->when($divisionId, fn ($query) => $query->where('division_id', $divisionId))
            ->where(fn ($query) => $query->where('name', $district)->orWhere('name_bangla', $district)->orWhere('slug', $district))
            ->value('id'));
    }

    private static function upazilaId(?string $upazila, ?int $districtId): ?int
    {
        return self::locationId("upazila:{$districtId}", $upazila, fn () => DB::table('upazilas')
            ->when($districtId, fn ($query) => $query->where('district_id', $districtId))
            ->where(fn ($query) => $query->where('name', $upazila)->orWhere('name_bangla', $upazila)->orWhere('slug', $upazila))
            ->value('id'));
    }

    private static function locationId(string $type, ?string $value, callable $resolver): ?int
    {
        if (! $value) {
            return null;
        }

        $cacheKey = "{$type}:{$value}";

        if (array_key_exists($cacheKey, self::$locationIdCache)) {
            return self::$locationIdCache[$cacheKey];
        }

        try {
            return self::$locationIdCache[$cacheKey] = $resolver();
        } catch (\Throwable $exception) {
            Log::warning('Failed to resolve location ID.', [
                'type' => $type,
                'value' => $value,
                'message' => $exception->getMessage(),
            ]);

            return self::$locationIdCache[$cacheKey] = null;
        }
    }

    private static function postTitle(Post $post): string
    {
        return (string) ($post->title_bn ?: $post->title ?: $post->title_en ?: 'Untitled');
    }

    private static function postExcerpt(Post $post): string
    {
        $excerpt = trim((string) ($post->excerpt ?: self::richTextPlain($post, 'summary_bn') ?: self::richTextPlain($post, 'summary_en')));

        return $excerpt !== ''
            ? $excerpt
            : Str::limit(strip_tags(self::postBodyText($post)), 170);
    }

    private static function postBodyText(Post $post): string
    {
        $body = trim((string) ($post->content ?: $post->body ?: ''));

        if ($body !== '') {
            return $body;
        }

        return self::richTextPlain($post, 'body_bn') ?: self::richTextPlain($post, 'body_en') ?: '';
    }

    private static function richTextPlain(Post $post, string $field): string
    {
        try {
            $value = $post->{$field};

            return method_exists($value, 'toPlainText')
                ? trim($value->toPlainText())
                : trim(strip_tags((string) $value));
        } catch (\Throwable) {
            return '';
        }
    }

    private static function locationLabel(Post $post): ?string
    {
        foreach (['upazila', 'upazilaLocation', 'district', 'districtLocation', 'division', 'divisionLocation'] as $relation) {
            if ($post->relationLoaded($relation) && $post->{$relation}) {
                return $post->{$relation}->name_bangla ?: $post->{$relation}->name;
            }
        }

        return null;
    }

    private static function hasLocationFilter(?string $division, ?string $district, ?string $upazila): bool
    {
        return filled($division) || filled($district) || filled($upazila);
    }

    private static function articleIdExcluded(array $article, array $exceptIds): bool
    {
        return isset($article['id']) && in_array((int) $article['id'], array_map('intval', $exceptIds), true);
    }

    private static function postHasColumn(string $column): bool
    {
        try {
            return Schema::hasColumn('posts', $column);
        } catch (\Throwable) {
            return false;
        }
    }

    private static function legacyHomepageSection(string $section, array $articles, array $fallbackArticles): array
    {
        return match ($section) {
            'breaking' => self::legacyBreakingArticles($articles, $fallbackArticles),
            'featured' => array_slice($articles, 0, 1),
            'sticky' => self::articlesAt($articles, [1, 2, 6, 7, 8, 3]),
            'trending' => self::articlesAt($articles, [4, 7, 10, 16, 19]),
            'editors_pick' => self::articlesAt($articles, [5, 9, 3]),
            default => [],
        };
    }

    private static function legacyBreakingArticles(array $articles, array $fallbackArticles): array
    {
        $tickerSlugs = [
            'metro-rail-new-route',
            'cricket-world-cup-win',
            'ai-new-development',
            'economic-growth-report',
            'new-hospital-dhaka',
            'international-climate-summit',
            'new-movie-release',
            'student-protest-update',
            'tech-startup-funding',
            'agricultural-innovation',
        ];

        return collect($tickerSlugs)
            ->map(fn (string $slug) => collect($articles)->firstWhere('slug', $slug) ?: collect($fallbackArticles)->firstWhere('slug', $slug))
            ->filter()
            ->values()
            ->all();
    }

    private static function articlesAt(array $articles, array $indexes): array
    {
        return collect($indexes)
            ->map(fn (int $index) => $articles[$index] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    private static function legacyLocalNewsSection(array $articles): Collection
    {
        return collect([18, 10, 6, 4, 15, 19, 8, 2, 11])
            ->map(fn (int $index) => $articles[$index] ?? null)
            ->filter()
            ->unique('id')
            ->values();
    }
}
