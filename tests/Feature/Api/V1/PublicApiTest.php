<?php

namespace Tests\Feature\Api\V1;

use App\Models\Language;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create(['code' => 'en', 'locale' => 'en_US', 'is_default' => true]);
    }

    public function test_list_posts_returns_json_structure(): void
    {
        $response = $this->getJson('/api/v1/posts');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['page', 'limit', 'total', 'totalPages']]);
    }

    public function test_show_post_returns_404_for_missing(): void
    {
        $response = $this->getJson('/api/v1/posts/non-existent-slug');

        $response->assertNotFound();
    }

    public function test_breaking_endpoint(): void
    {
        $response = $this->getJson('/api/v1/posts/breaking');

        $response->assertOk();
    }

    public function test_trending_endpoint(): void
    {
        $response = $this->getJson('/api/v1/posts/trending');

        $response->assertOk();
    }

    public function test_featured_endpoint(): void
    {
        $response = $this->getJson('/api/v1/posts/featured');

        $response->assertOk();
    }

    public function test_popular_endpoint(): void
    {
        $response = $this->getJson('/api/v1/posts/popular');

        $response->assertOk();
    }

    public function test_editors_pick_endpoint(): void
    {
        $response = $this->getJson('/api/v1/posts/editors-pick');

        $response->assertOk();
    }

    public function test_categories_tree(): void
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_search_endpoint(): void
    {
        $response = $this->getJson('/api/v1/search?q=test');

        $response->assertOk();
    }

    public function test_view_increment(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'primary_category_id' => $category->id,
        ]);

        $response = $this->postJson("/api/v1/posts/{$post->id}/view");

        $response->assertOk();
        $this->assertEquals(1, $post->fresh()->view_count);
    }
}
