<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    private ?string $locale = null;

    public function locale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function toArray($request)
    {
        $locale = $this->locale ?? app()->getLocale();

        $imageUrl = null;
        if ($this->relationLoaded('featuredMedia') && $this->featuredMedia) {
            $imageUrl = $this->featuredMedia->file_url ?: $this->featuredMedia->url;
        }
        $imageUrl ??= $this->featured_image
            ? url('storage/' . ltrim($this->featured_image, '/'))
            : null;

        $categoryNameBn = null;
        $categorySlug = null;
        $primaryCat = $this->relationLoaded('primaryCategory') ? $this->primaryCategory : null;
        $cats = $this->relationLoaded('categories') ? $this->categories : collect();

        if ($primaryCat) {
            $categoryNameBn = $primaryCat->name_bn ?? $primaryCat->name;
            $categorySlug = $primaryCat->slug;
        } elseif ($cats->isNotEmpty()) {
            $primary = $cats->firstWhere('pivot.is_primary', true) ?? $cats->first();
            $categoryNameBn = $primary->name_bn ?? $primary->name;
            $categorySlug = $primary->slug;
        }

        $authorName = $this->bylineAuthor?->name ?? $this->author?->name;

        $publishedAt = $this->published_at;

        $result = [
            'id' => $this->id,
            'title' => $this->titleForLocale($locale),
            'title_bn' => $this->title_bn,
            'title_en' => $this->title_en,
            'slug' => $this->slug,
            'shoulder' => $this->shoulder,
            'excerpt' => $this->excerpt,
            'image_url' => $imageUrl,
            'image_alt' => $this->featured_image_alt ?: $this->title,
            'image_caption' => $this->featured_image_caption,
            'published_at' => $publishedAt,
            'reading_time' => (int) ($this->reading_time ?? 1),
            'view_count' => (int) ($this->view_count ?? 0),
            'is_breaking' => (bool) ($this->is_breaking ?? false),
            'is_breaking_news' => (bool) ($this->is_breaking_news ?? false),
            'is_featured' => (bool) ($this->is_featured ?? false),
            'is_trending' => (bool) ($this->is_trending ?? false),
            'is_editors_pick' => (bool) ($this->is_editors_pick ?? false),
            'is_sticky' => (bool) ($this->is_sticky ?? false),
            'is_photocard' => (bool) ($this->is_photocard ?? false),
            'post_format' => $this->post_format ?? 'standard',
            'category_name_bn' => $categoryNameBn,
            'category_slug' => $categorySlug,
            'category' => [
                'name_bn' => $categoryNameBn,
                'name_en' => $this->primaryCategory?->name_en,
                'slug' => $categorySlug,
            ],
            'author_name' => $authorName,
            'author_username' => $this->author?->username,
            'author_avatar' => $this->author?->avatar,
            'tags' => $this->relationLoaded('tags')
                ? $this->tags->map(fn ($t) => ['name' => $t->name, 'slug' => $t->slug])->values()->all()
                : [],
            'meta_title' => $this->metaTitleForLocale($locale),
            'meta_title_bn' => $this->meta_title_bn,
            'meta_title_en' => $this->meta_title_en,
            'meta_description' => $this->metaDescriptionForLocale($locale),
            'meta_description_bn' => $this->meta_description_bn,
            'meta_description_en' => $this->meta_description_en,
            'canonical_url' => $this->canonical_url,
            'og_image' => $this->og_image,
            'locale' => $locale,
        ];

        if ($request?->route()?->getName() === 'v1.posts.show') {
            $bodyHtml = $this->bodyHtmlForLocale($locale);
            $result['body'] = collect(preg_split('/\R{2,}/', strip_tags($bodyHtml)))
                ->map(fn (string $p) => trim($p))
                ->filter()
                ->values()
                ->all();
            $result['location'] = $this->districtLocation?->name_bangla
                ?? $this->districtLocation?->name;
            $result['division_id'] = $this->division_id;
            $result['district_id'] = $this->district_id;
            $result['upazila_id'] = $this->upazila_id;
        }

        return $result;
    }
}
