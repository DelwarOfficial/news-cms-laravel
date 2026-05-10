<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function show($slug)
    {
        $post = Post::with(['author', 'categories'])
            ->withRichText(['body_en', 'body_bn', 'summary_en', 'summary_bn'])
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->orWhere('slug_en', $slug)
                    ->orWhere('slug_bn', $slug);
            })
            ->firstOrFail();

        $post->increment('view_count');
        return view('front.post', compact('post'));
    }
}
