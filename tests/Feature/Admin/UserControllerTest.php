<?php

namespace Tests\Feature\Admin;

use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    private User $superAdmin;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    /**
     * Test non-admin cannot access user management
     */
    public function test_non_admin_cannot_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Author/Reporter');

        $response = $this->actingAs($user)
            ->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    /**
     * Test admin can view users
     */
    public function test_admin_can_view_users(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewHas('users');
    }

    /**
     * Test admin can create user with strong password
     */
    public function test_admin_can_create_user_with_strong_password(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'username' => 'newuser',
                'email' => 'newuser@example.com',
                'password' => 'SecurePassword123',
                'password_confirmation' => 'SecurePassword123',
                'role' => 'Author/Reporter',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    /**
     * Test user creation with weak password fails
     */
    public function test_user_creation_with_weak_password_fails(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name' => 'New User',
                'username' => 'newuser',
                'email' => 'newuser@example.com',
                'password' => 'weak',
                'password_confirmation' => 'weak',
                'role' => 'Author/Reporter',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test cannot delete last super admin
     */
    public function test_cannot_delete_last_super_admin(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $this->superAdmin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }
}
