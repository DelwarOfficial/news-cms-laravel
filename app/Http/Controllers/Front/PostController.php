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

        $canonicalUrl = $this->canonicalUrl($article);
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

    public function showIdSlug(Request $request, int $postId, string $slug)
    {
        $post = $this->publishedPostById($postId);

        return $this->renderPost($post);
    }

    public function showId(Request $request, int $postId)
    {
        return $this->renderPost($this->publishedPostById($postId));
    }

    public function showRootId(int $postId)
    {
        return redirect()->route('article.id', $postId, 301);
    }

    private function renderPost(Post $post)
    {
        $article = ArticleFeed::postToArticleArray($post->loadMissing([
            'author',
            'bylineAuthor',
            'language',
            'primaryCategory.parent',
            'category.parent',
            'subcategory.parent',
            'categories.parent',
            'featuredMedia',
            'divisionLocation',
            'districtLocation',
            'upazilaLocation',
            'tags',
        ]), true);

        $canonicalUrl = route('article.id', $post->id);
        $ampUrl = route('article.amp', $article['slug']);

        $post->incrementViews();

        $relatedArticles = $this->relatedArticles->forArticle($article);
        $popularNews = $this->popularNews->get(5, [$post->id]);
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
                'canonicalUrl' => $this->canonicalUrl($article),
                'ampUrl' => route('article.amp', $article['slug']),
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function publishedPostById(int $postId): Post
    {
        return Post::query()
            ->published()
            ->whereKey($postId)
            ->firstOrFail();
    }

    private function canonicalUrl(array $article): string
    {
        return ! empty($article['id'])
            ? route('article.id', $article['id'])
            : route('article.show', $article['slug']);
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
