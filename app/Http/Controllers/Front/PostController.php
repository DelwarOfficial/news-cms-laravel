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
        $locale = app()->getLocale();
        $fallbackArticles = FallbackDataService::getArticles();
        $article = ArticleFeed::findArticle($slug, $fallbackArticles);

        if (!$article) {
            abort(404);
        }

        $prefix = ArticleFeed::formatRoutePrefix($article['post_format'] ?? 'standard');
        $canonicalUrl = $this->canonicalUrl($article);
        $ampUrl = $this->ampUrl($article);

        if (! $request->routeIs("{$prefix}.show")) {
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
            'locale'
        ));
    }

    public function showIdSlug(Request $request, int $postId, string $slug)
    {
        $post = $this->publishedPostById($postId);
        $prefix = ArticleFeed::formatRoutePrefix($post->post_format);

        if (! $request->routeIs("{$prefix}.id_slug")) {
            return redirect($this->canonicalUrl([
                'id' => $post->id,
                'slug' => $post->slug,
                'post_format' => $post->post_format,
            ]), 301);
        }

        return $this->renderPost($post);
    }

    public function showId(Request $request, int $postId)
    {
        return $this->renderPost($this->publishedPostById($postId));
    }

    public function showRootId(int $postId)
    {
        $post = $this->publishedPostById($postId);
        $prefix = ArticleFeed::formatRoutePrefix($post->post_format);

        return redirect()->route("{$prefix}.id", $postId, 301);
    }

    private function renderPost(Post $post)
    {
        $locale = app()->getLocale();
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

        $canonicalUrl = $this->canonicalUrl($article);
        $ampUrl = $this->ampUrl($article);

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
            'locale'
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
                'ampUrl' => $this->ampUrl($article),
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
        $prefix = ArticleFeed::formatRoutePrefix($article['post_format'] ?? 'standard');

        return ! empty($article['id'])
            ? route("{$prefix}.id_slug", ['postId' => $article['id'], 'slug' => $article['slug']])
            : route("{$prefix}.show", $article['slug']);
    }

    private function ampUrl(array $article): string
    {
        $prefix = ArticleFeed::formatRoutePrefix($article['post_format'] ?? 'standard');

        return route("{$prefix}.amp", $article['slug']);
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
