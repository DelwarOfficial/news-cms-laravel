<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LanguageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'English',
            'code' => 'en',
            'locale' => 'en_US',
            'is_active' => true,
            'is_default' => true,
            'order' => 1,
        ];
    }
}
