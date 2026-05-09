<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'NewsCore', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Professional News Content Management System', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => '', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'contact@newscore.com', 'group' => 'general'],
            ['key' => 'default_language', 'value' => 'en', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'Asia/Dhaka', 'group' => 'general'],
            ['key' => 'posts_per_page', 'value' => '12', 'group' => 'general'],
            ['key' => 'enable_comments', 'value' => '1', 'group' => 'general'],
            ['key' => 'meta_title', 'value' => 'NewsCore - Latest News & Breaking Stories', 'group' => 'seo'],
            ['key' => 'meta_description', 'value' => 'Stay updated with the latest breaking news, trending stories, and in-depth analysis from around the world.', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}