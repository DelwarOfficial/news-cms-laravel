<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'parent_id' => $this->parent_id,
            'author_name' => $this->author_name,
            'content' => $this->content,
            'created_at' => $this->created_at?->toIso8601String(),
            'replies' => $this->when($this->relationLoaded('replies') && $this->replies->isNotEmpty(), function () {
                return self::collection($this->replies);
            }),
        ];
    }
}
