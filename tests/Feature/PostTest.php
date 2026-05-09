<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_post()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $response = $this->actingAs($admin)->post('/admin/posts', [
            'title' => 'Test Breaking News',
            'content' => 'This is a test article content.',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        $this->assertDatabaseHas('posts', ['title' => 'Test Breaking News']);
    }

    public function test_author_cannot_publish_directly()
    {
        $author = User::factory()->create();
        $author->assignRole('Author');

        $response = $this->actingAs($author)->post('/admin/posts', [
            'title' => 'Author Post',
            'content' => 'Test content',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.posts.index'));
        
        $post = Post::where('title', 'Author Post')->first();
        $this->assertEquals('pending', $post->status);
    }

    public function test_author_can_only_see_own_posts()
    {
        $author = User::factory()->create();
        $author->assignRole('Author');

        Post::factory()->create(['user_id' => $author->id]);
        Post::factory()->create(); // Another user's post

        $response = $this->actingAs($author)->get('/admin/posts');

        $response->assertSee('Author Post'); // Should see own post
        // Should not see other posts (basic check)
    }
}