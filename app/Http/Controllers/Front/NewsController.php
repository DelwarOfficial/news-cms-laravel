<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Helpers\DateHelper;
use App\Models\Post;
use App\Services\PopularNewsService;
use App\Support\PostCategoryResolver;
use App\Support\FallbackDataService;
use App\Support\ImageResolver;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    private static ?bool $postsTableReady = null;

    public function __construct(
        private readonly PopularNewsService $popularNews,
    ) {
    }

    public function latest()
    {
        $fallbackArticles = FallbackDataService::getArticles();
        $posts = $this->latestPosts($fallbackArticles);
        $topStory = $posts->firstItem() === 1 ? $posts->getCollection()->first() : null;
        $popularNews = $this->popularNews->get();
        $metaTitle = 'সর্বশেষ সংবাদ | Dhaka Magazine';
        $metaDescription = 'বাংলাদেশ ও বিশ্বের সর্বশেষ খবর, রাজনীতি, খেলাধুলা, বিনোদন, অর্থনীতি ও প্রযুক্তির আপডেট পড়ুন Dhaka Magazine-এ।';
        $canonicalUrl = route('news.latest');
        $pageImage = $topStory['image_url'] ?? asset('images/dhaka-magazine-color-logo.svg');

        return view('news.latest', compact(
            'posts',
            'topStory',
            'popularNews',
            'metaTitle',
            'metaDescription',
            'canonicalUrl',
            'pageImage'
        ));
    }

    private function latestPosts(array $fallbackArticles): LengthAwarePaginator
    {
        if (! $this->postsTableReady()) {
            return $this->paginateArticles($fallbackArticles);
        }

        try {
            $posts = Post::query()
                // Match ArticleFeed's eager-loaded graph so latest-page cards
                // can be normalized without category/media/author lazy loads.
                ->withContentRelations()
                ->whereIn('status', ['published'])
                ->latest('published_at')
                ->latest('id')
                ->paginate(20)
                ->withQueryString();

            $posts->getCollection()->transform(fn (Post $post) => $this->toNewsItem($post));

            if ($posts->total() === 0) {
                return $this->paginateArticles($fallbackArticles);
            }

            return $posts;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to fetch latest posts: " . $e->getMessage());
            return $this->paginateArticles($fallbackArticles);
        }
    }

    private function toNewsItem(Post $post): array
    {
        $category = PostCategoryResolver::categoryFor($post);
        $publishedAt = $post->published_at ?: $post->created_at;

        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'category' => $category['name_bn'] ?? PostCategoryResolver::fallbackCategory()['name_bn'],
            'category_url' => PostCategoryResolver::categoryRoute($category),
            'excerpt' => Str::limit(strip_tags((string) ($post->excerpt ?: $post->content ?: $post->body)), 170),
            'author' => $post->author?->name ?: $post->source_name ?: 'ঢাকা ম্যাগাজিন ডেস্ক',
            'date' => DateHelper::getBengaliDate($publishedAt),
            'time_ago' => DateHelper::timeAgo($publishedAt),
            'image_url' => ImageResolver::postImageUrl($post),
            'views' => $this->viewCount($post),
        ];
    }



    private function viewCount(Post $post): ?int
    {
        foreach (['view_count', 'views', 'hit_count'] as $column) {
            if (isset($post->{$column}) && is_numeric($post->{$column})) {
                return (int) $post->{$column};
            }
        }

        return null;
    }

    private function paginateArticles(array $articles): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage('page');
        $perPage = 20;
        $items = collect($articles)
            ->values()
            ->map(fn (array $article) => $article + [
                'category_url' => null,
                'views' => null,
            ]);

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }

    private function postsTableReady(): bool
    {
        return \App\Support\SchemaReadyCheck::isPostsTableReady();
    }

}
