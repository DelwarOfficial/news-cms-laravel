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

        $permissions = [
            'posts.view.any', 'posts.create', 'posts.edit.own', 'posts.edit.any',
            'posts.delete.own', 'posts.delete.any', 'posts.publish',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'media.upload', 'media.manage',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'settings.manage', 'comments.moderate'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $author = Role::firstOrCreate(['name' => 'Author']);

        $superAdmin->givePermissionTo(Permission::all());
        
        $admin->givePermissionTo([
            'posts.view.any', 'posts.create', 'posts.edit.any', 'posts.delete.any', 'posts.publish',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'media.upload', 'media.manage',
            'users.view', 'users.create', 'users.edit',
            'settings.manage', 'comments.moderate'
        ]);

        $editor->givePermissionTo([
            'posts.view.any', 'posts.create', 'posts.edit.any', 'posts.publish',
            'categories.view', 'categories.create', 'categories.edit',
            'media.upload', 'comments.moderate'
        ]);

        $author->givePermissionTo([
            'posts.create', 'posts.edit.own', 'posts.delete.own',
            'media.upload'
        ]);
    }
}