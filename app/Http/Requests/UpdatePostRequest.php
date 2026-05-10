<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:255|unique:posts,title,' . $this->post->id,
            'content' => 'required',
            'status' => 'required|in:draft,pending,published',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'excerpt' => 'nullable|max:500',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
            'featured_image' => 'nullable|image|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Post title is required.',
            'title.unique' => 'A post with this title already exists.',
            'content.required' => 'Post content is required.',
        ];
    }
}
