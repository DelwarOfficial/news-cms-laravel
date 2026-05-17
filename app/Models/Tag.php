<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Locale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{
    use BelongsToTenant, HasFactory, HasSlug;

    private static ?bool $translationTableReady = null;

    protected $fillable = [
        'tenant_id', 'name', 'name_bn', 'name_en', 'slug', 'slug_bn', 'slug_en',
        'description', 'description_bn', 'description_en',
        'meta_title', 'meta_title_bn', 'meta_title_en',
        'meta_description', 'meta_description_bn', 'meta_description_en',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->skipGenerateWhen(fn () => filled($this->slug));
    }

    public function nameForLocale(?string $locale = null): string
    {
        $locale = Locale::normalize($locale ?: app()->getLocale());

        return $this->translationValue($locale, 'name')
            ?: $this->{"name_{$locale}"}
            ?: $this->name;
    }

    public function slugForLocale(?string $locale = null): string
    {
        $locale = Locale::normalize($locale ?: app()->getLocale());

        return $this->translationValue($locale, 'slug')
            ?: $this->{"slug_{$locale}"}
            ?: $this->slug;
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }

    public function translations()
    {
        return $this->hasMany(TagTranslation::class);
    }

    public function translationFor(?string $locale = null): ?TagTranslation
    {
        if (! self::translationTableReady()) {
            return null;
        }

        $locale = Locale::normalize($locale ?: app()->getLocale());

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('locale', $locale);
        }

        return $this->translations()->where('locale', $locale)->first();
    }

    public function setTranslationValue(string $locale, string $field, mixed $value): TagTranslation
    {
        $locale = Locale::normalize($locale);

        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            [$field => $value],
        );
    }

    private function translationValue(string $locale, string $field): mixed
    {
        return $this->translationFor($locale)?->{$field};
    }

    private static function translationTableReady(): bool
    {
        return self::$translationTableReady ??= Schema::hasTable('tag_translations');
    }
}
