<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\Api\V1\PostResource;
use App\Http\Resources\Api\V1\TagResource;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TagController extends BaseApiController
{
    public function index()
    {
        $tags = Cache::remember('v1:tags', 3600, function () {
            return Tag::withCount('posts')
                ->where('status', 'published')
                ->orderBy('name')
                ->get();
        });

        return $this->success(TagResource::collection($tags));
    }

    public function show(string $slug)
    {
        $tag = Tag::where('slug', $slug)->where('status', 'published')->first();

        if (! $tag) {
            return $this->error('Not Found', 'Tag not found.', 404);
        }

        return $this->success(new TagResource($tag));
    }

    public function posts(Request $request, string $slug)
    {
        $tag = Tag::where('slug', $slug)->where('status', 'published')->first();

        if (! $tag) {
            return $this->error('Not Found', 'Tag not found.', 404);
        }

        $perPage = min((int) $request->get('limit', 15), 50);
        $cacheKey = "v1:tags:{$slug}:posts:" . md5(json_encode($request->all()));

        $posts = Cache::remember($cacheKey, 300, function () use ($tag, $request, $perPage) {
            $query = Post::withContentRelations()
                ->published()
                ->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id));

            if ($request->filled('date_from')) {
                $query->whereDate('published_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('published_at', '<=', $request->date_to);
            }

            foreach (['is_breaking', 'is_featured', 'is_trending', 'is_editors_pick', 'is_sticky'] as $flag) {
                if ($request->has($flag)) {
                    $query->where($flag, filter_var($request->$flag, FILTER_VALIDATE_BOOLEAN));
                }
            }

            return $query->latest('published_at')->latest('id')->paginate($perPage);
        });

        return $this->paginated(PostResource::collection($posts));
    }
}
