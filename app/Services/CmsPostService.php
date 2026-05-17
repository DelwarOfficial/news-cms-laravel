<?php

namespace App\Services;

use App\Jobs\ProcessPostPublishing;
use App\Models\Category;
use App\Models\Language;
use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use App\Support\FileUploadSecurity;
use App\Support\FrontendCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsPostService
{
    public function create(array $data, int $userId): Post
    {
        // Resolve categorySlug → Category ID
        $category = $this->resolveCategory($data);
        $data['primary_category_id'] = $category->id;

        // Auto-create tags from tagNames[]
        $tagIds = $this->resolveTags($data['tag_names'] ?? []);
        unset($data['tag_names']);

        // Download featuredImageUrl → Media record
        if (! empty($data['featured_image_url'])) {
            $media = $this->downloadImage($data['featured_image_url'], $userId);
            if ($media) {
                $data['featured_media_id'] = $media->id;
            }
            unset($data['featured_image_url']);
        }

        // Store entire original payload for audit
        $data['raw_import_payload'] = $data['raw_import_payload'] ?? [];

        // Set author from API key owner
        $data['author_id'] ??= $userId;

        // Build title / slug fallbacks
        $title = $data['title'] ?? ($data['title_en'] ?? 'Untitled CMS Import');
        $data['title'] = $title;
        $data['slug'] ??= Str::slug($title);

        // Default status
        $data['status'] ??= 'draft';
        $data['post_format'] ??= 'standard';
        $data['language_id'] ??= Language::query()->value('id') ?? 1;

        if (empty($data['published_at']) && $data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $post = Post::create([
            'user_id' => $userId,
            'author_id' => $data['author_id'],
            'language_id' => $data['language_id'],
            'title' => $data['title'],
            'title_en' => $data['title_en'] ?? null,
            'title_bn' => $data['title_bn'] ?? null,
            'slug' => $this->uniqueSlug($data['slug']),
            'slug_en' => ! empty($data['slug_en']) ? $this->uniqueSlug($data['slug_en'], 'slug_en') : null,
            'slug_bn' => ! empty($data['slug_bn']) ? $this->uniqueSlug($data['slug_bn'], 'slug_bn') : null,
            'content' => $data['body'] ?? ($data['content'] ?? ''),
            'body_en' => $data['body_en'] ?? null,
            'body_bn' => $data['body_bn'] ?? null,
            'excerpt' => $data['excerpt'] ?? Str::limit(strip_tags($data['body'] ?? $data['content'] ?? ''), 500),
            'shoulder' => $data['shoulder'] ?? null,
            'status' => $data['status'],
            'post_format' => $data['post_format'],
            'primary_category_id' => $data['primary_category_id'],
            'featured_media_id' => $data['featured_media_id'] ?? null,
            'meta_title' => $data['meta_title'] ?? $data['seo_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? $data['seo_description'] ?? null,
            'canonical_url' => $data['canonical_url'] ?? null,
            'source_url' => $data['source_url'] ?? null,
            'source_name' => $data['source_name'] ?? null,
            'division_id' => $data['division_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'upazila_id' => $data['upazila_id'] ?? null,
            'is_breaking' => filter_var($data['is_breaking'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_featured' => filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_trending' => filter_var($data['is_trending'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_editors_pick' => filter_var($data['is_editors_pick'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'is_sticky' => filter_var($data['is_sticky'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'show_author' => filter_var($data['show_author'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'show_publish_date' => filter_var($data['show_publish_date'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'published_at' => $data['published_at'] ?? null,
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'raw_import_payload' => $data['raw_import_payload'],
        ]);

        $post->categories()->sync([$category->id => ['is_primary' => true]]);
        $post->tags()->sync($tagIds);

        if (($data['status'] ?? null) === 'published') {
            ProcessPostPublishing::dispatch($post)->onQueue('publishing');
        }

        FrontendCache::flushContent();

        return $post;
    }

    public function update(Post $post, array $data): Post
    {
        $updatable = [];

        if (! empty($data['category_slug'])) {
            $category = $this->resolveCategory($data);
            $updatable['primary_category_id'] = $category->id;
            $data['primary_category_id'] = $category->id;
        }

        if (isset($data['tag_names'])) {
            $tagIds = $this->resolveTags($data['tag_names']);
            $post->tags()->sync($tagIds);
        }

        if (! empty($data['featured_image_url'])) {
            $media = $this->downloadImage($data['featured_image_url'], $post->user_id);
            if ($media) {
                $updatable['featured_media_id'] = $media->id;
            }
        }

        foreach ([
            'title', 'title_en', 'title_bn', 'slug', 'slug_en', 'slug_bn',
            'content', 'body_en', 'body_bn', 'excerpt', 'shoulder',
            'status', 'post_format', 'meta_title', 'meta_description',
            'canonical_url', 'source_url', 'source_name',
            'division_id', 'district_id', 'upazila_id',
        ] as $field) {
            if (isset($data[$field])) {
                $updatable[$field] = $data[$field];
            }
        }

        foreach (['is_breaking','is_featured','is_trending','is_editors_pick','is_sticky','show_author','show_publish_date'] as $flag) {
            if (isset($data[$flag])) {
                $updatable[$flag] = filter_var($data[$flag], FILTER_VALIDATE_BOOLEAN);
            }
        }

        if (! empty($data['status']) && $data['status'] === 'published' && ! $post->published_at) {
            $updatable['published_at'] = now();
        }

        $updatable['raw_import_payload'] = array_merge(
            $post->raw_import_payload ?? [],
            ['updated_from_cms' => now()->toIso8601String()],
        );

        $oldStatus = $post->status;
        $post->update($updatable);

        if (isset($data['primary_category_id'])) {
            $post->categories()->sync([$data['primary_category_id'] => ['is_primary' => true]]);
        }

        $newStatus = $data['status'] ?? $oldStatus;
        if ($newStatus === 'published' && $oldStatus !== 'published') {
            ProcessPostPublishing::dispatch($post)->onQueue('publishing');
        }

        FrontendCache::flushContent();

        return $post;
    }

    public function destroy(Post $post): void
    {
        $post->update(['status' => 'archived']);
        $post->delete();
        FrontendCache::flushContent();
    }

    public function resolveCategory(array $data): Category
    {
        $slug = $data['category_slug'] ?? null;
        $name = $data['category_name'] ?? null;

        if ($slug) {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                return $category;
            }
        }

        if ($name) {
            $category = Category::where('name', $name)->first();
            if ($category) {
                return $category;
            }
        }

        if ($slug) {
            return Category::create([
                'name' => $name ?? Str::title(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'status' => 'active',
            ]);
        }

        // Fallback to first active category
        return Category::where('status', 'active')->firstOrFail();
    }

    public function resolveTags(array $tagNames): array
    {
        $ids = [];
        foreach ($tagNames as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $tag = Tag::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)],
            );
            $ids[] = $tag->id;
        }
        return $ids;
    }

    public function downloadImage(string $url, int $userId): ?Media
    {
        try {
            $parsed = parse_url($url);
            if (! in_array($parsed['scheme'] ?? '', ['http', 'https'], true)) {
                return null;
            }

            $response = Http::timeout(15)->get($url);
            if (! $response->successful()) {
                return null;
            }

            $body = $response->body();
            $contentType = strtolower(trim(explode(';', (string) $response->header('Content-Type'))[0]));
            if (! in_array($contentType, FileUploadSecurity::IMAGE_MIMES, true)) {
                return null;
            }

            if (strlen($body) > FileUploadSecurity::MAX_UPLOAD_KB * 1024) {
                return null;
            }

            $extension = $this->guessExtension($url, $contentType);
            $fileName = Str::uuid() . '.' . $extension;
            $path = 'media/' . $fileName;

            Storage::disk('public')->put($path, $body);

            return Media::create([
                'user_id' => $userId,
                'name' => basename(parse_url($url, PHP_URL_PATH) ?: $fileName),
                'file_name' => $fileName,
                'file_path' => $path,
                'file_url' => Storage::url($path),
                'file_type' => $contentType,
                'file_size' => strlen($body),
            ]);
        } catch (\Throwable $e) {
            \Log::warning("CMS image download failed: {$url}", ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function guessExtension(string $url, ?string $contentType): string
    {
        $ext = pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION);
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'], true)) {
            return $ext;
        }
        return match ($contentType) {
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };
    }

    private function uniqueSlug(string $slug, string $column = 'slug'): string
    {
        $candidate = $slug;
        $i = 2;
        while (Post::where($column, $candidate)->exists()) {
            $candidate = $slug . '-' . $i++;
        }
        return $candidate;
    }
}
