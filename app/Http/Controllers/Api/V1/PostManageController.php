<?php

namespace App\Http\Controllers\Api\V1;

use App\Jobs\ProcessPostPublishing;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Models\Category;
use App\Models\Language;
use App\Models\Tag;
use App\Support\FrontendCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostManageController extends BaseApiController
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->get('limit', 15), 50);

        $query = Post::withContentRelations();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $request->category_id));
        }
        if ($request->filled('author_id')) {
            $query->where('user_id', $request->author_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('title_en', 'like', "%{$s}%")
                  ->orWhere('title_bn', 'like', "%{$s}%");
            });
        }

        $posts = $query->latest('id')->paginate($perPage);

        return $this->paginated(PostResource::collection($posts));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'title_bn' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:500|unique:posts,slug',
            'slug_en' => 'nullable|string|max:500|unique:posts,slug_en',
            'slug_bn' => 'nullable|string|max:500|unique:posts,slug_bn',
            'content' => 'required|string',
            'body_en' => 'nullable|string',
            'body_bn' => 'nullable|string',
            'excerpt' => 'nullable|string|max:5000',
            'shoulder' => 'nullable|string|max:255',
            'status' => 'required|in:draft,pending,published,scheduled',
            'scheduled_at' => 'nullable|date|after:now|required_if:status,scheduled',
            'published_at' => 'nullable|date',
            'category_id' => 'required|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
            'author_id' => 'nullable|exists:users,id',
            'post_format' => 'sometimes|in:standard,video,gallery,opinion,live',
            'featured_media_id' => 'nullable|exists:media,id',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:170',
            'canonical_url' => 'nullable|url|max:500',
            'division_id' => 'nullable|exists:divisions,id',
            'district_id' => 'nullable|exists:districts,id',
            'upazila_id' => 'nullable|exists:upazilas,id',
            'is_breaking' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'is_editors_pick' => 'nullable|boolean',
            'is_sticky' => 'nullable|boolean',
            'is_photocard' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'show_author' => 'nullable|boolean',
            'show_publish_date' => 'nullable|boolean',
        ]);

        $userId = $request->get('api_key_owner') ?: 1;

        $post = Post::create([
            'user_id' => $userId,
            'author_id' => $validated['author_id'] ?? $userId,
            'language_id' => $validated['language_id'] ?? Language::idForLocale(app()->getLocale()) ?? 1,
            'title' => $validated['title'],
            'title_en' => $validated['title_en'] ?? null,
            'title_bn' => $validated['title_bn'] ?? null,
            'slug' => $this->slugOrGenerate($validated['slug'] ?? null, $validated['title']),
            'slug_en' => $this->slugOrGenerate($validated['slug_en'] ?? null, $validated['title_en'] ?? $validated['title']),
            'slug_bn' => $this->slugOrGenerate($validated['slug_bn'] ?? null, $validated['title_bn'] ?? $validated['title']),
            'content' => $validated['content'],
            'body_en' => $validated['body_en'] ?? null,
            'body_bn' => $validated['body_bn'] ?? null,
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 500),
            'shoulder' => $validated['shoulder'] ?? null,
            'status' => $validated['status'],
            'post_format' => $validated['post_format'] ?? 'standard',
            'primary_category_id' => $validated['category_id'],
            'featured_media_id' => $validated['featured_media_id'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'division_id' => $validated['division_id'] ?? null,
            'district_id' => $validated['district_id'] ?? null,
            'upazila_id' => $validated['upazila_id'] ?? null,
            'is_breaking' => $request->boolean('is_breaking', false),
            'is_featured' => $request->boolean('is_featured', false),
            'is_trending' => $request->boolean('is_trending', false),
            'is_editors_pick' => $request->boolean('is_editors_pick', false),
            'is_sticky' => $request->boolean('is_sticky', false),
            'is_photocard' => $request->boolean('is_photocard', false),
            'allow_comments' => $request->boolean('allow_comments', true),
            'show_author' => $request->boolean('show_author', true),
            'show_publish_date' => $request->boolean('show_publish_date', true),
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? now()) : null,
            'scheduled_at' => $validated['status'] === 'scheduled'
                ? $validated['scheduled_at'] : null,
        ]);

        $post->categories()->sync([$validated['category_id'] => ['is_primary' => true]]);
        $post->tags()->sync($validated['tag_ids'] ?? []);

        if ($validated['status'] === 'published') {
            ProcessPostPublishing::dispatch($post)->onQueue('publishing');
        }

        FrontendCache::flushContent();

        $post->load(Post::contentRelations());
        return $this->created(new PostResource($post));
    }

    public function show($id)
    {
        $post = Post::withContentRelations()->findOrFail($id);
        return $this->success(new PostResource($post));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:500',
            'title_en' => 'nullable|string|max:500',
            'title_bn' => 'nullable|string|max:500',
            'slug' => 'nullable|string|max:500|unique:posts,slug,' . $id,
            'slug_en' => 'nullable|string|max:500|unique:posts,slug_en,' . $id,
            'slug_bn' => 'nullable|string|max:500|unique:posts,slug_bn,' . $id,
            'content' => 'sometimes|string',
            'body_en' => 'nullable|string',
            'body_bn' => 'nullable|string',
            'excerpt' => 'nullable|string|max:5000',
            'shoulder' => 'nullable|string|max:255',
            'status' => 'sometimes|in:draft,pending,published,scheduled',
            'scheduled_at' => 'nullable|date|after:now|required_if:status,scheduled',
            'published_at' => 'nullable|date',
            'category_id' => 'sometimes|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id',
            'author_id' => 'nullable|exists:users,id',
            'post_format' => 'sometimes|in:standard,video,gallery,opinion,live',
            'featured_media_id' => 'nullable|exists:media,id',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:170',
            'canonical_url' => 'nullable|url|max:500',
            'division_id' => 'nullable|exists:divisions,id',
            'district_id' => 'nullable|exists:districts,id',
            'upazila_id' => 'nullable|exists:upazilas,id',
            'is_breaking' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'is_editors_pick' => 'nullable|boolean',
            'is_sticky' => 'nullable|boolean',
            'is_photocard' => 'nullable|boolean',
            'allow_comments' => 'nullable|boolean',
            'show_author' => 'nullable|boolean',
            'show_publish_date' => 'nullable|boolean',
        ]);

        $updateData = collect($validated)->except(['tag_ids', 'category_id'])->toArray();

        if (isset($validated['slug'])) {
            $updateData['slug'] = $validated['slug'];
        } elseif (isset($validated['title']) && ! $post->slug) {
            $updateData['slug'] = Str::slug($validated['title']);
        }

        if (isset($validated['category_id'])) {
            $updateData['primary_category_id'] = $validated['category_id'];
        }

        foreach (['is_breaking','is_featured','is_trending','is_editors_pick','is_sticky','is_photocard','allow_comments','show_author','show_publish_date'] as $boolField) {
            if ($request->has($boolField)) {
                $updateData[$boolField] = $request->boolean($boolField);
            }
        }

        if (isset($validated['status'])) {
            if ($validated['status'] === 'published' && ! $post->published_at) {
                $updateData['published_at'] = $validated['published_at'] ?? now();
                $updateData['scheduled_at'] = null;
            } elseif ($validated['status'] === 'scheduled' && isset($validated['scheduled_at'])) {
                $updateData['published_at'] = null;
                $updateData['scheduled_at'] = $validated['scheduled_at'];
            }
        }

        $oldStatus = $post->status;
        $post->update($updateData);

        if (isset($validated['category_id'])) {
            $post->categories()->sync([$validated['category_id'] => ['is_primary' => true]]);
        }
        if (isset($validated['tag_ids'])) {
            $post->tags()->sync($validated['tag_ids']);
        }

        if (isset($validated['status']) && $validated['status'] === 'published' && $oldStatus !== 'published') {
            ProcessPostPublishing::dispatch($post)->onQueue('publishing');
        }

        FrontendCache::flushContent();

        return $this->success(new PostResource($post->load(Post::contentRelations())));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        FrontendCache::flushContent();

        return $this->noContent();
    }

    private function slugOrGenerate(?string $slug, string $title): string
    {
        if ($slug) {
            return Str::slug($slug);
        }
        $base = Str::slug($title);
        $candidate = $base;
        $i = 2;
        while (Post::where('slug', $candidate)->exists()) {
            $candidate = $base . '-' . $i++;
        }
        return $candidate;
    }
}
