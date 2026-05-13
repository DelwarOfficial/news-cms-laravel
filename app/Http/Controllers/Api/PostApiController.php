<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Resources\Api\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostApiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);

        $posts = Post::withContentRelations()
            ->published()
            ->latest('published_at')
            ->latest('id')
            ->paginate(min($perPage, 50));

        return PostResource::collection($posts);
    }

    public function show($slug)
    {
        $post = Post::withContentRelations()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $post->increment('view_count');

        return new PostResource($post);
    }

    public function breaking()
    {
        $posts = Post::withContentRelations()
            ->published()
            ->breaking()
            ->latest('published_at')
            ->take(10)
            ->get();

        return PostResource::collection($posts);
    }

    public function trending()
    {
        $posts = Post::withContentRelations()
            ->published()
            ->trending()
            ->latest('published_at')
            ->take(10)
            ->get();

        return PostResource::collection($posts);
    }

    public function popular()
    {
        $posts = Post::withContentRelations()
            ->published()
            ->popular()
            ->take(10)
            ->get();

        return PostResource::collection($posts);
    }

    public function view(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->incrementViews();

        return response()->json(['status' => 'success', 'views' => $post->view_count]);
    }

    public function featured()
    {
        $posts = Post::withContentRelations()
            ->published()
            ->featured()
            ->latest('published_at')
            ->take(5)
            ->get();

        return PostResource::collection($posts);
    }
}