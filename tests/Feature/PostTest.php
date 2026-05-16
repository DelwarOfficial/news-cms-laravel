<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Language;
use App\Models\User;
use App\Models\Post;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create(['code' => 'en', 'locale' => 'en_US', 'is_default' => true]);
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_create_post()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title_bn' => 'Test Breaking News',
            'body_bn' => 'This is a test article content.',
            'status' => 'published',
            'visibility' => 'public',
            'category_id' => $category->id,
            'post_format' => 'standard',
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', ['title' => 'Test Breaking News']);
    }

    public function test_author_cannot_publish_directly()
    {
        $category = Category::factory()->create();
        $author = User::factory()->create();
        $author->assignRole('Author/Reporter');

        $response = $this->actingAs($author)->post('/admin/posts', [
'title_bn' => 'Author Post',
                'body_bn' => 'Test content',
            'status' => 'published',
            'visibility' => 'public',
            'category_id' => $category->id,
            'post_format' => 'standard',
        ]);

        $response->assertRedirect(route('admin.posts.index'));

        $post = Post::where('title', 'Author Post')->first();
        $this->assertEquals('pending', $post->status);
    }

    public function test_author_can_only_see_own_posts()
    {
        $category = Category::factory()->create();
        $author = User::factory()->create();
        $author->assignRole('Author/Reporter');

        $post = Post::factory()->create([
            'user_id' => $author->id,
            'primary_category_id' => $category->id,
            'title' => 'Author Post',
            'slug' => 'author-post',
        ]);
        Post::factory()->create(['primary_category_id' => $category->id]); // Another user's post

        $response = $this->actingAs($author)->get('/admin/posts');

        $response->assertSee('Author Post');
    }
}