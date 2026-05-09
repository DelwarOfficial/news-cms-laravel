<?php

namespace App\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'published_at' => $this->published_at,
            'reading_time' => $this->reading_time,
            'author' => $this->author ? $this->author->name : null,
            'categories' => $this->categories->pluck('name'),
            'view_count' => $this->view_count,
        ];
    }
}