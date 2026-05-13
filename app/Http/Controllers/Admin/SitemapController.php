<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    public function generate(Request $request)
    {
        $this->authorize('update', Post::class);
        
        try {
            $sitemap = Sitemap::create();

            // Homepage
            $sitemap->add(Url::create(route('home'))->setPriority(1.0));

            // Posts
            Post::published()->get()->each(function ($post) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('article.show', $post->slug))
                        ->setLastModificationDate($post->updated_at)
                        ->setPriority(0.8)
                );
            });

            // Categories
            Category::all()->each(function ($category) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('category.show', $category->slug))
                        ->setLastModificationDate($category->updated_at)
                        ->setPriority(0.6)
                );
            });

            $sitemapPath = public_path('sitemap.xml');
            $sitemap->writeToFile($sitemapPath);

            Log::info('Sitemap generated successfully', [
                'file' => $sitemapPath,
                'user_id' => auth()->id()
            ]);

            return back()->with('success', 'Sitemap generated successfully! File: sitemap.xml');
        } catch (\Exception $e) {
            Log::error('Sitemap generation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to generate sitemap: ' . $e->getMessage());
        }
    }
}
