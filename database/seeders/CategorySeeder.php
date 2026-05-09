<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Politics', 'slug' => 'politics'],
            ['name' => 'Business', 'slug' => 'business'],
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'World', 'slug' => 'world'],
            ['name' => 'Sports', 'slug' => 'sports'],
            ['name' => 'Entertainment', 'slug' => 'entertainment'],
            ['name' => 'Health', 'slug' => 'health'],
            ['name' => 'Science', 'slug' => 'science'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}