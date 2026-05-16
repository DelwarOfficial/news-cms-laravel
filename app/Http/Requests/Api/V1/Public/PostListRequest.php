<?php

namespace App\Http\Requests\Api\V1\Public;

use Illuminate\Foundation\Http\FormRequest;

class PostListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'category_slug' => 'string|max:255',
            'tag_slug' => 'string|max:255',
            'author_id' => 'integer|exists:users,id',
            'post_format' => 'string|in:standard,video,gallery,live,opinion',
            'date_from' => 'date',
            'date_to' => 'date|after_or_equal:date_from',
            'sort' => 'string|in:latest,oldest,popular,title',
            'search' => 'string|max:255',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'is_editors_pick' => 'boolean',
            'is_sticky' => 'boolean',
        ];
    }
}
