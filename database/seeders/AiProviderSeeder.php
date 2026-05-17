<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'name' => 'deepseek',
                'driver_class' => \App\Translation\Drivers\DeepSeekTranslationDriver::class,
                'model' => 'deepseek-chat',
                'endpoint' => 'https://api.deepseek.com/chat/completions',
                'options' => ['temperature' => 0.3, 'max_tokens' => 8192],
                'sort_order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'openai',
                'driver_class' => \App\Translation\Drivers\OpenAITranslationDriver::class,
                'model' => 'gpt-4o-mini',
                'endpoint' => 'https://api.openai.com/v1/chat/completions',
                'options' => ['temperature' => 0.3, 'max_tokens' => 4096],
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'claude',
                'driver_class' => \App\Translation\Drivers\ClaudeTranslationDriver::class,
                'model' => 'claude-sonnet-4-20250514',
                'endpoint' => 'https://api.anthropic.com/v1/messages',
                'options' => ['temperature' => 0.3, 'max_tokens' => 4096],
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'gemini',
                'driver_class' => \App\Translation\Drivers\GeminiTranslationDriver::class,
                'model' => 'gemini-1.5-flash',
                'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models',
                'options' => ['temperature' => 0.3, 'max_tokens' => 4096],
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($providers as $provider) {
            AiProvider::firstOrCreate(
                ['name' => $provider['name']],
                $provider,
            );
        }
    }
}
