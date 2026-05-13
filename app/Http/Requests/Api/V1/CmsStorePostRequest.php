<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CmsStorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required_without:title_en|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'title_bn' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:500',
            'slug_en' => 'nullable|string|max:500',
            'slug_bn' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'body_en' => 'nullable|string',
            'body_bn' => 'nullable|string',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:5000',
            'shoulder' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,pending,published,scheduled,archived',
            'post_format' => 'nullable|in:standard,video,gallery,opinion,live',
            'category_slug' => 'nullable|string|max:255',
            'category_name' => 'nullable|string|max:255',
            'tag_names' => 'nullable|array',
            'tag_names.*' => 'string|max:255',
            'featured_image_url' => 'nullable|url|max:2000',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:170',
            'seo_title' => 'nullable|string|max:70',
            'seo_description' => 'nullable|string|max:170',
            'canonical_url' => 'nullable|url|max:500',
            'source_url' => 'nullable|url|max:500',
            'source_name' => 'nullable|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
            'district_id' => 'nullable|exists:districts,id',
            'upazila_id' => 'nullable|exists:upazilas,id',
            'is_breaking' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'is_editors_pick' => 'nullable|boolean',
            'is_sticky' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'show_author' => 'nullable|boolean',
            'show_publish_date' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'scheduled_at' => 'nullable|date',
            'raw_import_payload' => 'nullable|array',
        ];
    }
}
