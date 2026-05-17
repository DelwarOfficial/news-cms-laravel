<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subdomain',
        'database',
        'settings',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'expires_at' => 'datetime',
        'status' => 'string',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(Widget::class);
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    public function contentPlacements(): HasMany
    {
        return $this->hasMany(ContentPlacement::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public static function identifyBySubdomain(string $host): ?self
    {
        $parts = explode('.', $host);

        if (count($parts) < 2) {
            return null;
        }

        $subdomain = $parts[0];

        if (in_array($subdomain, ['www', 'mail', 'admin'], true)) {
            return null;
        }

        return self::active()->where('subdomain', $subdomain)->first();
    }

    public static function identifyByDomain(string $host): ?self
    {
        return self::active()->where('domain', $host)->first();
    }

    public static function identify(string $host): ?self
    {
        return self::identifyByDomain($host) ?? self::identifyBySubdomain($host);
    }

    public function mediaStoragePath(string $path = ''): string
    {
        $base = sprintf(config('tenancy.media_path', 'tenants/%s/media'), $this->slug);
        return $path ? "{$base}/{$path}" : $base;
    }

    public function queuePrefix(): string
    {
        return "tenant:{$this->slug}:";
    }

    public function cachePrefix(): string
    {
        return "t{$this->id}:";
    }
}
