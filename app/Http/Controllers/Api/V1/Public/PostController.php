<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Api\V1\Public\PostListRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Support\CacheLock;
use App\Support\ViewCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostController extends BaseApiController
{
    public function index(PostListRequest $request)
    {
        $perPage = min((int) $request->get('limit', 15), 50);
        $cacheKey = 'v1:posts:' . md5(json_encode($request->all()));

        $posts = CacheLock::remember($cacheKey, 300, function () use ($request, $perPage) {
            $query = Post::withListRelations()->published();

            // Category filter
            if ($request->filled('category_slug')) {
                $slug = $request->category_slug;
                $query->where(function ($q) use ($slug) {
                    $q->whereHas('categories', fn ($cq) => $cq->where('slug', $slug))
                      ->orWhereHas('primaryCategory', fn ($cq) => $cq->where('slug', $slug));
                });
            }

            // Tag filter
            if ($request->filled('tag_slug')) {
                $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag_slug));
            }

            // Author filter
            if ($request->filled('author_id')) {
                $query->where('user_id', $request->author_id);
            }

            // Date range filters
            if ($request->filled('date_from')) {
                $query->whereDate('published_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('published_at', '<=', $request->date_to);
            }

            // Flag filters
            foreach (['is_breaking', 'is_featured', 'is_trending', 'is_editors_pick', 'is_sticky'] as $flag) {
                if ($request->has($flag)) {
                    $query->where($flag, filter_var($request->$flag, FILTER_VALIDATE_BOOLEAN));
                }
            }

            // Post format filter
            if ($request->filled('post_format')) {
                $query->where('post_format', $request->post_format);
            }

            // Search within results
            if ($request->filled('search')) {
                $s = $request->search;
                $query->where(function ($q) use ($s) {
                    $q->where('title', 'like', "%{$s}%")
                      ->orWhere('title_en', 'like', "%{$s}%")
                      ->orWhere('title_bn', 'like', "%{$s}%")
                      ->orWhere('excerpt', 'like', "%{$s}%");
                });
            }

            // Sorting
            $sortField = match ($request->get('sort', 'latest')) {
                'oldest' => 'published_at',
                'popular' => 'view_count',
                'title' => 'title',
                default => 'published_at',
            };
            $sortDir = $request->get('sort', 'latest') === 'oldest' ? 'asc' : 'desc';

            return $query->orderBy($sortField, $sortDir)
                ->latest('id')
                ->paginate($perPage);
        });

        return $this->paginated(PostResource::collection($posts));
    }

    public function show($slug)
    {
        $cacheKey = "v1:post:{$slug}";

        $post = CacheLock::rememberWithStale($cacheKey, 300, function () use ($slug) {
            return Post::withContentRelations()
                ->published()
                ->where('slug', $slug)
                ->first();
        });

        if (! $post) {
            return $this->error('Not Found', 'Post not found.', 404);
        }

        app(ViewCounter::class)->increment($post->id);

        return $this->success(new PostResource($post));
    }

    public function breaking()
    {
        $posts = CacheLock::remember('v1:posts:breaking', 120, function () {
            return Post::withListRelations()
                ->published()->breaking()
                ->latest('published_at')
                ->take(10)->get();
        });

        return $this->success(PostResource::collection($posts));
    }

    public function trending()
    {
        $posts = CacheLock::remember('v1:posts:trending', 120, function () {
            return Post::withListRelations()
                ->published()->trending()
                ->latest('published_at')
                ->take(10)->get();
        });

        return $this->success(PostResource::collection($posts));
    }

    public function popular()
    {
        $posts = CacheLock::remember('v1:posts:popular', 300, function () {
            return Post::withListRelations()
                ->published()->popular()
                ->take(10)->get();
        });

        return $this->success(PostResource::collection($posts));
    }

    public function featured()
    {
        $posts = CacheLock::remember('v1:posts:featured', 120, function () {
            return Post::withListRelations()
                ->published()->featured()
                ->latest('published_at')
                ->take(5)->get();
        });

        return $this->success(PostResource::collection($posts));
    }

    public function editorsPick()
    {
        $posts = CacheLock::remember('v1:posts:editors-pick', 120, function () {
            return Post::withListRelations()
                ->published()->editorsPick()
                ->latest('published_at')
                ->take(5)->get();
        });

        return $this->success(PostResource::collection($posts));
    }

    public function view(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        app(ViewCounter::class)->increment($post->id);

        Cache::forget("v1:post:{$post->slug}");

        return $this->success(['views' => $post->view_count + app(ViewCounter::class)->get($post->id)]);
    }
}
