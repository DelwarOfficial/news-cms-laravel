<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\ViewCounter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Support\Locale;
use App\Support\RichTextSanitizer;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Tonysm\RichTextLaravel\Models\Traits\HasRichText;

class Post extends Model
{
    use BelongsToTenant, HasFactory, SoftDeletes, HasSlug, HasRichText;

    public const CACHE_PREFIX = 'post_model:';
    public const CACHE_TTL = 300;

    private static ?bool $translationTableReady = null;

    protected $fillable = [
        'tenant_id', 'author_id', 'language_id', 'primary_category_id',
        'division_id', 'district_id', 'upazila_id', 'shoulder',
        'title', 'title_en', 'title_bn', 'slug', 'slug_en', 'slug_bn',
        'excerpt', 'content', 'body_en', 'body_bn', 'summary_en', 'summary_bn',
        'featured_image', 'featured_media_id', 'featured_image_alt',
        'featured_image_caption',
        'source_url', 'source_name', 'post_format', 'status', 'visibility',
        'published_at', 'scheduled_at', 'is_breaking', 'is_featured', 'is_sticky',
        'is_photocard',
        'is_trending', 'is_editors_pick',
        'allow_comments', 'show_author', 'show_publish_date', 'raw_import_payload',
        'meta_title', 'meta_title_en', 'meta_title_bn',
        'meta_description', 'meta_description_en', 'meta_description_bn',
        'canonical_url', 'og_image',
    ];

