<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;

class CategoryControllerTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Language::factory()->create(['code' => 'en', 'locale' => 'en_US', 'is_default' => true]);
        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    /**
     * Test admin can create category
     */
    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Technology',
                'description' => 'Tech news',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Technology']);
    }

    /**
     * Test category name must be unique
     */
    public function test_category_name_must_be_unique(): void
    {
        Category::create(['name' => 'Technology']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Technology',
                'description' => 'Duplicate',
            ]);

        $response->assertSessionHasErrors('name');
    }

    /**
     * Test cannot create circular category hierarchy
     */
    public function test_cannot_create_circular_hierarchy(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $parent), [
                'name' => $parent->name,
                'parent_id' => $child->id,
            ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test cannot delete category with posts
     */
    public function test_cannot_delete_category_with_posts(): void
    {
        $category = Category::factory()->create();
        $category->posts()->attach(\App\Models\Post::factory()->create()->id);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
