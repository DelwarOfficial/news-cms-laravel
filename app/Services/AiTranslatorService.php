<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTranslatorService
{
    public function translatePost(Post $post, string $from = 'bn', string $to = 'en'): array
    {
        $title = $post->title_bn ?: $post->title;
        $summary = $post->summaryForLocale('bn');
        $body = $post->bodyHtmlForLocale('bn');
        $metaTitle = $post->meta_title_bn ?: $title;
        $metaDescription = $post->meta_description_bn ?: $summary;

        if (empty($title) && empty($body)) {
            return [];
        }

        $prompt = "Translate the following Bengali news content to English. Preserve all HTML tags exactly as-is. Do not wrap the response in markdown code blocks. Return a JSON object with keys: title_en, summary_en, body_en, meta_title_en, meta_description_en.\n\n"
            . "---\n"
            . "title_bn: {$title}\n"
            . "summary_bn: {$summary}\n"
            . "body_bn: {$body}\n"
            . "meta_title_bn: {$metaTitle}\n"
            . "meta_description_bn: {$metaDescription}\n"
            . "---";

        $response = $this->callAi($prompt);

        $translated = $this->parseResponse($response);

        if (empty($translated)) {
            Log::warning('AI translation returned empty response for post', ['post_id' => $post->id]);
            return [];
        }

        return $translated;
    }

    private function callAi(string $prompt): ?string
    {
        $provider = config('ai.provider', 'deepseek');
        $apiKey = config('ai.api_key');

        if (empty($apiKey)) {
            Log::warning('AI translation skipped: no API key configured. Set AI_API_KEY in .env');
            return null;
        }

        $endpoint = config("ai.endpoints.{$provider}");
        $model = config("ai.models.{$provider}");

        if (empty($endpoint) || empty($model)) {
            Log::warning("AI translation: unknown provider '{$provider}'");
            return null;
        }

        try {
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
        } catch (\Throwable $e) {
            Log::error('AI translation request failed', ['error' => $e->getMessage()]);
            return null;
        }
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
            ]));
        }

        return [];
    }
}
