<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Api\V1\Public\CategoryPostsRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Support\FrontendCache;

class CategoryController extends BaseApiController
{
    public function index()
    {
        $categories = FrontendCache::remember([FrontendCache::TAG_CONTENT, FrontendCache::TAG_CATEGORY_FEEDS], 'v1:categories', 3600, function () {
            $all = Category::with(Category::apiRelations())
                ->withCount('posts')
                ->where('status', 'active')
                ->orderBy('order')
                ->orderBy('name')
                ->get();

            $parents = $all->whereNull('parent_id')->values();
            $children = $all->whereNotNull('parent_id')->groupBy('parent_id');

            return $parents->map(function ($parent) use ($children) {
                $data = (new CategoryResource($parent))->toArray(request());
                $data['children'] = collect($children->get($parent->id, []))
                    ->map(fn ($c) => (new CategoryResource($c))->toArray(request()))
                    ->values()->all();
                return $data;
            })->values()->all();
        });

        return $this->success($categories);
    }

    public function posts(CategoryPostsRequest $request, $slug)
    {
        $category = Category::with(Category::apiRelations())->where('slug', $slug)->firstOrFail();
        $perPage = min((int) $request->get('limit', 15), 50);
        $cacheKey = "v1:categories:{$slug}:posts:" . md5(json_encode($request->all()));

        $posts = FrontendCache::remember([FrontendCache::TAG_CONTENT, FrontendCache::TAG_CATEGORY_FEEDS], $cacheKey, 1800, function () use ($category, $request, $perPage) {
            $query = Post::withContentRelations()
                ->published()
                ->where(function ($q) use ($category) {
                    $q->whereHas('categories', fn ($cq) => $cq->where('categories.id', $category->id))
                      ->orWhereHas('primaryCategory', fn ($cq) => $cq->where('id', $category->id));
                });

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

            return $query->latest('published_at')->latest('id')->paginate($perPage);
        });

        return $this->paginated(PostResource::collection($posts));
    }
}
