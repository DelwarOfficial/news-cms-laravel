<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{
    use BelongsToTenant, HasFactory, HasSlug;

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
        $locale = $locale ?? config('app.locale', 'bn');
        return $this->{"name_{$locale}"} ?: $this->name;
    }

    public function slugForLocale(?string $locale = null): string
    {
        $locale = $locale ?? config('app.locale', 'bn');
        return $this->{"slug_{$locale}"} ?: $this->slug;
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }
}
