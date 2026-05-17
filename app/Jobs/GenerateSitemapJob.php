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
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');
        $urls = [[
            'loc' => $frontendUrl.'/',
            'lastmod' => null,
            'priority' => '1.0',
        ]];

        Post::published()
            ->select(['id', 'slug', 'updated_at'])
            ->chunkById(500, function ($posts) use (&$urls) {
                foreach ($posts as $post) {
                    $urls[] = [
                        'loc' => $this->frontendUrl('/article/'.$post->slug),
                        'lastmod' => $post->updated_at?->toAtomString(),
                        'priority' => '0.8',
                    ];
                }
            });

        Category::query()
            ->select(['id', 'slug', 'updated_at'])
            ->chunkById(500, function ($categories) use (&$urls) {
                foreach ($categories as $category) {
                    $urls[] = [
                        'loc' => $this->frontendUrl('/category/'.$category->slug),
                        'lastmod' => $category->updated_at?->toAtomString(),
                        'priority' => '0.6',
                    ];
                }
            });

        $sitemapPath = public_path('sitemap.xml');
        file_put_contents($sitemapPath, $this->toXml($urls));

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

    private function frontendUrl(string $path = ''): string
    {
        return rtrim((string) config('app.frontend_url'), '/').'/'.ltrim($path, '/');
    }

    private function toXml(array $urls): string
    {
        $xml = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>'.$this->escapeXml($url['loc']).'</loc>';

            if (! empty($url['lastmod'])) {
                $xml[] = '    <lastmod>'.$this->escapeXml($url['lastmod']).'</lastmod>';
            }

            $xml[] = '    <priority>'.$this->escapeXml($url['priority']).'</priority>';
            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return implode(PHP_EOL, $xml).PHP_EOL;
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
