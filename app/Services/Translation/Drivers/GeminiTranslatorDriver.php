<?php

namespace App\Services\Translation\Drivers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiTranslatorDriver extends AbstractTranslatorDriver
{
    public function translate(string $prompt): ?string
    {
        $endpoint = rtrim($this->config['endpoint'], '/');
        $model = $this->config['model'];

        $response = Http::timeout((int) config('translators.timeout', 60))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$endpoint}/{$model}:generateContent?key={$this->config['api_key']}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => (float) config('translators.temperature', 0.3),
                    'maxOutputTokens' => (int) config('translators.max_tokens', 8192),
                ],
            ]);

        if ($response->failed()) {
            Log::error('Gemini translator error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('candidates.0.content.parts.0.text');
    }
}
