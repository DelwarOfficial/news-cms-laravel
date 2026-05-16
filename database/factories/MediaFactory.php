<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    public function definition(): array
    {
        $uuid = fake()->uuid();

        return [
            'user_id' => User::factory(),
            'name' => fake()->word() . '.jpg',
            'file_name' => $uuid . '.jpg',
            'file_path' => 'media/' . $uuid . '.jpg',
            'file_url' => 'https://example.com/storage/media/' . $uuid . '.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(1000, 5000000),
            'width' => fake()->numberBetween(400, 1920),
            'height' => fake()->numberBetween(300, 1080),
        ];
    }
}
