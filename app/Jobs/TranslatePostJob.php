<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\TranslationLog;
use App\Services\Translation\TranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TranslatePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(
        public readonly Post $post,
        public readonly string $from = 'bn',
        public readonly string $to = 'en',
        public readonly ?string $preferredProvider = null,
    ) {
        $this->onQueue('translations');
    }

    public function handle(TranslationService $service): void
    {
        $start = hrtime(true);

        $result = $service->translatePost(
            $this->post,
            $this->from,
            $this->to,
            $this->preferredProvider,
        );

        $durationMs = (int) ((hrtime(true) - $start) / 1_000_000);

        if (empty($result['translated'])) {
            TranslationLog::create([
                'translatable_type' => Post::class,
                'translatable_id' => $this->post->id,
                'provider_name' => $result['provider'] ?? $this->preferredProvider ?? 'unknown',
                'model' => $result['model'] ?? null,
                'from_locale' => $this->from,
                'to_locale' => $this->to,
                'status' => 'failed',
                'error_message' => $result['error'] ?? 'Translation returned empty',
                'total_chars' => $result['total_chars'] ?? 0,
                'duration_ms' => $durationMs,
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff[$this->attempts() - 1] ?? 300);
            }

            return;
        }

        $t = $result['translated'];

        \DB::transaction(function () use ($t, $result, $durationMs): void {
            PostTranslation::updateOrCreate(
                ['post_id' => $this->post->id, 'locale' => $this->to],
                [
                    'title' => $t['title'] ?? null,
                    'content' => $t['body'] ?? null,
                    'slug' => $t['slug'] ?? null,
                    'summary' => $t['summary'] ?? null,
                    'meta_title' => $t['meta_title'] ?? null,
                    'meta_description' => $t['meta_description'] ?? null,
                    'status' => 'draft',
                    'translation_method' => 'ai',
                    'ai_provider' => $result['provider'],
                ],
            );

            TranslationLog::create([
                'translatable_type' => Post::class,
                'translatable_id' => $this->post->id,
                'provider_id' => $result['provider_id'] ?? null,
                'provider_name' => $result['provider'],
                'model' => $result['model'],
                'from_locale' => $this->from,
                'to_locale' => $this->to,
                'input_tokens' => $result['input_tokens'] ?? 0,
                'output_tokens' => $result['output_tokens'] ?? 0,
                'total_chars' => $result['total_chars'] ?? 0,
                'cost_usd' => $result['cost_usd'] ?? 0.0,
                'duration_ms' => $durationMs,
                'status' => 'completed',
            ]);
        });
    }

    public function failed(\Throwable $e): void
    {
        TranslationLog::create([
            'translatable_type' => Post::class,
            'translatable_id' => $this->post->id,
            'provider_name' => $this->preferredProvider ?? 'unknown',
            'from_locale' => $this->from,
            'to_locale' => $this->to,
            'status' => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }
}
