<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagApiController extends Controller
{
    public function posts(Request $request, $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        $posts = $tag->posts()
            ->with(['author:id,name', 'categories:id,name,slug'])
            ->published()
            ->latest()
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'tag' => $tag,
            'data' => $posts
        ]);
    }
}
