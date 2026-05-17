<?php

namespace App\Services\Translation;

use App\Models\Post;
use App\Services\Translation\Contracts\TranslatorDriver;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class TranslatorManager
{
    public function translatePost(Post $post, string $from = 'bn', string $to = 'en', ?string $provider = null): array
    {
        $prompt = $this->postPrompt($post, $from, $to);

        if ($prompt === null) {
            return [];
        }

        return $this->parseJsonResponse($this->translatePrompt($prompt, $provider));
    }

    public function translateText(string $text, string $from = 'bn', string $to = 'en', ?string $provider = null): ?string
    {
        if (trim($text) === '') {
            return $text;
        }

        return $this->translatePrompt($this->textPrompt($text, $from, $to), $provider);
    }

    public function translatePrompt(string $prompt, ?string $provider = null): ?string
    {
        $provider ??= (string) config('translators.default', 'deepseek');

        try {
            return $this->driver($provider)->translate($prompt);
        } catch (\Throwable $e) {
            Log::error("Translator provider {$provider} failed", [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function driver(?string $provider = null): TranslatorDriver
    {
        $provider ??= (string) config('translators.default', 'deepseek');
        $config = config("translators.providers.{$provider}");

        if (! is_array($config)) {
            throw new InvalidArgumentException("Translator provider [{$provider}] is not configured.");
        }

        if (empty($config['api_key'])) {
            throw new InvalidArgumentException("Translator provider [{$provider}] is missing an API key.");
        }

        $driverClass = $config['driver'] ?? null;
        if (! is_string($driverClass) || ! is_subclass_of($driverClass, TranslatorDriver::class)) {
            throw new InvalidArgumentException("Translator provider [{$provider}] has an invalid driver.");
        }

        return new $driverClass($config);
    }

    private function postPrompt(Post $post, string $from, string $to): ?string
    {
        $title = $post->{"title_{$from}"} ?: $post->title;
        $summary = $post->summaryForLocale($from);
        $body = $post->bodyHtmlForLocale($from);
        $metaTitle = $post->{"meta_title_{$from}"} ?: $title;
        $metaDescription = $post->{"meta_description_{$from}"} ?: $summary;

        if (empty($title) && empty($body)) {
            return null;
        }

        return strtr((string) config('translators.prompts.post'), [
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

    private function textPrompt(string $text, string $from, string $to): string
    {
        return strtr((string) config('translators.prompts.text'), [
            '{from}' => $from,
            '{to}' => $to,
            '{text}' => $text,
        ]);
    }

    private function parseJsonResponse(?string $content): array
    {
        if (empty($content)) {
            return [];
        }

        $content = trim($content);

        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $content);
        }

        $decoded = json_decode((string) $content, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_intersect_key($decoded, array_flip([
            'title_en', 'summary_en', 'body_en', 'meta_title_en', 'meta_description_en',
            'title_bn', 'summary_bn', 'body_bn', 'meta_title_bn', 'meta_description_bn',
        ]));
    }
}
