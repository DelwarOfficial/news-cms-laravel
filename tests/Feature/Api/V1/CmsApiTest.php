<?php

namespace Tests\Feature\Api\V1;

use App\Models\ApiKey;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsApiTest extends TestCase
{
    use RefreshDatabase;

    private array $cmsKeyHeader;

    protected function setUp(): void
    {
        parent::setUp();

        $plain = 'nh_cms_key_' . bin2hex(random_bytes(16));
        ApiKey::create([
            'name' => 'CMS Test Key',
            'key_prefix' => substr($plain, 0, 10),
            'key_hash' => hash('sha256', $plain),
            'scopes' => ['cms'],
            'rate_limit' => 1000,
            'is_active' => true,
        ]);

        $this->cmsKeyHeader = ['X-API-Key' => $plain, 'Accept' => 'application/json'];
    }

    public function test_status(): void
    {
        $response = $this->getJson('/api/v1/cms/status', $this->cmsKeyHeader);

        $response->assertOk()
            ->assertJsonPath('data.status', 'ok');
    }

    public function test_cms_requires_cms_scope(): void
    {
        $plain = 'nh_read_key_' . bin2hex(random_bytes(16));
        ApiKey::create([
            'name' => 'Read Only Key',
            'key_prefix' => substr($plain, 0, 10),
            'key_hash' => hash('sha256', $plain),
            'scopes' => ['read'],
            'rate_limit' => 1000,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/cms/status', [
            'X-API-Key' => $plain,
            'Accept' => 'application/json',
        ]);

        $response->assertForbidden();
    }

    public function test_create_category_via_cms(): void
    {
        $response = $this->postJson('/api/v1/cms/categories', [
            'name' => 'CMS Imported Cat',
            'slug' => 'cms-imported-cat',
        ], $this->cmsKeyHeader);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'CMS Imported Cat');
    }

    public function test_create_tag_via_cms(): void
    {
        $response = $this->postJson('/api/v1/cms/tags', [
            'name' => 'CMS Imported Tag',
        ], $this->cmsKeyHeader);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'CMS Imported Tag');
    }

    public function test_create_post_via_cms(): void
    {
        $category = Category::factory()->create(['slug' => 'test-category']);

        $response = $this->postJson('/api/v1/cms/posts', [
            'title' => 'CMS Imported Post',
            'body' => '<p>Imported content</p>',
            'status' => 'published',
            'category_slug' => 'test-category',
            'tag_names' => ['Auto Tag 1', 'Auto Tag 2'],
            'source_url' => 'https://example.com/original',
            'source_name' => 'CMS Import',
            'is_breaking' => true,
        ], $this->cmsKeyHeader);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'CMS Imported Post');

        $this->assertDatabaseHas('tags', ['name' => 'Auto Tag 1']);
        $this->assertDatabaseHas('tags', ['name' => 'Auto Tag 2']);
    }

    public function test_soft_delete_via_cms(): void
    {
        $post = Post::factory()->create(['status' => 'published']);

        $response = $this->deleteJson("/api/v1/cms/posts/{$post->id}", [], $this->cmsKeyHeader);

        $response->assertNoContent();

        $this->assertSoftDeleted($post);
        $this->assertEquals('archived', $post->fresh()->status);
    }

    public function test_update_via_cms(): void
    {
        $post = Post::factory()->create(['title' => 'Original Title', 'status' => 'draft']);

        $response = $this->putJson("/api/v1/cms/posts/{$post->id}", [
            'title' => 'Updated Title',
            'is_featured' => true,
        ], $this->cmsKeyHeader);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
    }
}
