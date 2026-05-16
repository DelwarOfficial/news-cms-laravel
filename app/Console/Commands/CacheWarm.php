<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Support\ArticleFeed;
use App\Support\FallbackDataService;
use App\Services\HomepageContentRepository;
use App\Services\PopularNewsService;
use App\Services\TickerHeadlineService;
use App\Services\HomeDataService as BackendHomeDataService;
use App\Support\CategoryRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CacheWarm extends Command
{
    protected $signature = 'cache:warm {--dry-run : Show what would be cached without actually caching}';
    protected $description = 'Warm critical caches for performance';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $locale = app()->getLocale();

        $this->components->task('Warming homepage data', function () use ($dryRun, $locale) {
            if ($dryRun) {
                $this->components->info("[DRY-RUN] Would warm homepage:v1:{$locale}");
                return;
            }
            app(BackendHomeDataService::class)->getHomepageData();
        });

        $this->components->task('Warming category tree', function () use ($dryRun) {
            if ($dryRun) {
                $this->components->info('[DRY-RUN] Would warm layout:site-categories:v2');
                return;
            }
            Cache::remember(
                'layout:site-categories:v2',
                now()->addSeconds((int) config('homepage.cache.ttl', 300)),
                fn () => CategoryRepository::parents(),
            );
        });

        $this->components->task('Warming ticker headlines', function () use ($dryRun) {
            if ($dryRun) {
                $this->components->info('[DRY-RUN] Would warm ticker headlines');
                return;
            }
            app(TickerHeadlineService::class)->get();
        });

        $this->components->task('Warming popular news', function () use ($dryRun) {
            if ($dryRun) {
                $this->components->info('[DRY-RUN] Would warm popular news');
                return;
            }
            app(PopularNewsService::class)->get();
        });

        $this->components->task('Warming latest articles', function () use ($dryRun) {
            if ($dryRun) {
                $this->components->info('[DRY-RUN] Would warm latest articles');
                return;
            }
            ArticleFeed::homepageArticles(FallbackDataService::getArticles(), 40);
        });

        $this->newLine();
        $this->components->info('Cache warming completed successfully.');

        return Command::SUCCESS;
    }
}
