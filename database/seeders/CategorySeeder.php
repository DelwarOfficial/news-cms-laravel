<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            [
                'name' => 'Bangladesh', 'name_bn' => 'বাংলাদেশ', 'slug' => 'bangladesh',
                'children' => [
                    ['name' => 'National', 'name_bn' => 'জাতীয়', 'slug' => 'national'],
                    ['name' => 'Dhaka', 'name_bn' => 'রাজধানী', 'slug' => 'dhaka'],
                    ['name' => 'Crime', 'name_bn' => 'অপরাধ', 'slug' => 'crime'],
                    ['name' => 'Accidents', 'name_bn' => 'দুর্ঘটনা', 'slug' => 'accidents'],
                    ['name' => 'Law Justice', 'name_bn' => 'আইন-বিচার', 'slug' => 'law-justice'],
                    ['name' => 'Politics', 'name_bn' => 'রাজনীতি', 'slug' => 'politics'],
                ],
            ],
            [
                'name' => 'Economy', 'name_bn' => 'অর্থনীতি', 'slug' => 'economy',
                'children' => [
                    ['name' => 'Stock Market', 'name_bn' => 'শেয়ারবাজার', 'slug' => 'stock-market'],
                    ['name' => 'Banking Insurance', 'name_bn' => 'ব্যাংকিং ও বীমা', 'slug' => 'banking-insurance'],
                    ['name' => 'Industry', 'name_bn' => 'শিল্প', 'slug' => 'industry'],
                    ['name' => 'Agriculture', 'name_bn' => 'কৃষি', 'slug' => 'agriculture'],
                ],
            ],
            ['name' => 'World', 'name_bn' => 'আন্তর্জাতিক', 'slug' => 'world'],
            ['name' => 'Country News', 'name_bn' => 'সারাদেশ', 'slug' => 'country-news'],
            ['name' => 'Entertainment', 'name_bn' => 'বিনোদন', 'slug' => 'entertainment'],
            [
                'name' => 'Sports', 'name_bn' => 'খেলাধুলা', 'slug' => 'sports',
                'children' => [
                    ['name' => 'Football', 'name_bn' => 'ফুটবল', 'slug' => 'football'],
                    ['name' => 'Cricket', 'name_bn' => 'ক্রিকেট', 'slug' => 'cricket'],
                    ['name' => 'Other Sports', 'name_bn' => 'অন্যান্য খেলা', 'slug' => 'other-sports'],
                ],
            ],
            [
                'name' => 'Jobs', 'name_bn' => 'চাকরি', 'slug' => 'jobs',
                'children' => [
                    ['name' => 'Government Jobs', 'name_bn' => 'সরকারি চাকরি', 'slug' => 'government-jobs'],
                    ['name' => 'Private Jobs', 'name_bn' => 'বেসরকারি চাকরি', 'slug' => 'private-jobs'],
                ],
            ],
            [
                'name' => 'Lifestyle', 'name_bn' => 'জীবনযাপন', 'slug' => 'lifestyle',
                'children' => [
                    ['name' => 'Health', 'name_bn' => 'স্বাস্থ্য', 'slug' => 'health'],
                    ['name' => 'Beauty', 'name_bn' => 'রূপচর্চা', 'slug' => 'beauty'],
                    ['name' => 'Recipes', 'name_bn' => 'খাবার', 'slug' => 'recipes'],
                ],
            ],
            ['name' => 'Videos', 'name_bn' => 'ভিডিও', 'slug' => 'videos'],
            ['name' => 'Special', 'name_bn' => 'বিশেষ', 'slug' => 'dhaka-magazine-special'],
            [
                'name' => 'Others News', 'name_bn' => 'অন্যান্য', 'slug' => 'others-news',
                'children' => [
                    ['name' => 'Religion', 'name_bn' => 'ধর্ম', 'slug' => 'religion'],
                    ['name' => 'Technology', 'name_bn' => 'তথ্য-প্রযুক্তি', 'slug' => 'technology'],
                    ['name' => 'Education', 'name_bn' => 'শিক্ষা', 'slug' => 'education'],
                    ['name' => 'Opinion', 'name_bn' => 'মতামত', 'slug' => 'opinion'],
                    ['name' => 'Expatriates', 'name_bn' => 'প্রবাস', 'slug' => 'expatriates'],
                ],
            ],
        ];

        foreach ($structure as $parentData) {
            $children = $parentData['children'] ?? [];
            unset($parentData['children']);

            $parent = Category::updateOrCreate(
                ['slug' => $parentData['slug']],
                [
                    'name' => $parentData['name'],
                    'parent_id' => null,
                    'status' => 'active',
                ]
            );

            foreach ($children as $childData) {
                Category::updateOrCreate(
                    ['slug' => $childData['slug']],
                    [
                        'name' => $childData['name'],
                        'parent_id' => $parent->id,
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}