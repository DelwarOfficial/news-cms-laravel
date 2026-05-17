<?php

namespace App\Services\Translation;

use App\Models\AiProvider;
use App\Models\Post;
use App\Models\TranslationLog;
use App\Models\TranslationPrompt;
use App\Translation\Contracts\TranslationProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class TranslationService
{
    private const CHUNK_SIZE = 3000;

    private ?float $monthlyCostLimit = null;

    public function __construct()
    {
        $limit = config('translation.monthly_cost_limit');
        $this->monthlyCostLimit = $limit ? (float) $limit : null;
    }

    public function translatePost(
        Post $post,
        string $from = 'bn',
        string $to = 'en',
        ?string $preferredProvider = null,
    ): array {
        $title = $post->{"title_{$from}"} ?: $post->title;
        $body = $this->extractPlainText($post, $from);
        $summary = $this->extractSummary($post, $from);
        $metaTitle = $post->{"meta_title_{$from}"} ?: $title;
        $metaDescription = $post->{"meta_description_{$from}"} ?: $summary;

        if (empty($title) && empty($body)) {
            return ['translated' => [], 'error' => 'No source content to translate'];
        }

        $bodyChunks = $this->chunkText($body, self::CHUNK_SIZE);

        $provider = $this->resolveProvider($preferredProvider);
        $driver = $provider->resolveDriver();
        $prompt = $this->buildPostPrompt($title, $summary, $bodyChunks[0] ?? '', $metaTitle, $metaDescription, $from, $to);

        $this->checkCostLimit();

        $result = $driver->translate($prompt, $from, $to);

        if (! $result->success) {
            return $this->attemptFallback($preferredProvider, $prompt, $from, $to, $result->error ?? '');
        }

        $parsed = $this->parsePostResponse($result->content, $to);

        if (empty($parsed)) {
            return $this->attemptFallback($preferredProvider, $prompt, $from, $to, 'Failed to parse response');
        }

        if (count($bodyChunks) > 1) {
            $remaining = array_slice($bodyChunks, 1);
            foreach ($remaining as $chunk) {
                $chunkResult = $driver->translate(
                    "Continue translation. Translate the following {$from}→{$to}. Return ONLY the translated text.\n---\n{$chunk}",
                    $from,
                    $to,
                );
                if ($chunkResult->success) {
                    $parsed['body'] = ($parsed['body'] ?? '') . "\n\n" . $chunkResult->content;
                }
            }
        }

        return [
            'translated' => $parsed,
            'provider' => $result->provider,
            'provider_id' => $provider->id ?? null,
            'model' => $result->model,
            'input_tokens' => $result->inputTokens,
            'output_tokens' => $result->outputTokens,
            'total_chars' => mb_strlen($prompt) + mb_strlen($result->content),
            'cost_usd' => $result->costUsd,
        ];
    }

    public function translateText(
        string $text,
        string $from = 'bn',
        string $to = 'en',
        ?string $preferredProvider = null,
    ): ?string {
        if (trim($text) === '') {
            return $text;
        }

        $this->checkCostLimit();

        $provider = $this->resolveProvider($preferredProvider);
        $driver = $provider->resolveDriver();

        $promptTemplate = TranslationPrompt::getTemplate('text')
            ?? config('translators.prompts.text')
            ?? "Translate the following {from} text to {to}. Return only the translated text.\n\n{text}";

        $prompt = strtr($promptTemplate, [
            '{from}' => $from,
            '{to}' => $to,
            '{text}' => $text,
        ]);

        $result = $driver->translate($prompt, $from, $to);

        return $result->success ? $result->content : null;
    }

    private function resolveProvider(?string $preferred = null): AiProvider
    {
        $name = $preferred ?? config('translation.default', 'deepseek');

        $provider = AiProvider::active()->where('name', $name)->first();

        if ($provider) {
            if (empty($provider->api_key)) {
                $provider->api_key = $this->envFallbackKey($name);
            }
            return $provider;
        }

        return $this->createVirtualProvider($name);
    }

    private function createVirtualProvider(string $name): AiProvider
    {
        $driverMap = [
            'deepseek' => \App\Translation\Drivers\DeepSeekTranslationDriver::class,
            'openai' => \App\Translation\Drivers\OpenAITranslationDriver::class,
            'claude' => \App\Translation\Drivers\ClaudeTranslationDriver::class,
            'gemini' => \App\Translation\Drivers\GeminiTranslationDriver::class,
        ];

        $provider = new AiProvider();
        $provider->name = $name;
        $provider->driver_class = $driverMap[$name] ?? $driverMap['deepseek'];
        $provider->api_key = $this->envFallbackKey($name);
        $provider->model = config("translation.drivers.{$name}.model");
        $provider->endpoint = null;

        return $provider;
    }

    private function envFallbackKey(string $provider): string
    {
        return match ($provider) {
            'deepseek' => env('DEEPSEEK_API_KEY'),
            'openai' => env('OPENAI_API_KEY'),
            'claude' => env('CLAUDE_API_KEY'),
            'gemini' => env('GEMINI_API_KEY'),
            default => '',
        };
    }

    private function attemptFallback(?string $preferred, string $prompt, string $from, string $to, string $error): array
    {
        $fallbackOrder = config('translation.fallback_order', ['deepseek', 'claude', 'openai', 'gemini']);

        $fallbackOrder = array_values(array_filter($fallbackOrder, fn ($p) => $p !== $preferred));

        foreach ($fallbackOrder as $fb) {
            try {
                $provider = $this->resolveProvider($fb);
                $driver = $provider->resolveDriver();

                if (! $driver->isAvailable()) {
                    continue;
                }

                $result = $driver->translate($prompt, $from, $to);

                if ($result->success) {
                    $parsed = $this->parsePostResponse($result->content, $to);
                    if (! empty($parsed)) {
                        return [
                            'translated' => $parsed,
                            'provider' => $result->provider,
                            'provider_id' => $provider->id ?? null,
                            'model' => $result->model,
                            'input_tokens' => $result->inputTokens,
                            'output_tokens' => $result->outputTokens,
                            'total_chars' => mb_strlen($prompt) + mb_strlen($result->content),
                            'cost_usd' => $result->costUsd,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("Translation fallback {$fb} failed", ['error' => $e->getMessage()]);
            }
        }

        return ['translated' => [], 'error' => $error, 'provider' => $preferred ?? 'unknown'];
    }

    private function buildPostPrompt(
        string $title,
        string $summary,
        string $body,
        string $metaTitle,
        string $metaDescription,
        string $from,
        string $to,
    ): string {
        $template = TranslationPrompt::getTemplate('post')
            ?? config('translators.prompts.post');

        if ($template) {
            return strtr($template, [
                '{from}' => $from,
                '{to}' => $to,
                '{target}' => $to,
                '{title}' => $title,
                '{summary}' => $summary,
                '{body}' => $body,
                '{meta_title}' => $metaTitle,
                '{meta_description}' => $metaDescription,
            ]);
        }

        return "Professional news translator. Translate {$from}→{$to}. "
            . "Rules: preserve HTML tags, keep proper nouns, journalistic tone, formal register. "
            . "Return ONLY translated content.\n---\n"
            . "Title: {$title}\nBody: {$body}";
    }

    private function parsePostResponse(string $content, string $targetLocale): array
    {
        $content = trim($content);

        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $content);
        }

        $decoded = json_decode($content, true);

        if (is_array($decoded)) {
            $keyMap = [
                "title_{$targetLocale}" => 'title',
                'title' => 'title',
                "summary_{$targetLocale}" => 'summary',
                'summary' => 'summary',
                "body_{$targetLocale}" => 'body',
                'body' => 'body',
                "meta_title_{$targetLocale}" => 'meta_title',
                'meta_title' => 'meta_title',
                "meta_description_{$targetLocale}" => 'meta_description',
                'meta_description' => 'meta_description',
                "slug_{$targetLocale}" => 'slug',
                'slug' => 'slug',
            ];

            $result = [];
            foreach ($keyMap as $key => $normalized) {
                if (isset($decoded[$key]) && is_string($decoded[$key]) && $decoded[$key] !== '') {
                    $result[$normalized] ??= $decoded[$key];
                }
            }

            return $result;
        }

        return [
            'title' => explode("\n", $content)[0] ?? '',
            'body' => $content,
        ];
    }

    private function checkCostLimit(): void
    {
        if ($this->monthlyCostLimit === null) {
            return;
        }

        $monthlyCost = TranslationLog::completed()
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('cost_usd');

        if ($monthlyCost >= $this->monthlyCostLimit) {
            throw new RuntimeException(
                "Monthly translation cost limit of \${$this->monthlyCostLimit} reached (\${$monthlyCost} used)."
            );
        }
    }

    private function chunkText(string $text, int $maxLen): array
    {
        $text = trim($text);
        if (mb_strlen($text) <= $maxLen) {
            return [$text];
        }

        $paragraphs = explode("\n\n", $text);
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $para) {
            $para = trim($para);
            if ($para === '') {
                continue;
            }
            if (mb_strlen($current . "\n\n" . $para) > $maxLen && $current !== '') {
                $chunks[] = $current;
                $current = $para;
            } else {
                $current = $current === '' ? $para : $current . "\n\n" . $para;
            }
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }

    private function extractPlainText(Post $post, string $locale): string
    {
        $field = "body_{$locale}";
        if (isset($post->{$field}) && method_exists($post->{$field}, 'toPlainText')) {
            return $post->{$field}->toPlainText();
        }
        if (isset($post->{$field}) && is_string($post->{$field})) {
            return strip_tags($post->{$field});
        }
        return $post->body_bn?->toPlainText() ?? '';
    }

    private function extractSummary(Post $post, string $locale): string
    {
        $field = "summary_{$locale}";
        if (isset($post->{$field}) && method_exists($post->{$field}, 'toPlainText')) {
            return $post->{$field}->toPlainText();
        }
        if (isset($post->{$field}) && is_string($post->{$field})) {
            return strip_tags($post->{$field});
        }
        return '';
    }
}
