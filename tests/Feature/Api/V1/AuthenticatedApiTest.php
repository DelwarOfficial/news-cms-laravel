<?php

namespace Tests\Feature\Api\V1;

use App\Models\ApiKey;
use App\Models\Language;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthenticatedApiTest extends TestCase
{
    use DatabaseTransactions;

    private array $apiKeyHeader;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create(['code' => 'en', 'locale' => 'en_US', 'is_default' => true]);
        User::factory()->create(['id' => 1]);

        $plain = 'nh_test_key_' . bin2hex(random_bytes(16));
        ApiKey::create([
            'name' => 'Test Key',
            'key_prefix' => substr($plain, 0, 10),
            'key_hash' => hash('sha256', $plain),
            'scopes' => ['*'],
            'rate_limit' => 1000,
            'is_active' => true,
        ]);

        $this->apiKeyHeader = ['X-API-Key' => $plain, 'Accept' => 'application/json'];
    }

    public function test_dashboard(): void
    {
        $response = $this->getJson('/api/v1/dashboard', $this->apiKeyHeader);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['total_posts', 'published_posts', 'total_categories', 'total_tags', 'total_media']]);
    }

    public function test_dashboard_requires_key(): void
    {
        $response = $this->getJson('/api/v1/dashboard');

        $response->assertUnauthorized();
    }

    public function test_dashboard_rejects_bad_key(): void
    {
        $response = $this->getJson('/api/v1/dashboard', ['X-API-Key' => 'nh_badkey']);

        $response->assertUnauthorized();
    }

    public function test_create_category(): void
    {
        $response = $this->postJson('/api/v1/categories/manage', [
            'name' => 'API Category',
            'status' => 'active',
        ], $this->apiKeyHeader);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'API Category');
    }

    public function test_create_tag(): void
    {
        $response = $this->postJson('/api/v1/tags/manage', [
            'name' => 'API Tag',
        ], $this->apiKeyHeader);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'API Tag');
    }

    public function test_create_post(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/v1/posts/manage', [
            'title' => 'API Post',
            'content' => '<p>Body</p>',
            'status' => 'draft',
            'category_id' => $category->id,
            'post_format' => 'standard',
        ], $this->apiKeyHeader);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'API Post');
    }

    public function test_list_menus(): void
    {
        $response = $this->getJson('/api/v1/menus', $this->apiKeyHeader);

        $response->assertOk();
    }

    public function test_list_widgets(): void
    {
        $response = $this->getJson('/api/v1/widgets', $this->apiKeyHeader);

        $response->assertOk();
    }

    public function test_list_advertisements(): void
    {
        $response = $this->getJson('/api/v1/advertisements', $this->apiKeyHeader);

        $response->assertOk();
    }

    public function test_settings(): void
    {
        $response = $this->getJson('/api/v1/settings', $this->apiKeyHeader);

        $response->assertOk();
    }

    public function test_sitemap(): void
    {
        $response = $this->getJson('/api/v1/sitemap', $this->apiKeyHeader);

        $response->assertOk();
    }

    public function test_revisions(): void
    {
        $response = $this->getJson('/api/v1/revisions', $this->apiKeyHeader);

        $response->assertOk();
    }
}
