<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostApiController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);

        $posts = Post::with(['author:id,name', 'categories:id,name,slug'])
            ->published()
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    public function show($slug)
    {
        $post = Post::with(['author:id,name', 'categories:id,name,slug', 'tags:id,name,slug'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('view_count');

        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }

    public function trending()
    {
        $posts = Post::with(['author:id,name', 'categories:id,name,slug'])
            ->published()
            ->where('is_trending', true)
            ->latest()
            ->take(10)
            ->get();

        return response()->json(['status' => 'success', 'data' => $posts]);
    }

    public function breaking()
    {
        $posts = Post::with(['author:id,name', 'categories:id,name,slug'])
            ->published()
            ->where('is_breaking', true)
            ->latest()
            ->take(5)
            ->get();

        return response()->json(['status' => 'success', 'data' => $posts]);
    }

    public function featured()
    {
        $posts = Post::with(['author:id,name', 'categories:id,name,slug'])
            ->published()
            ->where('is_featured', true)
            ->latest()
            ->take(5)
            ->get();

        return response()->json(['status' => 'success', 'data' => $posts]);
    }
}