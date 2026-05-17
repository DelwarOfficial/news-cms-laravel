<?php

namespace App\Services\Translation\Drivers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiCompatibleTranslatorDriver extends AbstractTranslatorDriver
{
    public function translate(string $prompt): ?string
    {
        $response = Http::timeout((int) config('translators.timeout', 60))
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->config['api_key'],
                'Content-Type' => 'application/json',
            ])
            ->post($this->config['endpoint'], [
                'model' => $this->config['model'],
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => (float) config('translators.temperature', 0.3),
                'max_tokens' => (int) config('translators.max_tokens', 8192),
            ]);

        if ($response->failed()) {
            Log::error('OpenAI-compatible translator error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('choices.0.message.content');
    }
}
