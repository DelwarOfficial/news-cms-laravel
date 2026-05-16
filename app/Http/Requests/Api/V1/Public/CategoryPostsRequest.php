<?php

namespace App\Http\Requests\Api\V1\Public;

use Illuminate\Foundation\Http\FormRequest;

class CategoryPostsRequest extends FormRequest
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
            'date_from' => 'date',
            'date_to' => 'date|after_or_equal:date_from',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'is_editors_pick' => 'boolean',
            'is_sticky' => 'boolean',
        ];
    }
}
