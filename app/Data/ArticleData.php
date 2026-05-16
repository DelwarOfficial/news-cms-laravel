<?php

namespace App\Data;

use App\Models\Post;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Standardized data contract between CMS and frontend.
 *
 * All API responses for articles (PostResource, HomepageDataService)
 * MUST conform to this shape. The frontend's ArticleFeed::toViewArray()
 * maps these fields to Blade-view-compatible arrays.
 *
 * @template T of array
 */
final class ArticleData
{
    /**
     * @param  int|null  $id  Post primary key
     * @param  string  $slug  URL slug (not locale-specific)
     * @param  string  $title  Locale-aware title (auto-picks bn/en)
     * @param  string|null  $title_bn  Bengali title
     * @param  string|null  $title_en  English title
     * @param  string|null  $shoulder  Pre-headline label (Bangla: শোল্ডার)
     * @param  string|null  $excerpt  Short description
     * @param  string|null  $image_url  Full URL to featured image
     * @param  string|null  $image_alt  Alt text for image
     * @param  string|null  $image_caption  Caption for image
     * @param  string|null  $published_at  ISO-8601 datetime
     * @param  int  $reading_time  Estimated minutes to read
     * @param  int  $view_count  Total page views
     * @param  bool  $is_breaking  Show in breaking news ticker
     * @param  bool  $is_breaking_news  Legacy alias for is_breaking
     * @param  bool  $is_featured  Featured on homepage hero
     * @param  bool  $is_trending  Currently trending
     * @param  bool  $is_editors_pick  Editorially recommended
     * @param  bool  $is_sticky  Always on top within section
     * @param  bool  $is_photocard  Display as photo card
     * @param  string  $post_format  standard|video|gallery|live|opinion
     * @param  string|null  $category_name_bn  Resolved category name (Bengali)
     * @param  string|null  $category_slug  Resolved category slug
     * @param  array{name_bn: string|null, name_en: string|null, slug: string|null}|null  $category  Structured category object
     * @param  string|null  $author_name  Display name of author/byline
     * @param  string|null  $author_username  Login username
     * @param  string|null  $author_avatar  Avatar URL
     * @param  list<array{name: string, slug: string}>  $tags
     * @param  string|null  $meta_title  SEO title (locale-aware)
     * @param  string|null  $meta_title_bn
     * @param  string|null  $meta_title_en
     * @param  string|null  $meta_description  SEO description (locale-aware)
     * @param  string|null  $meta_description_bn
     * @param  string|null  $meta_description_en
     * @param  string|null  $canonical_url  Canonical URL for SEO
     * @param  string|null  $og_image  Open Graph image URL
     * @param  string|null  $locale  Current response locale
     * @param  list<string>|null  $body  Article body paragraphs (only on detail)
     * @param  string|null  $location  Resolved location name (Bengali)
     * @param  int|null  $division_id
     * @param  int|null  $district_id
     * @param  int|null  $upazila_id
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $slug,
        public readonly string $title,
        public readonly ?string $title_bn = null,
        public readonly ?string $title_en = null,
        public readonly ?string $shoulder = null,
        public readonly ?string $excerpt = null,
        public readonly ?string $image_url = null,
        public readonly ?string $image_alt = null,
        public readonly ?string $image_caption = null,
        public readonly ?string $published_at = null,
        public readonly int $reading_time = 1,
        public readonly int $view_count = 0,
        public readonly bool $is_breaking = false,
        public readonly bool $is_breaking_news = false,
        public readonly bool $is_featured = false,
        public readonly bool $is_trending = false,
        public readonly bool $is_editors_pick = false,
        public readonly bool $is_sticky = false,
        public readonly bool $is_photocard = false,
        public readonly string $post_format = 'standard',
        public readonly ?string $category_name_bn = null,
        public readonly ?string $category_slug = null,
        public readonly ?array $category = null,
        public readonly ?string $author_name = null,
        public readonly ?string $author_username = null,
        public readonly ?string $author_avatar = null,
        public readonly array $tags = [],
        public readonly ?string $meta_title = null,
        public readonly ?string $meta_title_bn = null,
        public readonly ?string $meta_title_en = null,
        public readonly ?string $meta_description = null,
        public readonly ?string $meta_description_bn = null,
        public readonly ?string $meta_description_en = null,
        public readonly ?string $canonical_url = null,
        public readonly ?string $og_image = null,
        public readonly ?string $locale = null,
        public readonly ?array $body = null,
        public readonly ?string $location = null,
        public readonly ?int $division_id = null,
        public readonly ?int $district_id = null,
        public readonly ?int $upazila_id = null,
    ) {
    }

    /**
     * Factory from Eloquent Post model (used by PostResource / services).
     */
    public static function fromModel(Post $post, ?string $locale = null, bool $includeBody = false): self
    {
        $locale ??= app()->getLocale();

        $imageUrl = self::resolveImageUrl($post);
        $category = self::resolveCategory($post);

        $data = new self(
            id: $post->id,
            slug: $post->slug,
            title: $post->titleForLocale($locale),
            title_bn: $post->title_bn,
            title_en: $post->title_en,
            shoulder: $post->shoulder,
            excerpt: $post->excerpt,
            image_url: $imageUrl,
            image_alt: $post->featured_image_alt ?: $post->title,
            image_caption: $post->featured_image_caption,
            published_at: $post->published_at?->toIso8601String(),
            reading_time: (int) ($post->reading_time ?? 1),
            view_count: (int) ($post->view_count ?? 0),
            is_breaking: (bool) ($post->is_breaking ?? false),
            is_breaking_news: (bool) ($post->is_breaking_news ?? false),
            is_featured: (bool) ($post->is_featured ?? false),
            is_trending: (bool) ($post->is_trending ?? false),
            is_editors_pick: (bool) ($post->is_editors_pick ?? false),
            is_sticky: (bool) ($post->is_sticky ?? false),
            is_photocard: (bool) ($post->is_photocard ?? false),
            post_format: $post->post_format ?? 'standard',
            category_name_bn: $category['name_bn'],
            category_slug: $category['slug'],
            category: $category,
            author_name: $post->bylineAuthor?->name ?? $post->author?->name,
            author_username: $post->author?->username,
            author_avatar: $post->author?->avatar,
            tags: $post->relationLoaded('tags')
                ? $post->tags->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])->values()->all()
                : [],
            meta_title: $post->metaTitleForLocale($locale),
            meta_title_bn: $post->meta_title_bn,
            meta_title_en: $post->meta_title_en,
            meta_description: $post->metaDescriptionForLocale($locale),
            meta_description_bn: $post->meta_description_bn,
            meta_description_en: $post->meta_description_en,
            canonical_url: $post->canonical_url,
            og_image: $post->og_image,
            locale: $locale,
            body: $includeBody ? self::resolveBody($post, $locale) : null,
            location: $includeBody ? ($post->districtLocation?->name_bangla ?? $post->districtLocation?->name) : null,
            division_id: $post->division_id,
            district_id: $post->district_id,
            upazila_id: $post->upazila_id,
        );

        return $data;
    }

    /**
     * Convert to the array format expected by PostResource and the frontend contract.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'title_bn' => $this->title_bn,
            'title_en' => $this->title_en,
            'shoulder' => $this->shoulder,
            'excerpt' => $this->excerpt,
            'image_url' => $this->image_url,
            'image_alt' => $this->image_alt,
            'image_caption' => $this->image_caption,
            'published_at' => $this->published_at,
            'reading_time' => $this->reading_time,
            'view_count' => $this->view_count,
            'is_breaking' => $this->is_breaking,
            'is_breaking_news' => $this->is_breaking_news,
            'is_featured' => $this->is_featured,
            'is_trending' => $this->is_trending,
            'is_editors_pick' => $this->is_editors_pick,
            'is_sticky' => $this->is_sticky,
            'is_photocard' => $this->is_photocard,
            'post_format' => $this->post_format,
            'category_name_bn' => $this->category_name_bn,
            'category_slug' => $this->category_slug,
            'category' => $this->category,
            'author_name' => $this->author_name,
            'author_username' => $this->author_username,
            'author_avatar' => $this->author_avatar,
            'tags' => $this->tags,
            'meta_title' => $this->meta_title,
            'meta_title_bn' => $this->meta_title_bn,
            'meta_title_en' => $this->meta_title_en,
            'meta_description' => $this->meta_description,
            'meta_description_bn' => $this->meta_description_bn,
            'meta_description_en' => $this->meta_description_en,
            'canonical_url' => $this->canonical_url,
            'og_image' => $this->og_image,
            'locale' => $this->locale,
            'body' => $this->body,
            'location' => $this->location,
            'division_id' => $this->division_id,
            'district_id' => $this->district_id,
            'upazila_id' => $this->upazila_id,
        ];
    }

    private static function resolveImageUrl(Post $post): ?string
    {
        if ($post->relationLoaded('featuredMedia') && $post->featuredMedia) {
            return $post->featuredMedia->file_url ?: $post->featuredMedia->url;
        }

        if ($post->featured_image) {
            return url('storage/' . ltrim($post->featured_image, '/'));
        }

        return null;
    }

    /**
     * @return array{name_bn: string|null, name_en: string|null, slug: string|null}
     */
    private static function resolveCategory(Post $post): array
    {
        $primaryCat = $post->relationLoaded('primaryCategory') ? $post->primaryCategory : null;
        $cats = $post->relationLoaded('categories') ? $post->categories : collect();

        if ($primaryCat) {
            return [
                'name_bn' => $primaryCat->name_bn ?? $primaryCat->name,
                'name_en' => $primaryCat->name_en,
                'slug' => $primaryCat->slug,
            ];
        }

        if ($cats->isNotEmpty()) {
            $c = $cats->firstWhere('pivot.is_primary', true) ?? $cats->first();

            return [
                'name_bn' => $c->name_bn ?? $c->name,
                'name_en' => $c->name_en,
                'slug' => $c->slug,
            ];
        }

        return ['name_bn' => null, 'name_en' => null, 'slug' => null];
    }

    /**
     * @return list<string>
     */
    private static function resolveBody(Post $post, string $locale): array
    {
        $html = $post->bodyHtmlForLocale($locale);

        return collect(preg_split('/\R{2,}/', strip_tags($html)))
            ->map(fn (string $p) => trim($p))
            ->filter()
            ->values()
            ->all();
    }
}
