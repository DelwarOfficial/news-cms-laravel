<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions from the matrix
        $permissions = [
            'posts.create',
            'posts.edit.own',
            'posts.edit.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'categories.manage',
            'tags.manage',
            'media.manage',
            'comments.manage',
            'menus.manage',
            'users.manage',
            'roles.manage',
            'settings.manage',
            'ads.manage',
            'dashboard.view',
            'themes.manage',
            'api_keys.manage'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Define Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $author = Role::firstOrCreate(['name' => 'Author']);
        $journalist = Role::firstOrCreate(['name' => 'Journalist']);
        $contributor = Role::firstOrCreate(['name' => 'Contributor']);

        // 3. Assign Permissions based on Matrix

        // Super Admin gets everything
        $superAdmin->givePermissionTo(Permission::all());

        // Admin gets everything EXCEPT roles and api keys
        $adminPermissions = array_diff($permissions, [
            'roles.manage',
            'api_keys.manage'
        ]);
        $admin->syncPermissions($adminPermissions);

        // Editor
        $editor->syncPermissions([
            'posts.create',
            'posts.edit.own',
            'posts.edit.any',
            'posts.delete.own',
            'posts.delete.any',
            'posts.publish',
            'categories.manage',
            'tags.manage',
            'media.manage',
            'comments.manage',
            'dashboard.view',
        ]);

        // Author
        $author->syncPermissions([
            'posts.create',
            'posts.edit.own',
            'posts.delete.own',
            'tags.manage',
            'media.manage',
            'dashboard.view',
        ]);

        // Journalist
        $journalist->syncPermissions([
            'posts.create',
            'posts.edit.own',
            'posts.delete.own',
            'tags.manage',
            'media.manage',
            'dashboard.view',
        ]);

        // Contributor
        $contributor->syncPermissions([
            'posts.create',
            'posts.edit.own',
            'tags.manage',
            'media.manage',
            'dashboard.view',
        ]);
    }
}