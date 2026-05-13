<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Support\RichTextSanitizer;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Tonysm\RichTextLaravel\Models\Traits\HasRichText;

class Post extends Model
{
    use HasFactory, SoftDeletes, HasSlug, HasRichText;

    protected $fillable = [
        'user_id', 'author_id', 'language_id', 'category_id', 'subcategory_id',
        'primary_category_id', 'division_id', 'district_id', 'upazila_id', 'shoulder',
        'title', 'title_en', 'title_bn', 'slug', 'slug_en', 'slug_bn',
        'excerpt', 'body', 'content', 'body_en', 'body_bn', 'summary_en', 'summary_bn',
        'featured_image', 'featured_media_id', 'image_path', 'featured_image_alt',
        'featured_image_caption', 'featured_image_credit', 'featured_image_source',
        'featured_image_width', 'featured_image_height', 'source_url', 'source_name',
        'category', 'category_slug', 'subcategory_slug', 'post_format', 'status',
        'published_at', 'scheduled_at', 'is_breaking', 'is_featured', 'is_sticky',
        'is_trending', 'is_editors_pick', 'is_breaking_news', 'breaking_news_order',
        'featured_order', 'sticky_order', 'trending_order', 'editors_pick_order',
        'urgency_level', 'view_count', 'reading_time', 'comment_count',
        'allow_comments', 'show_author', 'show_publish_date', 'needs_editorial_review', 'raw_import_payload', 'meta_title',
        'meta_title_en', 'meta_title_bn', 'meta_description', 'meta_description_en',
        'meta_description_bn', 'canonical_url', 'og_image',
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

        return $this->{"title_{$locale}"} ?: $this->title;
    }

    public function slugForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return $this->{"slug_{$locale}"} ?: $this->slug;
    }

    public function summaryForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);
        $field = "summary_{$locale}";
        $summary = trim($this->{$field}->toPlainText());

        return $summary !== '' ? $summary : (string) $this->excerpt;
    }

    public function bodyHtmlForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);
        $field = "body_{$locale}";
        $html = trim($this->{$field}->toHtml());

        return app(RichTextSanitizer::class)->sanitize($html !== '' ? $html : nl2br(e($this->content)));
    }

    public function editorHtml(string $field): string
    {
        return $this->{$field}->toEditorHtml();
    }

    public function metaTitleForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return $this->{"meta_title_{$locale}"} ?: $this->meta_title ?: $this->titleForLocale($locale);
    }

    public function metaDescriptionForLocale(?string $locale = null): string
    {
        $locale = $this->normalizeLocale($locale);

        return $this->{"meta_description_{$locale}"}
            ?: $this->meta_description
            ?: Str::limit($this->summaryForLocale($locale), 160, '');
    }

    public function getHreflangAlternates(): array
    {
        return collect(['en', 'bn'])
            ->filter(fn (string $locale): bool => filled($this->{"slug_{$locale}"}))
            ->mapWithKeys(fn (string $locale): array => [
                $locale => route('article.id', $this->id),
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
        return $query->where('status', 'published');
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
        return $query->with([
            'author',
            'bylineAuthor',
            'language',
            'primaryCategory.parent',
            'category.parent',
            'subcategory.parent',
            'categories.parent',
            'featuredMedia',
            'divisionLocation',
            'districtLocation',
            'upazilaLocation',
            'tags',
        ]);
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
        return (bool) $this->increment('view_count', $amount);
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

    private function normalizeLocale(?string $locale = null): string
    {
        return in_array($locale, ['en', 'bn'], true) ? $locale : 'en';
    }
}
