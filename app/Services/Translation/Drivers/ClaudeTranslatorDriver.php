<?php

namespace App\Services\Translation\Drivers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeTranslatorDriver extends AbstractTranslatorDriver
{
    public function translate(string $prompt): ?string
    {
        $response = Http::timeout((int) config('translators.timeout', 60))
            ->withHeaders([
                'x-api-key' => $this->config['api_key'],
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])
            ->post($this->config['endpoint'], [
                'model' => $this->config['model'],
                'max_tokens' => (int) config('translators.max_tokens', 8192),
                'temperature' => (float) config('translators.temperature', 0.3),
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Claude translator error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('content.0.text');
    }
}
