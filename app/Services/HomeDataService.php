<?php

namespace App\Services;

use App\Models\District;
use App\Support\FallbackDataService;
use App\ViewModels\HomepageSection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class HomeDataService
{
    public function __construct(
        private readonly HomepageContentRepository $content,
        private readonly PopularNewsService $popularNews,
    ) {
    }

    public function getHomepageData(): array
    {
        if (! config('homepage.cache.enabled', true)) {
            return $this->buildHomepageData();
        }

        return Cache::remember(
            config('homepage.cache.key', 'homepage:v1'),
            now()->addSeconds((int) config('homepage.cache.ttl', 300)),
            fn () => $this->buildHomepageData(),
        );
    }

    public function getPhotoStoryData(): array
    {
        return $this->buildPhotoStoryPayload(
            $this->content->latest(FallbackDataService::getArticles()),
        );
    }

    private function buildHomepageData(): array
    {
        $fallbackArticles = FallbackDataService::getArticles();
        $articles = $this->content->latest($fallbackArticles);
        $sections = $this->categorySections($fallbackArticles);

        $breakingStories = $this->placement('breaking', $fallbackArticles);
        $usedHomepagePostIds = $this->articleIds($breakingStories);

        $featured = $this->placement('featured', $fallbackArticles, $usedHomepagePostIds)[0] ?? null;
        $usedHomepagePostIds = $this->mergeArticleIds($usedHomepagePostIds, [$featured]);

        $centerGrid = $this->placement('center_grid', $fallbackArticles, $usedHomepagePostIds);
        $usedHomepagePostIds = $this->mergeArticleIds($usedHomepagePostIds, $centerGrid);

        $leftCol = $this->placement('left_column', $fallbackArticles, $usedHomepagePostIds);
        $usedHomepagePostIds = $this->mergeArticleIds($usedHomepagePostIds, $leftCol);

        $rightCol = $this->placement('right_column', $fallbackArticles, $usedHomepagePostIds);

        $localNewsArticles = $this->content->localNews(
            $fallbackArticles,
            (int) config('homepage.sections.local_news.limit', 9),
        );

        $worldLayout = $this->featureListLayout($sections['world']->articles);
        $sportsArticles = $sections['sports']->articles;
        $videoLayout = $this->featureListLayout($sections['videos']->articles, 3);
        $entertainmentLayout = $this->entertainmentLayout($sections['entertainment']->articles);
        $photoStoryPayload = $this->buildPhotoStoryPayload($articles);

        return [
            'homepageSections' => $sections,
            'breakingStories' => $breakingStories,
            'categories' => [],
            'leftCol' => $leftCol,
            'featured' => $featured,
            'centerGrid' => $centerGrid,
            'rightCol' => $rightCol,
            'bangladeshArticles' => $sections['bangladesh']->articles,
            'countryLeft' => array_slice($localNewsArticles, 0, 2),
            'countryHero' => $localNewsArticles[2] ?? null,
            'countryRight' => array_slice($localNewsArticles, 3, 6),
            'internationalArticles' => $worldLayout['posts'],
            'internationalBig' => $worldLayout['featured'],
            'internationalSmall' => $worldLayout['list'],
            'opinionArticles' => $sections['politics']->articles,
            'opinionMeta' => $this->opinionMeta(),
            'sportsArticles' => $sportsArticles,
            'sportsPrimary' => $sportsArticles[0] ?? null,
            'sportsSecondary' => array_slice($sportsArticles, 1, 2),
            'sportsSubcatArticles' => $this->sportsSubcategoryArticles($sportsArticles, $fallbackArticles),
            'matamatArticles' => $sections['opinion']->articles,
            'videoArticles' => $videoLayout['posts'],
            'videoFeatured' => $videoLayout['featured'],
            'videoSmall' => $videoLayout['list'],
            'entertainmentArticles' => $entertainmentLayout['posts'],
            'entertainmentLeft' => $entertainmentLayout['left'],
            'entertainmentHero' => $entertainmentLayout['hero'],
            'entertainmentRight' => $entertainmentLayout['right'],
            'economyArticles' => $sections['economy']->articles,
            'healthArticles' => $sections['lifestyle']->articles,
            'jobArticles' => $sections['jobs']->articles,
            'specialArticles' => $sections['special']->articles,
            'popularNews' => $this->popularNews->get(),
            'photoNewsArticles' => $photoStoryPayload['carousel'],
            'photoNewsLatest' => $photoStoryPayload['latest'],
            'photoNewsPopular' => $photoStoryPayload['popular'],
            'photoStoryPayload' => $photoStoryPayload,
            'religionArticles' => $sections['religion']->articles,
            'rajdhaniArticles' => $sections['dhaka']->articles,
            'educationArticles' => $sections['education']->articles,
            'probashArticles' => $sections['expatriates']->articles,
            'saradeshDivisions' => $this->saradeshDivisions(),
        ];
    }

    private function categorySections(array $fallbackArticles): array
    {
        return collect(config('homepage.sections.category_feeds', []))
            ->mapWithKeys(function (array $definition, string $key) use ($fallbackArticles) {
                $source = $definition['source'] ?? 'category';
                $slugs = $definition['slugs'] ?? [];
                $limit = (int) ($definition['limit'] ?? 4);

                $articles = $source === 'relationship-category'
                    ? $this->content->relationshipCategory($slugs, $limit, $fallbackArticles)
                    : $this->content->category($slugs, $fallbackArticles, $limit);

                return [$key => new HomepageSection($key, $source, $articles, $definition)];
            })
            ->all();
    }

    private function placement(string $name, array $fallbackArticles, array $exceptIds = []): array
    {
        $definition = config("homepage.sections.hero.placements.{$name}", []);

        return $this->content->placement(
            $definition['key'] ?? "home.{$name}",
            $definition['legacy'] ?? null,
            $fallbackArticles,
            (int) ($definition['limit'] ?? 1),
            $exceptIds,
        );
    }

    private function featureListLayout(array $articles, int $listLimit = 5): array
    {
        $posts = array_values($articles);

        return [
            'posts' => $posts,
            'featured' => $posts[0] ?? null,
            'list' => array_slice($posts, 1, $listLimit),
        ];
    }

    private function entertainmentLayout(array $articles): array
    {
        $posts = array_values($articles);

        return [
            'posts' => $posts,
            'left' => array_slice($posts, 0, 3),
            'hero' => $posts[3] ?? ($posts[0] ?? null),
            'right' => array_slice($posts, 4, 3),
        ];
    }

    private function sportsSubcategoryArticles(array $sportsArticles, array $fallbackArticles): array
    {
        return collect(config('homepage.sections.sports_subcategories', []))
            ->values()
            ->map(function (array $definition, int $index) use ($sportsArticles, $fallbackArticles) {
                $article = $this->content->relationshipCategory($definition['slugs'] ?? [], 1, $fallbackArticles)[0]
                    ?? $sportsArticles[$index]
                    ?? null;

                return [
                    'article' => $article,
                    'subcat' => $definition['label'] ?? '',
                ];
            })
            ->filter(fn (array $item) => ! empty($item['article']))
            ->values()
            ->all();
    }

    private function opinionMeta(): array
    {
        return [
            ['name' => 'à¦¡. à¦¶à¦«à¦¿à¦•à§à¦² à¦‡à¦¸à¦²à¦¾à¦®', 'tag' => 'à¦•à¦²à¦¾à¦®'],
            ['name' => 'à¦¸à§ˆà¦¯à¦¼à¦¦ à¦†à¦¬à§à¦² à¦®à¦•à¦¸à§à¦¦', 'tag' => 'à¦®à¦¤à¦¾à¦®à¦¤'],
            ['name' => 'à¦…à¦§à§à¦¯à¦¾à¦ªà¦• à¦†à¦¨à§ à¦®à§à¦¹à¦¾à¦®à§à¦®à¦¦', 'tag' => 'à¦¬à¦¿à¦¶à§à¦²à§‡à¦·à¦£'],
            ['name' => 'à¦«à¦¾à¦°à§à¦• à¦“à¦¯à¦¼à¦¾à¦¸à¦¿à¦«', 'tag' => 'à¦®à¦¤à¦¾à¦®à¦¤'],
        ];
    }

    private function saradeshDivisions(): array
    {
        try {
            return District::allDivisions();
        } catch (\Throwable $exception) {
            Log::warning('Failed to load homepage Saradesh divisions.', [
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    private function mergeArticleIds(array $ids, array $articles): array
    {
        return array_values(array_unique(array_merge($ids, $this->articleIds($articles))));
    }

    private function articleIds(array $articles): array
    {
        return collect($articles)
            ->filter()
            ->pluck('id')
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function buildPhotoStoryPayload(array $articles): array
    {
        $carousel = collect($articles)->take(10)->values()->map(function ($article, $index) {
            return [
                'id' => $article['id'] ?? $index + 1,
                'headline' => $article['title'],
                'slug' => $article['slug'],
                'timestamp' => $article['time_ago'],
                'image_url' => $article['image_url'],
                'tags' => [],
            ];
        });

        if ($carousel->isEmpty()) {
            $carousel = $this->publicImageFallbackSlides();
        }

        $latest = collect($articles)->take(8)->values()->map(fn ($article, $index) => [
            'id' => $index + 1,
            'headline' => $article['title'],
            'slug' => $article['slug'],
            'timestamp' => $article['time_ago'],
        ])->all();

        $popular = collect($this->popularNews->get())->values()->map(fn ($article, $index) => [
            'id' => $index + 1,
            'headline' => $article['title'],
            'slug' => $article['slug'],
            'timestamp' => $article['time_ago'],
        ])->all();

        return [
            'carousel' => $carousel->values()->all(),
            'latest' => $latest,
            'popular' => $popular,
        ];
    }

    private function publicImageFallbackSlides(): \Illuminate\Support\Collection
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        return collect(File::files(public_path('images')))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), $allowedExtensions, true))
            ->sortBy(fn ($file) => $file->getFilename())
            ->take(5)
            ->values()
            ->map(fn ($file, $index) => [
                'id' => $index + 1,
                'headline' => 'Placeholder',
                'slug' => '#',
                'timestamp' => '',
                'image_url' => asset('images/' . $file->getFilename()),
                'tags' => [],
            ]);
    }
}
