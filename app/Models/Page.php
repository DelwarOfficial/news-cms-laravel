<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'title', 'title_bn', 'title_en', 'slug', 'slug_bn', 'slug_en',
        'content', 'content_bn', 'content_en',
        'status',
        'meta_title', 'meta_title_bn', 'meta_title_en',
        'meta_description', 'meta_description_bn', 'meta_description_en',
    ];

    public function titleForLocale(?string $locale = null): string
    {
        $locale = $locale ?? config('app.locale', 'bn');
        return $this->{"title_{$locale}"} ?: $this->title;
    }

    public function contentForLocale(?string $locale = null): string
    {
        $locale = $locale ?? config('app.locale', 'bn');
        return $this->{"content_{$locale}"} ?: $this->content;
    }
}
