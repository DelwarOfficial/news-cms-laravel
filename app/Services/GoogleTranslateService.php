<?php

namespace App\Services;

use App\Models\Post;
use App\Models\TranslationUsage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleTranslateService
{
    private const COST_PER_CHAR = 20 / 1_000_000; // ~$20 per 1M chars (v3 pay-as-you-go)
    private const CACHE_KEY_MONTHLY = 'translation:monthly_chars:';

    private ?\Google\Cloud\Translate\V3\TranslationServiceClient $client = null;

    /**
     * Translate a single text string.
     */
    public function translate(string $text, string $source = 'bn', string $target = 'en'): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        $this->checkMonthlyLimit(strlen($text));

        try {
            $client = $this->client();
            $response = $client->translateText(
                parent: $this->parent(),
                contents: [$text],
                targetLanguageCode: $target,
                sourceLanguageCode: $source,
            );

            $translated = $response->getTranslations()[0]->getTranslatedText() ?? $text;
            $this->addUsage(null, $source, $target, strlen($text));

            return $translated;
        } catch (\Throwable $e) {
            Log::error('Google Translate API error', [
                'message' => $e->getMessage(),
                'source' => $source,
                'target' => $target,
            ]);
            return $text;
        }
    }

    /**
     * Translate all locale-specific fields on a Post.
     * Returns ['field_key' => 'translated text', ...] for fields that had content.
     */
    public function translatePost(Post $post, string $from = 'bn', string $to = 'en'): array
    {
        $fields = config('google_translate.fields', ['title', 'summary', 'body', 'meta_title', 'meta_description']);
        $payload = [];
        $totalChars = 0;

        foreach ($fields as $field) {
            $sourceField = "{$field}_{$from}";
            $targetField = "{$field}_{$to}";

            // For rich text fields, get HTML content; for plain text, use attribute
            if (in_array($sourceField, (new Post)->getRichTextAttributes())) {
                $sourceText = $post->{$sourceField}?->toHtml() ?? '';
            } else {
                $sourceText = $post->{$sourceField} ?? $post->{$field} ?? '';
            }

            if (empty(trim(strip_tags((string) $sourceText)))) {
                continue;
            }

            $totalChars += mb_strlen($sourceText);
        }

        $this->checkMonthlyLimit($totalChars);

        foreach ($fields as $field) {
            $sourceField = "{$field}_{$from}";
            $targetField = "{$field}_{$to}";

            if (in_array($sourceField, (new Post)->getRichTextAttributes())) {
                $sourceText = $post->{$sourceField}?->toHtml() ?? '';
            } else {
                $sourceText = $post->{$sourceField} ?? $post->{$field} ?? '';
            }

            if (empty(trim(strip_tags((string) $sourceText)))) {
                continue;
            }

            try {
                $client = $this->client();
                $response = $client->translateText(
                    parent: $this->parent(),
                    contents: [$sourceText],
                    targetLanguageCode: $to,
                    sourceLanguageCode: $from,
                );

                $payload[$targetField] = $response->getTranslations()[0]->getTranslatedText() ?? $sourceText;
            } catch (\Throwable $e) {
                Log::error("Google Translate failed for field {$targetField}", [
                    'post_id' => $post->id,
                    'error' => $e->getMessage(),
                ]);
                $payload[$targetField] = $sourceText;
            }
        }

        // Log total usage once
        if ($totalChars > 0) {
            $this->addUsage($post->id, $from, $to, $totalChars);
        }

        return $payload;
    }

    /**
     * Get current month's character usage.
     */
    public function getMonthlyUsage(): int
    {
        return Cache::remember(self::CACHE_KEY_MONTHLY . now()->format('Y_m'), 3600, function () {
            return (int) TranslationUsage::query()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->where('status', 'completed')
                ->sum('character_count');
        });
    }

    /**
     * Get remaining characters for the month.
     */
    public function getMonthlyRemaining(): int
    {
        $limit = (int) config('google_translate.monthly_limit', 0);
        if ($limit <= 0) {
            return PHP_INT_MAX;
        }
        return max(0, $limit - $this->getMonthlyUsage());
    }

    // ── Private helpers ──

    private function checkMonthlyLimit(int $chars): void
    {
        $limit = (int) config('google_translate.monthly_limit', 0);
        if ($limit <= 0) {
            return;
        }

        $remaining = $this->getMonthlyRemaining();
        if ($chars > $remaining) {
            throw new \RuntimeException(
                "Monthly translation limit of {$limit} characters exceeded. "
                . "Used: {$this->getMonthlyUsage()}, Requested: {$chars}, Remaining: {$remaining}"
            );
        }
    }

    private function addUsage(?int $postId, string $from, string $to, int $chars): void
    {
        try {
            TranslationUsage::create([
                'post_id' => $postId,
                'from_locale' => $from,
                'to_locale' => $to,
                'character_count' => $chars,
                'cost_estimate' => round($chars * self::COST_PER_CHAR, 6),
                'status' => 'completed',
            ]);

            // Bust monthly cache
            Cache::forget(self::CACHE_KEY_MONTHLY . now()->format('Y_m'));
        } catch (\Throwable $e) {
            Log::warning('Failed to log translation usage', ['error' => $e->getMessage()]);
        }
    }

    private function client(): \Google\Cloud\Translate\V3\TranslationServiceClient
    {
        if ($this->client === null) {
            $keyPath = $this->resolveKeyPath();

            $config = [];
            if ($keyPath) {
                $config['keyFilePath'] = $keyPath;
            }

            $this->client = new \Google\Cloud\Translate\V3\TranslationServiceClient($config);
        }

        return $this->client;
    }

    private function parent(): string
    {
        $projectId = config('google_translate.project_id');
        return "projects/{$projectId}/locations/global";
    }

    private function resolveKeyPath(): ?string
    {
        $absolute = config('google_translate.key_file_absolute');
        if ($absolute && file_exists($absolute)) {
            return $absolute;
        }

        $relative = config('google_translate.key_file_path');
        if ($relative) {
            $path = storage_path($relative);
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
