<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\TranslationUsage;
use App\Services\AiTranslatorService;
use App\Services\GoogleTranslateService;
use App\Support\FrontendCache;
use App\Support\RichTextSanitizer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslatePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public Post $post,
        public string $from = 'bn',
        public string $to = 'en',
        public bool $useGoogleFallback = true,
        public string $method = 'ai_then_google',
    ) {
        $this->onQueue('translations');
    }

    public function handle(AiTranslatorService $ai, GoogleTranslateService $google): void
    {
        $translated = [];

        if ($this->method !== 'google') {
            try {
                $translated = $ai->translatePost($this->post, $this->from, $this->to);

                if (empty($translated)) {
                    Log::info('AI translation returned empty, will use fallback', [
                        'post_id' => $this->post->id,
                    ]);
                } else {
                    Log::info('AI translation completed', [
                        'post_id' => $this->post->id,
                        'provider' => config('translators.default', config('ai.provider')),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('AI translation failed, attempting fallback', [
                    'post_id' => $this->post->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (empty($translated) && ($this->method === 'google' || $this->useGoogleFallback)) {
            try {
                $translated = $google->translatePost($this->post, $this->from, $this->to);

                if (! empty($translated)) {
                    Log::info('Google Translate fallback completed', [
                        'post_id' => $this->post->id,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('Google Translate fallback also failed', [
                    'post_id' => $this->post->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (empty($translated)) {
            $this->logUsage('failed');
            Log::error('All translation methods failed for post', ['post_id' => $this->post->id]);

            if ($this->attempts() >= $this->tries) {
                return;
            }

            $this->release(30);
            return;
        }

        // Apply translations
        $updateData = [];
        foreach ($translated as $field => $value) {
            if ($value !== null && $value !== '') {
                $updateData[$field] = $this->cleanTranslatedValue($field, $value);
            }
        }

        if (! empty($updateData)) {
            $this->post->update($updateData);
        }

        FrontendCache::flushContent();
        $this->logUsage('completed', $updateData);

        Log::info('TranslatePostJob finished', [
            'post_id' => $this->post->id,
            'from' => $this->from,
            'to' => $this->to,
            'fields' => array_keys($updateData),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        $this->logUsage('failed');

        Log::error('TranslatePostJob failed permanently', [
            'post_id' => $this->post->id,
            'error' => $e->getMessage(),
        ]);
    }

    private function logUsage(string $status, array $updateData = []): void
    {
        try {
            $charCount = 0;
            foreach ($updateData as $value) {
                $charCount += mb_strlen((string) $value);
            }

            TranslationUsage::create([
                'post_id' => $this->post->id,
                'from_locale' => $this->from,
                'to_locale' => $this->to,
                'character_count' => $charCount,
                'cost_estimate' => round($charCount * (20 / 1_000_000), 6),
                'status' => $status,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log translation usage', ['error' => $e->getMessage()]);
        }
    }

    private function cleanTranslatedValue(string $field, mixed $value): mixed
    {
        if (! is_string($value) || ! in_array($field, ['body_en', 'body_bn', 'summary_en', 'summary_bn'], true)) {
            return $value;
        }

        return app(RichTextSanitizer::class)->sanitize($value);
    }
}
