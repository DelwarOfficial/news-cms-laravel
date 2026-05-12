<?php

namespace App\Support;

use Illuminate\Support\Collection;

class CategoryRepository
{
    public static function all(): Collection
    {
        return collect(config('categories.items', []))
            ->values()
            ->map(fn(array $category, int $index) => self::normalize($category, null, $index + 1));
    }

    public static function parents(): Collection
    {
        return self::all();
    }

    public static function findParent(string $slug): ?array
    {
        return self::parents()->firstWhere('slug', $slug);
    }

    public static function findChild(string $parentSlug, string $childSlug): ?array
    {
        $parent = self::findParent($parentSlug);

        if (!$parent) {
            return null;
        }

        return collect($parent['children'])->firstWhere('slug', $childSlug);
    }

    public static function redirectTarget(string $slug): ?string
    {
        $decoded = urldecode($slug);
        $redirects = config('categories.redirects', []);

        return $redirects[$decoded] ?? $redirects[$slug] ?? null;
    }

    public static function flat(): Collection
    {
        return self::parents()->flatMap(function (array $parent) {
            return collect([$parent])->merge($parent['children']);
        })->values();
    }

    public static function route(array|string $category): string
    {
        if (is_string($category)) {
            return route('category.parent', $category);
        }

        if (empty($category['slug']) || ! is_string($category['slug'])) {
            return route('home');
        }

        if (!empty($category['parent_slug'])) {
            return route('category.child', [$category['parent_slug'], $category['slug']]);
        }

        return route('category.parent', $category['slug']);
    }

    private static function normalize(array $category, ?array $parent, int $order): array
    {
        $category['parent_id'] = $parent['slug'] ?? null;
        $category['parent_slug'] = $parent['slug'] ?? null;
        $category['status'] = $category['status'] ?? true;
        $category['sort_order'] = $category['sort_order'] ?? $order;
        $category['menu_order'] = $category['menu_order'] ?? $order;
        $category['meta_title'] = $category['meta_title'] ?? "{$category['name_bn']} সংবাদ | Dhaka Magazine";
        $category['children'] = collect($category['children'] ?? [])
            ->values()
            ->map(fn(array $child, int $index) => self::normalize($child, $category, $index + 1))
            ->all();

        return $category;
    }
}
