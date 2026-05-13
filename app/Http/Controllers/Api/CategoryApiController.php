<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Resources\Api\PostResource;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('parent')
            ->where('status', 'active')
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'parent_id', 'description', 'color', 'icon']);

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function posts(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $perPage = (int) $request->get('per_page', 15);

        $posts = Post::withContentRelations()
            ->published()
            ->where(function ($q) use ($category) {
                $q->whereHas('categories', fn ($cq) => $cq->where('categories.id', $category->id))
                  ->orWhereHas('primaryCategory', fn ($cq) => $cq->where('id', $category->id));
            })
            ->latest('published_at')
            ->paginate(min($perPage, 50));

        return response()->json([
            'status' => 'success',
            'category' => ['id' => $category->id, 'name' => $category->name, 'slug' => $category->slug],
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }
}
