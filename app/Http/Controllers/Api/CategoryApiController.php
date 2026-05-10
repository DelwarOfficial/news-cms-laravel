<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('posts')
            ->where('status', 'active')
            ->where('show_in_menu', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function posts(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $posts = $category->posts()
            ->with(['author:id,name', 'categories:id,name,slug'])
            ->where('status', 'published')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'category' => $category,
            'data' => $posts
        ]);
    }
}
