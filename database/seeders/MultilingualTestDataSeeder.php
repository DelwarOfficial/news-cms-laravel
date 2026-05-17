<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MultilingualTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $en = Language::query()->updateOrCreate(
            ['code' => 'en'],
            ['name' => 'English', 'locale' => 'en_US', 'is_default' => false, 'is_active' => true, 'order' => 2],
        );

        $bn = Language::query()->updateOrCreate(
            ['code' => 'bn'],
            ['name' => 'Bangla', 'locale' => 'bn_BD', 'is_default' => true, 'is_active' => true, 'order' => 1],
        );

        $author = User::query()->where('email', 'admin@newscore.com')->first()
            ?? User::factory()->create(['email' => 'admin@newscore.com']);

        $category = Category::query()->first()
            ?? Category::factory()->create(['name' => 'News', 'slug' => 'news', 'status' => 'active']);

        $pairs = [
            [
                'en' => ['Dhaka traffic update', 'Commuters face heavy traffic across key intersections today.'],
                'bn' => ['ঢাকার ট্রাফিক আপডেট', 'আজ গুরুত্বপূর্ণ মোড়গুলোতে যানজট বেশি।'],
            ],
            [
                'en' => ['Economy watch', 'The market saw mixed signals as investors stayed cautious.'],
                'bn' => ['অর্থনীতির খবর', 'বিনিয়োগকারীদের সতর্ক অবস্থানে বাজারে মিশ্র প্রতিক্রিয়া দেখা গেছে।'],
            ],
            [
                'en' => ['Sports roundup', 'A late goal decided the match in stoppage time.'],
                'bn' => ['খেলাধুলার খবর', 'যোগ করা সময়ে শেষ মুহূর্তের গোলে ম্যাচের ফল নির্ধারিত হয়েছে।'],
            ],
        ];

        foreach ($pairs as $index => $pair) {
            [$enTitle, $enBody] = $pair['en'];
            [$bnTitle, $bnBody] = $pair['bn'];

            $baseSlug = Str::slug($enTitle) ?: ('post-'.$index);

            Post::query()->updateOrCreate(
                ['slug' => $baseSlug],
                [
                    'user_id' => $author->id,
                    'language_id' => $bn->id,
                    'primary_category_id' => $category->id,
                    'title' => $bnTitle,
                    'title_en' => $enTitle,
                    'title_bn' => $bnTitle,
                    'slug_en' => $baseSlug,
                    'slug_bn' => $baseSlug,
                    'excerpt' => $bnBody,
                    'content' => $bnBody,
                    'status' => 'published',
                    'published_at' => now()->subHours($index + 1),
                    'post_format' => 'standard',
                    'allow_comments' => true,
                ],
            );
        }
    }
}
