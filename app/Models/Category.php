<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Support\Locale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use BelongsToTenant, HasFactory, HasSlug;

    private static ?bool $translationTableReady = null;

    protected $fillable = [
        'tenant_id', 'parent_id', 'name', 'name_bn', 'name_en', 'slug', 'slug_bn', 'slug_en',
        'description', 'description_bn', 'description_en', 'image', 'icon',
        'color', 'order', 'status',
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

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_categories');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translationFor(?string $locale = null): ?CategoryTranslation
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

    public function setTranslationValue(string $locale, string $field, mixed $value): CategoryTranslation
    {
        $locale = Locale::normalize($locale);

        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            [$field => $value],
        );
    }

    public static function apiRelations(): array
    {
        $relations = ['parent'];

        if (class_exists(CategoryTranslation::class) && self::translationTableReady()) {
            $relations[] = 'translations';
        }

        return $relations;
    }

    private function translationValue(string $locale, string $field): mixed
    {
        return $this->translationFor($locale)?->{$field};
    }

    private static function translationTableReady(): bool
    {
        return self::$translationTableReady ??= Schema::hasTable('category_translations');
    }
}
