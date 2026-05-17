<?php

namespace App\Translation\Drivers;

use App\Translation\Contracts\TranslationProvider;
use App\Translation\TranslationResult;
use Illuminate\Support\Facades\Http;

class GeminiTranslationDriver implements TranslationProvider
{
    public function translate(string $text, string $from, string $to, array $opts = []): TranslationResult
    {
        $model = config('translation.drivers.gemini.model', 'gemini-1.5-flash');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . config('translation.drivers.gemini.key'), [
            'contents' => [['parts' => [['text' => $this->prompt($text, $from, $to)]]]],
        ]);

        if ($response->failed()) {
            return new TranslationResult('', 'gemini', '', 0, 0, 0.0, false, $response->body());
        }

        return new TranslationResult(
            content      : $response->json('candidates.0.content.parts.0.text', ''),
            provider     : 'gemini',
            model        : $model,
            inputTokens  : 0,
            outputTokens : 0,
            costUsd      : 0.0,
        );
    }

    private function prompt(string $text, string $from, string $to): string
    {
        return "Professional news translator. Translate {$from}→{$to}. Rules: preserve HTML tags, keep proper nouns, journalistic tone, formal register. Return ONLY translated content.\n---\n{$text}";
    }

    public function getProviderName(): string
    {
        return 'gemini';
    }

    public function isAvailable(): bool
    {
        return ! empty(config('translation.drivers.gemini.key'));
    }
}
