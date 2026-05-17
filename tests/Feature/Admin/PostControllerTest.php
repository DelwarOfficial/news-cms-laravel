<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;

class PostControllerTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $author;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create(['code' => 'en', 'locale' => 'en_US', 'is_default' => true]);
        $this->seed(RolePermissionSeeder::class);

        // Create users with roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->author = User::factory()->create();
        $this->author->assignRole('Author/Reporter');

        $this->category = Category::factory()->create();
    }

    /**
     * Test admin can view all posts
     */
    public function test_admin_can_view_all_posts(): void
    {
        $posts = Post::factory()->count(3)->create(['user_id' => $this->author->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.posts.index'));

        $response->assertStatus(200);
        $response->assertViewHas('posts');
    }

    /**
     * Test author can only view own posts
     */
    public function test_author_can_only_view_own_posts(): void
    {
        $ownPost = Post::factory()->create(['user_id' => $this->author->id]);
        $otherPost = Post::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->author)
            ->get(route('admin.posts.index'));

        $response->assertStatus(200);
        $this->assertEquals(1, $response->viewData('posts')->total());
    }

    /**
     * Test admin can create post with published status
     */
    public function test_admin_can_create_published_post(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title_bn' => 'Test Post',
                'body_bn' => 'Test content',
                'status' => 'published',
                'visibility' => 'public',
                'post_format' => 'standard',
                'category_id' => $this->category->id,
            ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'status' => 'published'
        ]);
    }

    /**
     * Test author cannot directly publish posts
     */
    public function test_author_cannot_directly_publish_post(): void
    {
        $this->actingAs($this->author)
            ->post(route('admin.posts.store'), [
                'title_bn' => 'Test Post',
                'body_bn' => 'Test content',
                'status' => 'published',
                'visibility' => 'public',
                'post_format' => 'standard',
                'category_id' => $this->category->id,
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'status' => 'pending'
        ]);
    }

    /**
     * Test author cannot edit other's posts
     */
    public function test_author_cannot_edit_others_post(): void
    {
        $post = Post::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->author)
            ->put(route('admin.posts.update', $post), [
                'title_bn' => 'Updated Title',
                'body_bn' => 'Updated content',
                'status' => 'draft',
                'visibility' => 'public',
                'post_format' => 'standard',
                'category_id' => $this->category->id,
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test validation on post creation
     */
    public function test_post_validation_on_store(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title_bn' => '',
                'body_bn' => '',
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors(['title_bn', 'body_bn', 'status']);
    }

    /**
     * Test category must exist when creating post
     */
    public function test_category_must_exist(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title_bn' => 'Test',
                'body_bn' => 'Content',
                'status' => 'draft',
                'visibility' => 'public',
                'post_format' => 'standard',
                'category_id' => 9999,
            ]);

        $response->assertSessionHasErrors('category_id');
    }
}
