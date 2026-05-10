<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $author;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->author = User::factory()->create();
        $this->author->assignRole('Author');

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
                'title' => 'Test Post',
                'content' => 'Test content',
                'status' => 'published',
                'category_ids' => [$this->category->id],
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
                'title' => 'Test Post',
                'content' => 'Test content',
                'status' => 'published',
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
            ->post(route('admin.posts.update', $post), [
                'title' => 'Updated Title',
                'content' => 'Updated content',
                'status' => 'draft',
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
                'title' => '',
                'content' => '',
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors(['title', 'content', 'status']);
    }

    /**
     * Test category must exist when creating post
     */
    public function test_category_must_exist(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Test',
                'content' => 'Content',
                'status' => 'draft',
                'category_ids' => [9999],
            ]);

        $response->assertSessionHasErrors('category_ids.0');
    }
}
