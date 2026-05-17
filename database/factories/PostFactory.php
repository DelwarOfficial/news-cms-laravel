<?php

namespace Database\Factories;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'user_id' => \App\Models\User::factory(),
            'language_id' => function (array $attributes) {
                return Language::query()->value('id') ?? Language::factory()->create()->id;
            },
            'title' => $title,
            'slug' => strtolower(str_replace(' ', '-', $title)) . '-' . fake()->unique()->randomNumber(5),
            'content' => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'excerpt' => fake()->sentence(),
            'status' => 'published',
            'published_at' => now(),
            'post_format' => 'standard',
            'allow_comments' => true,
            'show_author' => true,
            'show_publish_date' => true,
        ];
    }
}
