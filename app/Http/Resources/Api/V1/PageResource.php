<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        $result = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'template' => $this->template,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'order' => $this->order,
            'published_at' => $this->created_at?->toIso8601String(),
        ];

        if ($request?->route()?->getName() === 'v1.pages.show') {
            $result['content'] = $this->content;
        }

        return $result;
    }
}
