<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\ArticleFeed;
use App\Support\FallbackDataService;
use App\Services\PopularNewsService;
use App\Services\RelatedArticleService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private readonly PopularNewsService $popularNews,
        private readonly RelatedArticleService $relatedArticles,
    ) {
    }

    public function show(Request $request, string $slug)
    {
        $fallbackArticles = FallbackDataService::getArticles();
        $article = ArticleFeed::findArticle($slug, $fallbackArticles);

        if (!$article) {
            abort(404);
        }

        $canonicalUrl = route('article.show', $article['slug']);
        $ampUrl = route('article.amp', $article['slug']);

        if (! $request->routeIs('article.show')) {
            return redirect($canonicalUrl, 301);
        }

        if (! empty($article['id'])) {
            Post::query()->find($article['id'])?->incrementViews();
        }

        $relatedArticles = $this->relatedArticles->forArticle($article);
        $popularNews = $this->popularNews->get(5, array_filter([(int) ($article['id'] ?? 0)]));
        $metaTitle = $article['meta_title'] ?? $article['title'];
        $metaDescription = $article['meta_description'] ?? $article['excerpt'] ?? '';
        $pageImage = $article['og_image'] ?? $article['image_url'] ?? null;

        return view('pages.article', compact(
            'article',
            'relatedArticles',
            'popularNews',
            'canonicalUrl',
            'ampUrl',
            'metaTitle',
            'metaDescription',
            'pageImage',
        ));
    }

    public function amp(string $slug)
    {
        $article = ArticleFeed::findArticle($slug, FallbackDataService::getArticles());

        abort_unless($article, 404);

        return response()
            ->view('pages.article-amp', [
                'article' => $article,
                'canonicalUrl' => route('article.show', $article['slug']),
                'ampUrl' => route('article.amp', $article['slug']),
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8');
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
