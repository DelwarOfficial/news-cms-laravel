<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PostCategoryResolver
{
    public const FALLBACK_SLUG = 'others-news';
    private static array $databaseCategoryIdCache = [];

    public static function fallbackCategory(): array
    {
        return CategoryRepository::findParent(self::FALLBACK_SLUG) ?? [
            'name_bn' => 'অন্যান্য খবর',
            'name_en' => 'Others News',
            'slug' => self::FALLBACK_SLUG,
            'parent_slug' => null,
            'children' => [],
        ];
    }

    public static function categoryFor(Post $post): array
    {
        if ($post->relationLoaded('primaryCategory') && $post->getRelation('primaryCategory')) {
            return self::categoryArrayFromModel($post->getRelation('primaryCategory'));
        }

        if ($post->relationLoaded('categories') && $post->getRelation('categories')->isNotEmpty()) {
            $category = $post->getRelation('categories')->firstWhere('pivot.is_primary', true)
                ?? $post->getRelation('categories')->first();

            if ($category) {
                return self::categoryArrayFromModel($category);
            }
        }

        if ($post->relationLoaded('subcategory') && $post->getRelation('subcategory')) {
            return self::categoryArrayFromModel($post->getRelation('subcategory'));
        }

        if ($post->relationLoaded('category') && $post->getRelation('category')) {
            // The posts table also has a scalar `category` compatibility column for
            // live CMS data. Use the loaded relation explicitly so that column never
            // shadows the Category model needed by the resolver.
            return self::categoryArrayFromModel($post->getRelation('category'));
        }

        return self::findBySlug($post->subcategory_slug)
            ?? self::findBySlug($post->category_slug)
            ?? self::fallbackCategory();
    }

    public static function effectiveSlug(Post $post): string
    {
        return self::categoryFor($post)['slug'] ?? self::FALLBACK_SLUG;
    }

    public static function isValidSlug(?string $slug): bool
    {
        return self::findBySlug($slug) !== null;
    }

    public static function findBySlug(?string $slug): ?array
    {
        $slug = trim((string) $slug);

        if ($slug === '') {
            return null;
        }

        return CategoryRepository::flat()->firstWhere('slug', $slug);
    }

    public static function assignmentFor(Post $post): array
    {
        $explicit = self::explicitCategory($post);
        $strongMatch = self::strongKeywordCategory($post);

        if ($strongMatch && (! $explicit || empty($explicit['parent_slug']))) {
            return self::assignmentFromCategory($strongMatch, 'strong-keyword-match', false);
        }

        if ($explicit) {
            $explicit = self::refineParentCategory($explicit, $post) ?? $explicit;

            return self::assignmentFromCategory($explicit, 'explicit-category-data', false);
        }

        // Keep valid slugs first; only infer a category when the stored value is missing or broken.
        $current = self::findBySlug($post->subcategory_slug) ?? self::findBySlug($post->category_slug);

        if ($current) {
            return self::assignmentFromCategory($current, 'existing-valid', false);
        }

        $detected = self::detectCategory($post);

        if ($detected) {
            return self::assignmentFromCategory($detected, 'matched-from-content', false);
        }

        return self::assignmentFromCategory(self::fallbackCategory(), 'fallback-needs-review', true);
    }

    public static function assignmentFromCategory(array $category, string $reason = 'manual', bool $needsReview = false): array
    {
        $parentSlug = $category['parent_slug'] ?? null;

        return [
            'category' => $category,
            'category_slug' => $parentSlug ?: $category['slug'],
            'subcategory_slug' => $parentSlug ? $category['slug'] : null,
            'category_id' => self::databaseCategoryId($parentSlug ?: $category['slug']),
            'subcategory_id' => $parentSlug ? self::databaseCategoryId($category['slug']) : null,
            'needs_review' => $needsReview,
            'reason' => $reason,
        ];
    }

    public static function categoryRoute(array $category): ?string
    {
        try {
            return CategoryRepository::route($category);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to generate category route: " . $e->getMessage());
            return null;
        }
    }

    private static function detectCategory(Post $post): ?array
    {
        $haystack = self::searchableText($post);

        // Prefer configured category names/slugs before broader keyword rules.
        return self::matchConfiguredCategory($haystack)
            ?? self::matchRedirectCategory($haystack)
            ?? self::matchKeywordCategory($haystack);
    }

    private static function explicitCategory(Post $post): ?array
    {
        $payload = is_array($post->raw_import_payload) ? $post->raw_import_payload : [];
        $candidates = array_filter([
            $payload['subcategory_slug'] ?? null,
            $payload['category_slug'] ?? null,
            $payload['subcategory'] ?? null,
            $payload['category'] ?? null,
            $payload['category_name'] ?? null,
            $payload['section'] ?? null,
        ]);

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);

            if ($category = self::findBySlug($candidate)) {
                return $category;
            }

            if ($category = self::categoryFromRedirectOrName($candidate)) {
                return $category;
            }
        }

        return null;
    }

    private static function strongKeywordCategory(Post $post): ?array
    {
        $haystack = self::searchableText($post);
        $rules = [
            'bangladesh/dhaka' => ['metro-rail', 'traffic-jam'],
            'economy/agriculture' => ['agriculture', 'agricultural', 'farmer', 'crop'],
            'sports/cricket' => ['cricket'],
            'sports/other-sports' => ['olympic'],
            'lifestyle/health' => ['health', 'hospital', 'doctor'],
            'others-news/technology' => ['ai-', 'artificial-intelligence', 'tech-startup', 'smart-phone', 'technology'],
            'others-news/opinion' => ['opinion-'],
        ];

        foreach ($rules as $target => $needles) {
            foreach ($needles as $needle) {
                if (Str::contains($haystack, $needle)) {
                    return self::categoryFromTarget($target);
                }
            }
        }

        return null;
    }

    private static function refineParentCategory(array $category, Post $post): ?array
    {
        if (! empty($category['parent_slug']) || empty($category['children'])) {
            return null;
        }

        return self::matchChildKeywordForParent($category['slug'], self::searchableText($post));
    }

    private static function searchableText(Post $post): string
    {
        $payload = $post->raw_import_payload;
        $payloadText = '';

        if (is_array($payload)) {
            $payloadText = collect(Arr::flatten($payload))
                ->map(fn ($value) => is_scalar($value) ? (string) $value : json_encode($value))
                ->implode(' ');
        } elseif (is_string($payload)) {
            $payloadText = $payload;
        }

        return Str::lower(implode(' ', array_filter([
            $post->title,
            $post->slug,
            $post->excerpt,
            $post->body,
            $post->source_url,
            $post->source_name,
            $post->category_slug,
            $post->subcategory_slug,
            $post->meta_title,
            $post->meta_description,
            $payloadText,
        ])));
    }

    private static function matchConfiguredCategory(string $haystack): ?array
    {
        $categories = CategoryRepository::flat()
            ->sortByDesc(fn (array $category) => $category['parent_slug'] ? 1 : 0);

        foreach ($categories as $category) {
            $terms = array_filter([
                isset($category['parent_slug']) ? "{$category['parent_slug']}/{$category['slug']}" : null,
                $category['slug'] ?? null,
                $category['name_en'] ?? null,
                $category['name_bn'] ?? null,
            ]);

            foreach ($terms as $term) {
                $term = Str::lower((string) $term);

                if ($term !== '' && Str::contains($haystack, $term)) {
                    return $category;
                }
            }
        }

        return null;
    }

    private static function matchRedirectCategory(string $haystack): ?array
    {
        foreach (config('categories.redirects', []) as $needle => $target) {
            if (! Str::contains($haystack, Str::lower((string) $needle))) {
                continue;
            }

            return self::categoryFromTarget((string) $target);
        }

        return null;
    }

    private static function categoryFromRedirectOrName(string $value): ?array
    {
        $normalized = Str::lower($value);

        foreach (CategoryRepository::flat() as $category) {
            $names = array_filter([
                $category['name_bn'] ?? null,
                $category['name_en'] ?? null,
                $category['slug'] ?? null,
            ]);

            foreach ($names as $name) {
                if ($normalized === Str::lower((string) $name)) {
                    return $category;
                }
            }
        }

        $target = config('categories.redirects')[$value] ?? config('categories.redirects')[$normalized] ?? null;

        return $target ? self::categoryFromTarget((string) $target) : null;
    }

    private static function matchKeywordCategory(string $haystack): ?array
    {
        $rules = [
            'bangladesh/politics' => ['politics', 'election', 'parliament', 'minister', 'awami', 'bnp', 'jamaat'],
            'bangladesh/crime' => ['crime', 'police', 'arrest', 'case', 'court', 'murder'],
            'bangladesh/dhaka' => ['dhaka', 'capital', 'metro', 'traffic'],
            'bangladesh/accidents' => ['accident', 'fire', 'collision', 'crash'],
            'economy' => ['economy', 'business', 'market', 'bank', 'stock', 'trade', 'price'],
            'economy/agriculture' => ['agriculture', 'farmer', 'crop'],
            'world' => ['world', 'international', 'global', 'usa', 'india', 'china', 'russia'],
            'entertainment' => ['entertainment', 'film', 'cinema', 'music', 'drama', 'ott'],
            'sports/cricket' => ['cricket', 'bcb', 'icc', 't20', 'odi', 'test match'],
            'sports/football' => ['football', 'fifa', 'uefa', 'premier league'],
            'sports/other-sports' => ['olympic', 'athlete', 'athletics'],
            'jobs/government-jobs' => ['government job', 'govt job', 'circular', 'recruitment', 'bpsc'],
            'jobs/private-jobs' => ['private job', 'career', 'vacancy'],
            'lifestyle/health' => ['health', 'doctor', 'hospital', 'medicine', 'disease'],
            'lifestyle/recipes' => ['recipe', 'food', 'cooking'],
            'others-news/technology' => ['technology', 'tech', 'ai', 'startup', 'internet', 'gadget'],
            'others-news/education' => ['education', 'school', 'college', 'university', 'exam', 'result'],
            'others-news/opinion' => ['opinion', 'editorial', 'column', 'analysis', 'op-ed'],
            'others-news/religion' => ['religion', 'islam', 'ramadan', 'eid', 'hajj'],
            'others-news/expatriates' => ['expatriate', 'probashi', 'remittance', 'visa'],
        ];

        foreach ($rules as $target => $needles) {
            foreach ($needles as $needle) {
                if (Str::contains($haystack, $needle)) {
                    return self::categoryFromTarget($target);
                }
            }
        }

        return null;
    }

    private static function matchChildKeywordForParent(string $parentSlug, string $haystack): ?array
    {
        $rules = [
            'bangladesh/national' => ['national'],
            'bangladesh/dhaka' => ['dhaka', 'capital', 'metro', 'traffic'],
            'bangladesh/crime' => ['crime', 'police', 'arrest', 'case', 'court', 'murder'],
            'bangladesh/accidents' => ['accident', 'fire', 'collision', 'crash'],
            'bangladesh/politics' => ['politics', 'election', 'parliament', 'minister', 'awami', 'bnp', 'jamaat'],
            'economy/stock-market' => ['stock', 'share market'],
            'economy/banking-insurance' => ['bank', 'insurance'],
            'economy/industry' => ['industry', 'factory'],
            'economy/agriculture' => ['agriculture', 'agricultural', 'farmer', 'crop'],
            'sports/cricket' => ['cricket', 'bcb', 'icc', 't20', 'odi', 'test match'],
            'sports/football' => ['football', 'fifa', 'uefa', 'premier league'],
            'sports/other-sports' => ['olympic', 'athlete', 'athletics'],
            'jobs/government-jobs' => ['government job', 'govt job', 'circular', 'recruitment', 'bpsc'],
            'jobs/private-jobs' => ['private job', 'career', 'vacancy'],
            'lifestyle/health' => ['health', 'doctor', 'hospital', 'medicine', 'disease'],
            'lifestyle/beauty' => ['beauty', 'skin', 'hair'],
            'lifestyle/recipes' => ['recipe', 'food', 'cooking'],
            'others-news/technology' => ['technology', 'tech', 'ai', 'startup', 'internet', 'gadget'],
            'others-news/education' => ['education', 'school', 'college', 'university', 'exam', 'result'],
            'others-news/opinion' => ['opinion', 'editorial', 'column', 'analysis', 'op-ed'],
            'others-news/religion' => ['religion', 'islam', 'ramadan', 'eid', 'hajj'],
            'others-news/expatriates' => ['expatriate', 'probashi', 'remittance', 'visa'],
        ];

        foreach ($rules as $target => $needles) {
            if (! Str::startsWith($target, "{$parentSlug}/")) {
                continue;
            }

            foreach ($needles as $needle) {
                if (Str::contains($haystack, $needle)) {
                    return self::categoryFromTarget($target);
                }
            }
        }

        return null;
    }

    private static function categoryFromTarget(string $target): ?array
    {
        $parts = explode('/', trim($target, '/'));

        if (count($parts) === 2) {
            return CategoryRepository::findChild($parts[0], $parts[1]);
        }

        return CategoryRepository::findParent($parts[0] ?? '');
    }

    private static function databaseCategoryId(?string $slug): ?int
    {
        if (! $slug || ! Schema::hasTable('categories')) {
            return null;
        }

        if (array_key_exists($slug, self::$databaseCategoryIdCache)) {
            return self::$databaseCategoryIdCache[$slug];
        }

        return self::$databaseCategoryIdCache[$slug] = \App\Models\Category::query()
            ->where('slug', $slug)
            ->value('id');
    }

    private static function categoryArrayFromModel(\App\Models\Category $category): array
    {
        $parent = $category->relationLoaded('parent') ? $category->parent : null;

        return [
            'id' => $category->id,
            'name_bn' => $category->name_bn ?? $category->name ?? $category->slug,
            'name_en' => $category->name_en,
            'slug' => $category->slug,
            'parent_slug' => $parent?->slug,
            'children' => [],
        ];
    }
}
