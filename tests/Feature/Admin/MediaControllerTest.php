<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Media;

class MediaControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $author;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->author = User::factory()->create();
        $this->author->assignRole('Author');
    }

    /**
     * Test admin can upload media
     */
    public function test_admin_can_upload_media(): void
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.media.store'), [
                'file' => $file,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseCount('media', 1);
    }

    /**
     * Test invalid file type is rejected
     */
    public function test_invalid_file_type_rejected(): void
    {
        $file = UploadedFile::fake()->create('test.exe', 100);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.media.store'), [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('media', 0);
    }

    /**
     * Test oversized file is rejected
     */
    public function test_oversized_file_rejected(): void
    {
        $file = UploadedFile::fake()->image('test.jpg', 15000); // 15MB

        $response = $this->actingAs($this->admin)
            ->post(route('admin.media.store'), [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('media', 0);
    }

    /**
     * Test author can delete own media
     */
    public function test_author_can_delete_own_media(): void
    {
        $media = Media::factory()->create(['user_id' => $this->author->id]);

        $response = $this->actingAs($this->author)
            ->delete(route('admin.media.destroy', $media));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    /**
     * Test author cannot delete others media
     */
    public function test_author_cannot_delete_others_media(): void
    {
        $media = Media::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->author)
            ->delete(route('admin.media.destroy', $media));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }
}
