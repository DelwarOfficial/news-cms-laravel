<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends BaseApiController
{
    public function index(Request $request)
    {
        $limit = min((int) $request->get('limit', 50), 100);

        $pages = Cache::remember('v1:pages', 3600, function () use ($limit) {
            return Page::where('status', 'published')
                ->orderBy('order')
                ->orderBy('title')
                ->take($limit)
                ->get();
        });

        return $this->success(PageResource::collection($pages));
    }

    public function show(string $slug)
    {
        $cacheKey = "v1:pages:{$slug}";

        $page = Cache::remember($cacheKey, 3600, function () use ($slug) {
            return Page::where('slug', $slug)
                ->where('status', 'published')
                ->first();
        });

        if (! $page) {
            return $this->error('Not Found', 'Page not found.', 404);
        }

        return $this->success(new PageResource($page));
    }
}
