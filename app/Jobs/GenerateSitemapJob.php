<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 3;

    public function __construct(
        public ?int $userId = null,
    ) {
        $this->onQueue('sitemap');
    }

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        $sitemap->add(Url::create(route('home'))->setPriority(1.0));

        Post::published()
            ->select(['id', 'updated_at'])
            ->chunkById(500, function ($posts) use ($sitemap) {
                foreach ($posts as $post) {
                    $sitemap->add(
                        Url::create(route('article.id', $post->id))
                            ->setLastModificationDate($post->updated_at)
                            ->setPriority(0.8)
                    );
                }
            });

        Category::query()
            ->select(['id', 'slug', 'updated_at'])
            ->chunkById(500, function ($categories) use ($sitemap) {
                foreach ($categories as $category) {
                    $sitemap->add(
                        Url::create(route('category.show', $category->slug))
                            ->setLastModificationDate($category->updated_at)
                            ->setPriority(0.6)
                    );
                }
            });

        $sitemapPath = public_path('sitemap.xml');
        $sitemap->writeToFile($sitemapPath);

        Log::info('Sitemap generated successfully', [
            'file' => $sitemapPath,
            'user_id' => $this->userId,
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Sitemap generation failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => $this->userId,
        ]);
    }
}