    protected $richTextAttributes = [
        'body_en',
        'body_bn',
        'summary_en',
        'summary_bn',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_breaking' => 'boolean',
        'is_breaking_news' => 'boolean',
        'is_featured' => 'boolean',
        'is_sticky' => 'boolean',
        'is_photocard' => 'boolean',
        'is_trending' => 'boolean',
        'is_editors_pick' => 'boolean',
        'allow_comments' => 'boolean',
        'show_author' => 'boolean',
        'show_publish_date' => 'boolean',
        'needs_editorial_review' => 'boolean',
        'view_count' => 'integer',
        'reading_time' => 'integer',
        'comment_count' => 'integer',
        'featured_image_width' => 'integer',
        'featured_image_height' => 'integer',
        'breaking_news_order' => 'integer',
        'featured_order' => 'integer',
        'sticky_order' => 'integer',
        'trending_order' => 'integer',
        'editors_pick_order' => 'integer',
        'raw_import_payload' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Post $post): void {
            $post->reading_time = $post->calculateReadingTime();
        });
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function titleForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return $this->translationValue($locale, 'title')
            ?: $this->{"title_{$locale}"}
            ?: $this->title;
    }

    public function slugForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return $this->translationValue($locale, 'slug')
            ?: $this->{"slug_{$locale}"}
            ?: $this->slug;
    }

    public function summaryForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);
        $translatedSummary = $this->translationValue($locale, 'summary');
        if (filled($translatedSummary)) {
            return (string) $translatedSummary;
        }

        $field = "summary_{$locale}";
        $richText = $this->{$field};
        $summary = $richText ? trim($richText->toPlainText()) : '';

        return $summary !== '' ? $summary : (string) $this->excerpt;
    }

    public function bodyHtmlForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);
        $translatedBody = $this->translationValue($locale, 'body');
        if (filled($translatedBody)) {
            return app(RichTextSanitizer::class)->sanitize((string) $translatedBody);
        }

        $field = "body_{$locale}";
        $richText = $this->{$field};
        $html = $richText ? trim($richText->toHtml()) : '';

        return app(RichTextSanitizer::class)->sanitize($html !== '' ? $html : nl2br(e($this->content)));
    }

    public function editorHtml(string $field): string
    {
        $richText = $this->{$field};
        return $richText ? $richText->toEditorHtml() : '';
    }

    public function metaTitleForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return $this->translationValue($locale, 'meta_title')
            ?: $this->{"meta_title_{$locale}"}
            ?: $this->meta_title
            ?: $this->titleForLocale($locale);
    }

    public function metaDescriptionForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return $this->translationValue($locale, 'meta_description')
            ?: $this->{"meta_description_{$locale}"}
            ?: $this->meta_description
            ?: Str::limit($this->summaryForLocale($locale), 160, '');
    }

    public function getHreflangAlternates(): array
    {
        return collect(['en', 'bn'])
            ->filter(fn (string $locale): bool => filled($this->{"slug_{$locale}"}))
            ->mapWithKeys(fn (string $locale): array => [
                $locale => route('article.id_slug', [
                    'postId' => $this->id,
                    'slug' => $this->slugForLocale($locale),
                ]),
            ])
            ->all();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cmsAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bylineAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function primaryCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'primary_category_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_categories')
            ->withPivot('is_primary');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_related', 'post_id', 'related_post_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class);
    }

    public function translationFor(?string $locale = null): ?PostTranslation
    {
        if (! self::translationTableReady()) {
            return null;
        }

        $locale = $this->normalizeLocale($locale);

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale);
        }

        return $this->translations()->where('locale', $locale)->first();
    }

    public function setTranslationValue(string $locale, string $field, mixed $value): PostTranslation
    {
        $locale = $this->normalizeLocale($locale);

        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            [$field => $value],
        );
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PostRevision::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function divisionLocation(): BelongsTo
    {
        return $this->division();
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function districtLocation(): BelongsTo
    {
        return $this->district();
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }

    public function upazilaLocation(): BelongsTo
    {
        return $this->upazila();
    }

    public function contentPlacements(): HasMany
    {
        return $this->hasMany(ContentPlacement::class);
    }

    public function placements(): HasMany
    {
        return $this->contentPlacements();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('visibility', 'public');
    }

    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }

    public function scopeBreakingNews(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeSticky(Builder $query): Builder
    {
        return $query->where('is_sticky', true);
    }

    public function scopePhotocard(Builder $query): Builder
    {
        return $query->where('is_photocard', true);
    }

    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('is_trending', true);
    }

    public function scopeEditorsPick(Builder $query): Builder
    {
        return $query->where('is_editors_pick', true);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('view_count')->orderByDesc('published_at');
    }

    public function scopeWithContentRelations(Builder $query): Builder
    {
        return $query->with(self::contentRelations());
    }

    public function scopeWithListRelations(Builder $query): Builder
    {
        return $query->with(self::listRelations());
    }

    public static function contentRelations(): array
    {
        $relations = [
            'author:id,name,username,avatar',
            'bylineAuthor:id,name,username',
            'language:id,name,code',
            'primaryCategory:id,name,slug,color',
            'category:id,name,slug',
            'subcategory:id,name,slug',
            'categories:id,name,slug',
            'featuredMedia:id,file_name,file_path,file_url,alt_text,width,height',
            'division:id,name,name_en,slug',
            'divisionLocation:id,name,name_en,slug',
            'district:id,name,name_en,slug,division_id',
            'districtLocation:id,name,name_en,slug,division_id',
            'upazila:id,name,name_en,slug,district_id',
            'upazilaLocation:id,name,name_en,slug,district_id',
            'tags:id,name,slug',
        ];

        if (class_exists(PostTranslation::class) && self::translationTableReady()) {
            $relations[] = 'translations';
        }

        return $relations;
    }

    public static function listRelations(): array
    {
        $relations = [
            'author:id,name,username',
            'bylineAuthor:id,name,username',
            'primaryCategory:id,name,slug,color',
            'category:id,name,slug',
            'categories:id,name,slug',
            'featuredMedia:id,file_name,file_path,file_url,alt_text',
            'tags:id,name,slug',
        ];

        if (class_exists(PostTranslation::class) && self::translationTableReady()) {
            $relations[] = 'translations';
        }

        return $relations;
    }

    public function scopeLocalLocated(Builder $query): Builder
    {
        return $query
            ->whereNotNull('division_id')
            ->whereNotNull('district_id');
    }

    public function getReadingTimeAttribute($value): int
    {
        if ($value !== null) {
            return (int) $value;
        }

        return $this->calculateReadingTime();
    }

    public function incrementViews(int $amount = 1): bool
    {
        app(ViewCounter::class)->increment($this->id, $amount);
        return true;
    }

    public function syncViewCount(): void
    {
        app(ViewCounter::class)->syncToDatabase($this->id);
    }

    public static function findCached(int $id): ?self
    {
        return Cache::remember(self::CACHE_PREFIX . $id, self::CACHE_TTL, function () use ($id) {
            return self::withContentRelations()->find($id);
        });
    }

    public static function findBySlugCached(string $slug): ?self
    {
        return Cache::remember(self::CACHE_PREFIX . "slug:{$slug}", self::CACHE_TTL, function () use ($slug) {
            return self::withContentRelations()->published()
                ->where(function (Builder $query) use ($slug): void {
                    $query->where('slug', $slug)
                        ->orWhere('slug_en', $slug)
                        ->orWhere('slug_bn', $slug);
                })
                ->first();
        });
    }

    public static function forgetCached(int|self $post): void
    {
        $id = is_object($post) ? $post->id : $post;
        $slugs = is_object($post)
            ? array_filter([$post->slug, $post->slug_en, $post->slug_bn])
            : [];

        Cache::forget(self::CACHE_PREFIX . $id);
        foreach ($slugs as $slug) {
            Cache::forget(self::CACHE_PREFIX . "slug:{$slug}");
        }
    }

    private function calculateReadingTime(): int
    {
        $plainText = trim((string) ($this->content ?: $this->body ?: ''));

        foreach (['body_en', 'body_bn'] as $field) {
            if ($plainText !== '') {
                continue;
            }

            try {
                $richText = $this->{$field};
                $plainText = method_exists($richText, 'toPlainText')
                    ? trim($richText->toPlainText())
                    : trim((string) $richText);
            } catch (\Throwable) {
                $plainText = '';
            }
        }

        $words = str_word_count(strip_tags($plainText));

        return max(1, (int) ceil($words / 200));
    }

    public function getRichTextAttributes(): array
    {
        return $this->richTextAttributes;
    }

    private function normalizeLocale(?string $locale = null): string
    {
        return Locale::normalize($locale ?: app()->getLocale());
    }

    private function translationValue(string $locale, string $field): mixed
    {
        return $this->translationFor($locale)?->{$field};
    }

    private static function translationTableReady(): bool
    {
        return self::$translationTableReady ??= Schema::hasTable('post_translations');
    }
}
