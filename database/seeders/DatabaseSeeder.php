<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            FrontendLocationImportSeeder::class,
            SettingSeeder::class,
        ]);

        if (config('app.env') !== 'production' && env('SEED_DEMO_DATA', false)) {
            $this->call([
                DemoNewsContentSeeder::class,
                MultilingualTestDataSeeder::class,
            ]);
        }
    }
}
