<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTranslatorService
{
    public function translatePost(Post $post, string $from = 'bn', string $to = 'en'): array
    {
        $title = $post->{"title_{$from}"} ?: $post->title;
        $summary = $post->summaryForLocale($from);
        $body = $post->bodyHtmlForLocale($from);
        $metaTitle = $post->{"meta_title_{$from}"} ?: $title;
        $metaDescription = $post->{"meta_description_{$from}"} ?: $summary;

        if (empty($title) && empty($body)) {
            return [];
        }

        $targetSuffix = $to === 'en' ? 'en' : 'bn';

        $prompt = "Translate the following {$from} news content to {$to}. Preserve all HTML tags exactly as-is. Do not wrap the response in markdown code blocks. Return a JSON object with keys: title_{$targetSuffix}, summary_{$targetSuffix}, body_{$targetSuffix}, meta_title_{$targetSuffix}, meta_description_{$targetSuffix}.\n\n"
            . "---\n"
            . "title_{$from}: {$title}\n"
            . "summary_{$from}: {$summary}\n"
            . "body_{$from}: {$body}\n"
            . "meta_title_{$from}: {$metaTitle}\n"
            . "meta_description_{$from}: {$metaDescription}\n"
            . "---";

        $response = $this->callAi($prompt);

        if ($response === null) {
            return [];
        }

        $translated = $this->parseResponse($response);

        if (empty($translated)) {
            Log::warning('AI translation returned empty response for post', ['post_id' => $post->id]);
            return [];
        }

        return $translated;
    }

    public function translateText(string $text, string $from = 'bn', string $to = 'en'): ?string
    {
        if (empty(trim($text))) {
            return $text;
        }

        $prompt = "Translate the following {$from} text to {$to}. Return only the translated text, no explanations.\n\n{$text}";

        return $this->callAi($prompt);
    }

    private function callAi(string $prompt): ?string
    {
        $provider = config('ai.provider', 'deepseek');
        $apiKey = $this->resolveApiKey($provider);

        if (empty($apiKey)) {
            Log::warning('AI translation skipped: no API key for provider ' . $provider);
            return null;
        }

        $endpoint = config("ai.endpoints.{$provider}");
        $model = config("ai.models.{$provider}");

        if (empty($endpoint) || empty($model)) {
            Log::warning("AI translation: unknown provider '{$provider}'");
            return null;
        }

        try {
            return match ($provider) {
                'claude' => $this->callClaude($endpoint, $apiKey, $model, $prompt),
                'gemini' => $this->callGemini($endpoint, $apiKey, $model, $prompt),
                default  => $this->callOpenAiCompatible($endpoint, $apiKey, $model, $prompt),
            };
        } catch (\Throwable $e) {
            Log::error("AI translation request failed for provider {$provider}", [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function callOpenAiCompatible(string $endpoint, string $apiKey, string $model, string $prompt): ?string
    {
        $response = Http::timeout(60)
            ->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])
            ->post($endpoint, [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 8192,
            ]);

        if ($response->failed()) {
            Log::error('AI translation API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        return $data['choices'][0]['message']['content'] ?? null;
    }

    private function callClaude(string $endpoint, string $apiKey, string $model, string $prompt): ?string
    {
        $response = Http::timeout(60)
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])
            ->post($endpoint, [
                'model' => $model,
                'max_tokens' => 8192,
                'temperature' => 0.3,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Claude API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        return $data['content'][0]['text'] ?? null;
    }

    private function callGemini(string $endpoint, string $apiKey, string $model, string $prompt): ?string
    {
        $response = Http::timeout(60)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$endpoint}/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 8192,
                ],
            ]);

        if ($response->failed()) {
            Log::error('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        }

        $data = $response->json();

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    private function resolveApiKey(string $provider): ?string
    {
        return match ($provider) {
            'claude' => config('ai.claude_api_key') ?: config('ai.api_key'),
            'gemini' => config('ai.gemini_api_key') ?: config('ai.api_key'),
            default  => config('ai.api_key'),
        };
    }

    private function parseResponse(?string $content): array
    {
        if (empty($content)) {
            return [];
        }

        $content = trim($content);

        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $content);
        }

        $decoded = json_decode($content, true);

        if (is_array($decoded)) {
            return array_intersect_key($decoded, array_flip([
                'title_en', 'summary_en', 'body_en', 'meta_title_en', 'meta_description_en',
                'title_bn', 'summary_bn', 'body_bn', 'meta_title_bn', 'meta_description_bn',
            ]));
        }

        return [];
    }
}
