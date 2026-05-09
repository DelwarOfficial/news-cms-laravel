<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $posts = Post::whereHas('categories', function($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->published()
            ->latest()
            ->paginate(12);

        return view('front.category', compact('category', 'posts'));
    }
}