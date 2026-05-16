<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Requests\Api\V1\Public\SearchRequest;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class SearchController extends BaseApiController
{
    public function index(SearchRequest $request)
    {
        $q = trim((string) $request->get('q', ''));
        $perPage = min((int) $request->get('limit', 15), 50);

        if ($q === '') {
            return $this->success([]);
        }

        $cacheKey = 'v1:search:' . md5(strtolower($q) . ":{$perPage}");

        $posts = Cache::remember($cacheKey, 60, function () use ($q, $request, $perPage) {
            $query = Post::withContentRelations()
                ->published()
                ->where(function ($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                        ->orWhere('title_en', 'like', "%{$q}%")
                        ->orWhere('title_bn', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%");
                });

            // Optional category filter within search
            if ($request->filled('category_slug')) {
                $slug = $request->category_slug;
                $query->where(function ($q) use ($slug) {
                    $q->whereHas('categories', fn ($cq) => $cq->where('slug', $slug))
                      ->orWhereHas('primaryCategory', fn ($cq) => $cq->where('slug', $slug));
                });
            }

            // Flag filters
            foreach (['is_breaking', 'is_featured', 'is_trending'] as $flag) {
                if ($request->has($flag)) {
                    $query->where($flag, filter_var($request->$flag, FILTER_VALIDATE_BOOLEAN));
                }
            }

            // Date range
            if ($request->filled('date_from')) {
                $query->whereDate('published_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('published_at', '<=', $request->date_to);
            }

            return $query->latest('published_at')->latest('id')->paginate($perPage);
        });

        return $this->paginated(PostResource::collection($posts));
    }
}
