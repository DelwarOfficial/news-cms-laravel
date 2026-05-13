<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Support\FallbackDataService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoNewsContentSeeder extends Seeder
{
    public function run(): void
    {
        $languageId = $this->ensureLanguage();
        $authorId = User::query()->where('email', 'admin@newscore.com')->value('id')
            ?: User::query()->value('id');

        if (! $authorId) {
            $this->command?->warn('No CMS user found. Run AdminUserSeeder first.');

            return;
        }

        DB::transaction(function () use ($languageId, $authorId): void {
            $this->upsertCategories();
            $this->upsertArticles($languageId, $authorId);
        });

        Cache::flush();
    }

    private function ensureLanguage(): int
    {
        return Language::query()->updateOrCreate(
            ['code' => 'bn'],
            [
                'name' => 'Bangla',
                'locale' => 'bn_BD',
                'is_default' => true,
                'is_active' => true,
                'order' => 1,
            ],
        )->id;
    }

    private function upsertCategories(): void
    {
        foreach (config('categories.items', []) as $index => $category) {
            $parent = $this->upsertCategory($category, null, $index + 1);

            foreach (($category['children'] ?? []) as $childIndex => $child) {
                $this->upsertCategory($child, $parent->id, $childIndex + 1);
            }
        }
    }

    private function upsertCategory(array $category, ?int $parentId, int $order): Category
    {
        return Category::query()->updateOrCreate(
            ['slug' => $category['slug']],
            [
                'parent_id' => $parentId,
                'name' => $category['name_bn'] ?? $category['name_en'] ?? Str::headline($category['slug']),
                'description' => $category['meta_description'] ?? null,
                'order' => $category['sort_order'] ?? $category['menu_order'] ?? $order,
                'status' => 'active',
                'meta_title' => $category['meta_title'] ?? null,
                'meta_description' => $category['meta_description'] ?? null,
            ],
        );
    }

    private function upsertArticles(int $languageId, int $authorId): void
    {
        config(['homepage.demo_fallback.enabled' => true]);

        foreach (FallbackDataService::getArticles() as $index => $article) {
            $category = $this->categoryFor($article);
            $body = $this->bodyFor($article);
            $publishedAt = now()->subMinutes(($index + 1) * 17);

            $post = Post::query()->updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'user_id' => $authorId,
                    'language_id' => $languageId,
                    'primary_category_id' => $category?->id,
                    'title' => $article['title'],
                    'title_bn' => $article['title'],
                    'slug_bn' => $article['slug'],
                    'excerpt' => $article['excerpt'] ?? Str::limit(strip_tags($body), 180),
                    'content' => $body,
                    'featured_image' => $this->imagePath($article['image_url'] ?? null),
                    'image_path' => $this->imagePath($article['image_url'] ?? null),
                    'featured_image_alt' => $article['title'],
                    'status' => 'published',
                    'published_at' => $publishedAt,
                    'is_breaking' => $index < 3,
                    'is_featured' => $index === 0,
                    'is_sticky' => $index < 6,
                    'is_trending' => in_array($index, [1, 3, 5, 8, 12], true),
                    'is_editors_pick' => in_array($index, [2, 6, 9], true),
                    'view_count' => max(25, 500 - ($index * 13)),
                    'allow_comments' => true,
                    'meta_title' => $article['meta_title'] ?? $article['title'],
                    'meta_title_bn' => $article['meta_title'] ?? $article['title'],
                    'meta_description' => $article['meta_description'] ?? ($article['excerpt'] ?? null),
                    'meta_description_bn' => $article['meta_description'] ?? ($article['excerpt'] ?? null),
                ],
            );

            $this->syncCategories($post, $category);
            $this->syncTags($post, $article['tags'] ?? []);
        }
    }

    private function categoryFor(array $article): ?Category
    {
        $slug = $article['category_slug'] ?? null;

        return $slug ? Category::query()->where('slug', $slug)->first() : null;
    }

    private function bodyFor(array $article): string
    {
        $body = $article['body'] ?? null;

        if (is_array($body)) {
            return implode("\n\n", array_filter($body));
        }

        return (string) ($body ?: ($article['excerpt'] ?? ''));
    }

    private function imagePath(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('#/images/([^/?]+)#', $url, $matches)) {
            return 'images/'.$matches[1];
        }

        return Str::startsWith($url, ['images/', 'storage/']) ? $url : null;
    }

    private function syncCategories(Post $post, ?Category $category): void
    {
        if (! $category) {
            return;
        }

        $categoryIds = collect([$category->parent_id, $category->id])->filter()->unique()->values();

        $post->categories()->sync(
            $categoryIds->mapWithKeys(fn (int $id) => [$id => ['is_primary' => $id === $category->id]])->all(),
        );
    }

    private function syncTags(Post $post, array $tags): void
    {
        $tagIds = collect($tags)
            ->filter()
            ->map(function (string $tag): int {
                return Tag::query()->updateOrCreate(
                    ['slug' => Str::slug($tag)],
                    ['name' => Str::headline($tag)],
                )->id;
            })
            ->values()
            ->all();

        $post->tags()->sync($tagIds);
    }
}
