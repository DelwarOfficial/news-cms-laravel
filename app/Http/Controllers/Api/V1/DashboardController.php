<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Media;

class DashboardController extends BaseApiController
{
    public function index()
    {
        return $this->success([
            'total_posts' => Post::count(),
            'published_posts' => Post::where('status', 'published')->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
            'total_media' => Media::count(),
        ]);
    }
}
