<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Str;

class ImageResolver
{
    public static function postImageUrl(Post $post): string
    {
        if ($post->relationLoaded('featuredMedia') && $post->featuredMedia) {
            return self::imageUrl($post->featuredMedia->path ?? $post->featuredMedia->file_path ?? $post->featuredMedia->file_url);
        }

        if ($post->image_path) {
            $filename = basename($post->image_path);

            return file_exists(public_path("images/{$filename}"))
                ? asset("images/{$filename}")
                : self::placeholderImageUrl();
        }

        return self::imageUrl($post->featured_image);
    }

    public static function imageUrl(?string $path): string
    {
        if (! $path) {
            return asset('images/news-1.jpg');
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, ['/storage/', 'storage/'])) {
            return asset(ltrim($path, '/'));
        }

        if (Str::startsWith($path, ['/images/', 'images/'])) {
            return asset(ltrim($path, '/'));
        }

        if (! Str::contains($path, '/') && file_exists(public_path("images/{$path}"))) {
            return asset("images/{$path}");
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    public static function placeholderImageUrl(): string
    {
        foreach (['placeholder.jpg', 'news-1.jpg', 'coming-soon-ad.webp'] as $filename) {
            if (file_exists(public_path("images/{$filename}"))) {
                return asset("images/{$filename}");
            }
        }

        return asset('images/news-1.jpg');
    }
}
