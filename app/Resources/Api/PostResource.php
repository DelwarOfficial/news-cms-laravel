<?php

namespace App\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        $imageUrl = null;
        if ($this->relationLoaded('featuredMedia') && $this->featuredMedia) {
            $imageUrl = $this->featuredMedia->file_url ?: $this->featuredMedia->url;
        }
        $imageUrl ??= $this->featured_image ? url('storage/' . ltrim($this->featured_image, '/')) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'image_url' => $imageUrl,
            'published_at' => $this->published_at,
            'reading_time' => (int) ($this->reading_time ?? 1),
            'view_count' => (int) ($this->view_count ?? 0),
            'is_breaking' => (bool) ($this->is_breaking ?? false),
            'is_featured' => (bool) ($this->is_featured ?? false),
            'is_trending' => (bool) ($this->is_trending ?? false),
            'category' => $this->primaryCategory?->name ?? $this->categories->first()?->name,
            'category_slug' => $this->primaryCategory?->slug ?? $this->categories->first()?->slug,
            'author' => $this->author?->name,
            'author_username' => $this->author?->username,
            'author_avatar' => $this->author?->avatar,
            'tags' => $this->relationLoaded('tags') ? $this->tags->pluck('name')->values()->all() : [],
        ];
    }
}