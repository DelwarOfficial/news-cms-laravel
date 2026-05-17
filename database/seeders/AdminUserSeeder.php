<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@newscore.com'], [
            'name' => 'Super Admin',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'must_change_password' => true,
        ]);
        $admin->assignRole('Super Admin');
    }
}