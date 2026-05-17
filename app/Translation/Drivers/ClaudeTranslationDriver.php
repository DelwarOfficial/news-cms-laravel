<?php

namespace App\Translation\Drivers;

use App\Models\AiProvider;
use App\Translation\Contracts\TranslationProvider;
use App\Translation\TranslationResult;
use Illuminate\Support\Facades\Http;

class ClaudeTranslationDriver implements TranslationProvider
{
    private ?AiProvider $dbProvider = null;

    public function __construct(?AiProvider $provider = null)
    {
        $this->dbProvider = $provider;
    }

    public function translate(string $text, string $from, string $to, array $opts = []): TranslationResult
    {
        $apiKey = $this->dbProvider?->api_key ?: config('translation.drivers.claude.key');
        $model = $this->dbProvider?->model ?: config('translation.drivers.claude.model', 'claude-sonnet-4-20250514');
        $endpoint = $this->dbProvider?->endpoint ?: 'https://api.anthropic.com/v1/messages';

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(30)->post($endpoint, [
            'model' => $model,
            'max_tokens' => 4096,
            'messages' => [['role' => 'user', 'content' => $this->prompt($text, $from, $to)]],
        ]);

        if ($response->failed()) {
            return new TranslationResult('', 'claude', '', 0, 0, 0.0, false, $response->body());
        }

        $usage = $response->json('usage', []);

        return new TranslationResult(
            content: $response->json('content.0.text', ''),
            provider: 'claude',
            model: $model,
            inputTokens: $usage['input_tokens'] ?? 0,
            outputTokens: $usage['output_tokens'] ?? 0,
            costUsd: (($usage['input_tokens'] ?? 0) * 0.000003) + (($usage['output_tokens'] ?? 0) * 0.000015),
        );
    }

    private function prompt(string $text, string $from, string $to): string
    {
        return "Professional news translator. Translate {$from}→{$to}. Rules: preserve HTML tags, keep proper nouns, journalistic tone, formal register. Return ONLY translated content.\n---\n{$text}";
    }

    public function getProviderName(): string
    {
        return 'claude';
    }

    public function isAvailable(): bool
    {
        $key = $this->dbProvider?->api_key ?: config('translation.drivers.claude.key');
        return ! empty($key);
    }
}
