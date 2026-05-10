<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use App\Models\Post;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->post = Post::factory()->create();
    }

    /**
     * Test admin can view pending comments
     */
    public function test_admin_can_view_pending_comments(): void
    {
        Comment::factory()->count(3)->create(['post_id' => $this->post->id, 'status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.comments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('comments');
        $this->assertEquals(3, $response.viewData('comments')->total());
    }

    /**
     * Test admin can approve comment
     */
    public function test_admin_can_approve_comment(): void
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id, 'status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.comments.approve', $comment));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'status' => 'approved']);
    }

    /**
     * Test admin can mark comment as spam
     */
    public function test_admin_can_mark_comment_as_spam(): void
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.comments.markSpam', $comment));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'status' => 'spam']);
    }

    /**
     * Test admin can delete comment
     */
    public function test_admin_can_delete_comment(): void
    {
        $comment = Comment::factory()->create(['post_id' => $this->post->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.comments.destroy', $comment));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
