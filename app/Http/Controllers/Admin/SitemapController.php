<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;
use App\Models\Category;

class SitemapController extends Controller
{
    public function generate()
    {
        $sitemap = Sitemap::create();

        // Homepage
        $sitemap->add(Url::create(route('home'))->setPriority(1.0));

        // Posts
        Post::published()->get()->each(function ($post) use ($sitemap) {
            $sitemap->add(
                Url::create(route('post.show', $post->slug))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.8)
            );
        });

        // Categories
        Category::all()->each(function ($category) use ($sitemap) {
            $sitemap->add(
                Url::create(route('category.show', $category->slug))
                    ->setPriority(0.6)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        return back()->with('success', 'Sitemap generated successfully!');
    }
}