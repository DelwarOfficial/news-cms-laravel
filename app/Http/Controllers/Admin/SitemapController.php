<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateSitemapJob;
use App\Models\Post;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function generate(Request $request)
    {
        $this->authorize('update', Post::class);

        GenerateSitemapJob::dispatch(auth()->id());

        return back()->with('success', 'Sitemap generation queued successfully! File: sitemap.xml');
    }
}
