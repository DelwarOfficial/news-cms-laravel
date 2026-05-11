<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'user_id', 'language_id', 'title', 'title_en', 'title_bn', 'slug', 'slug_en', 'slug_bn',
        'excerpt', 'content', 'body_en', 'body_bn', 'summary_en', 'summary_bn', 'status',
        'published_at', 'is_breaking', 'is_featured', 'is_trending',
        'is_editors_pick', 'urgency_level', 'meta_title', 'meta_title_en',
        'meta_title_bn', 'meta_description', 'meta_description_en',
        'meta_description_bn', 'canonical_url',
    ];

    protected $richTextAttributes = [
        'body_en',
        'body_bn',
        'summary_en',
        'summary_bn',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_editors_pick' => 'boolean',
    ];

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
                $locale => route('post.show', $this->{"slug_{$locale}"}),
            ])
            ->all();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'post_categories');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function relatedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_related', 'post_id', 'related_post_id');
    }

    public function translations()
    {
        return $this->hasMany(PostTranslation::class);
    }

    public function revisions()
    {
        return $this->hasMany(PostRevision::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getReadingTimeAttribute(): int
    {
        $plainText = trim($this->body_en->toPlainText()) ?: strip_tags($this->content);
        $words = str_word_count($plainText);
        return max(1, (int) ceil($words / 200));
    }

    private function normalizeLocale(?string $locale = null): string
    {
        return in_array($locale, ['en', 'bn'], true) ? $locale : 'en';
    }
}
