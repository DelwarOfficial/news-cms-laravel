<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'post_count' => $this->when($this->posts_count !== null, (int) $this->posts_count),
        ];
    }
}
