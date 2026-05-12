<?php

namespace App\Services;

use App\Support\ArticleFeed;
use App\Support\FallbackDataService;

class RelatedArticleService
{
    public function forArticle(array $article, int $limit = 3): array
    {
        $limit = max(1, $limit);
        $fallbackArticles = FallbackDataService::getArticles();
        $categorySlug = $article['category_slug'] ?? null;
        $currentSlug = $article['slug'] ?? null;

        $related = $categorySlug
            ? ArticleFeed::categoryArticles([$categorySlug], $fallbackArticles, $limit + 1)
            : [];

        $related = collect($related)
            ->reject(fn (array $candidate) => ($candidate['slug'] ?? null) === $currentSlug)
            ->take($limit)
            ->values();

        if ($related->count() >= $limit) {
            return $related->all();
        }

        $fillers = collect(ArticleFeed::homepageArticles($fallbackArticles, 80))
            ->reject(fn (array $candidate) => ($candidate['slug'] ?? null) === $currentSlug)
            ->reject(fn (array $candidate) => $related->contains('slug', $candidate['slug'] ?? null))
            ->take($limit - $related->count());

        return $related
            ->merge($fillers)
            ->values()
            ->all();
    }
}
