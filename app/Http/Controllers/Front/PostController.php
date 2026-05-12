<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\ArticleFeed;
use App\Support\FallbackDataService;
use App\Services\PopularNewsService;
use App\Services\RelatedArticleService;

class PostController extends Controller
{
    public function __construct(
        private readonly PopularNewsService $popularNews,
        private readonly RelatedArticleService $relatedArticles,
    ) {
    }

    public function show(string $slug)
    {
        $fallbackArticles = FallbackDataService::getArticles();
        $article = ArticleFeed::findArticle($slug, $fallbackArticles);

        if (!$article) {
            abort(404);
        }

        if (! empty($article['id'])) {
            Post::query()->find($article['id'])?->incrementViews();
        }

        $relatedArticles = $this->relatedArticles->forArticle($article);
        $popularNews = $this->popularNews->get(5, array_filter([(int) ($article['id'] ?? 0)]));

        return view('pages.article', compact(
            'article',
            'relatedArticles',
            'popularNews',
        ));
    }

    public function incrementView(Post $post)
    {
        abort_unless($post->status === 'published', 404);

        $post->incrementViews();

        return response()->json([
            'views' => (int) $post->refresh()->view_count,
        ]);
    }
}
