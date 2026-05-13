<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use Illuminate\Http\JsonResponse;

class SitemapController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $urls = [];

        $posts = Post::published()->latest('published_at')->get(['slug', 'updated_at', 'published_at']);

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => route('article.show', $post->slug),
                'lastmod' => $post->updated_at?->toIso8601String()
                    ?? $post->published_at?->toIso8601String()
                    ?? now()->toIso8601String(),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ];
        }

        return $this->success($urls);
    }
}
