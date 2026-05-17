<?php

namespace App\Translation\Drivers;

use App\Models\AiProvider;
use App\Translation\Contracts\TranslationProvider;
use App\Translation\TranslationResult;
use Illuminate\Support\Facades\Http;

class DeepSeekTranslationDriver implements TranslationProvider
{
    private ?AiProvider $dbProvider = null;

    public function __construct(?AiProvider $provider = null)
    {
        $this->dbProvider = $provider;
    }

    public function translate(string $text, string $from, string $to, array $opts = []): TranslationResult
    {
        $apiKey = $this->dbProvider?->api_key ?: config('translation.drivers.deepseek.key');
        $model = $this->dbProvider?->model ?: config('translation.drivers.deepseek.model', 'deepseek-chat');
        $endpoint = $this->dbProvider?->endpoint ?: 'https://api.deepseek.com/chat/completions';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($endpoint, [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $this->prompt($text, $from, $to)]],
        ]);

        if ($response->failed()) {
            return new TranslationResult('', 'deepseek', '', 0, 0, 0.0, false, $response->body());
        }

        $usage = $response->json('usage', []);

        return new TranslationResult(
            content: $response->json('choices.0.message.content', ''),
            provider: 'deepseek',
            model: $model,
            inputTokens: $usage['prompt_tokens'] ?? 0,
            outputTokens: $usage['completion_tokens'] ?? 0,
            costUsd: (($usage['prompt_tokens'] ?? 0) * 0.00000027) + (($usage['completion_tokens'] ?? 0) * 0.0000011),
        );
    }

    private function prompt(string $text, string $from, string $to): string
    {
        return "Professional news translator. Translate {$from}→{$to}. Rules: preserve HTML tags, keep proper nouns, journalistic tone, formal register. Return ONLY translated content.\n---\n{$text}";
    }

    public function getProviderName(): string
    {
        return 'deepseek';
    }

    public function isAvailable(): bool
    {
        $key = $this->dbProvider?->api_key ?: config('translation.drivers.deepseek.key');
        return ! empty($key);
    }
}
