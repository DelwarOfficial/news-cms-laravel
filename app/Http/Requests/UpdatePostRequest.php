<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locale = app()->getLocale();
        $otherLocale = $locale === 'en' ? 'bn' : 'en';
        $postId = $this->route('post')?->id ?? $this->post?->id;

        return [
            "title_{$locale}" => ['required', 'string', 'max:500'],
            "title_{$otherLocale}" => ['nullable', 'string', 'max:500'],
            "slug_{$locale}" => ['nullable', 'string', 'max:500', Rule::unique('posts', "slug_{$locale}")->ignore($postId)],
            "slug_{$otherLocale}" => ['nullable', 'string', 'max:500', Rule::unique('posts', "slug_{$otherLocale}")->ignore($postId)],
            "body_{$locale}" => ['required', 'string'],
            "body_{$otherLocale}" => ['nullable', 'string'],
            "summary_{$locale}" => ['nullable', 'string', 'max:5000'],
            "summary_{$otherLocale}" => ['nullable', 'string', 'max:5000'],
            'shoulder' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,pending,published,scheduled'],
            'visibility' => ['required', 'in:public,private'],
            'scheduled_at' => ['nullable', 'date', 'after:now', 'required_if:status,scheduled'],
            'published_at' => ['nullable', 'date'],
            'category_id' => ['required', 'exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id'],
            'author_id' => ['nullable', 'exists:users,id'],
            'post_format' => ['required', 'in:standard,video,gallery,opinion,live'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:170'],
            "meta_title_{$locale}" => ['nullable', 'string', 'max:70'],
            "meta_title_{$otherLocale}" => ['nullable', 'string', 'max:70'],
            "meta_description_{$locale}" => ['nullable', 'string', 'max:170'],
            "meta_description_{$otherLocale}" => ['nullable', 'string', 'max:170'],
            'canonical_url' => ['nullable', 'url', 'max:500'],
            'featured_media_id' => ['nullable', 'exists:media,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'featured_image_caption' => ['nullable', 'string', 'max:500'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'exists:upazilas,id'],
            'is_breaking' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
            'is_editors_pick' => ['nullable', 'boolean'],
            'is_sticky' => ['nullable', 'boolean'],
            'is_photocard' => ['nullable', 'boolean'],
            'allow_comments' => ['nullable', 'boolean'],
            'show_author' => ['nullable', 'boolean'],
            'show_publish_date' => ['nullable', 'boolean'],
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
