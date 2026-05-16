<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\GoogleTranslateService;
use App\Support\FrontendCache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TranslatePostWithGoogle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 3;

    public function __construct(
        public Post $post,
        public string $from = 'bn',
        public string $to = 'en',
    ) {
        $this->onQueue('translations');
    }

    public function handle(GoogleTranslateService $translator): void
    {
        try {
            $translated = $translator->translatePost($this->post, $this->from, $this->to);

            if (empty($translated)) {
                Log::info('Google Translate: no content to translate', [
                    'post_id' => $this->post->id,
                ]);
                return;
            }

            $updateData = [];

            foreach ($translated as $field => $value) {
                $updateData[$field] = $value;
            }

            if (! empty($updateData)) {
                $this->post->update($updateData);
            }

            FrontendCache::flushContent();

            Log::info('Google Translate completed', [
                'post_id' => $this->post->id,
                'from' => $this->from,
                'to' => $this->to,
                'fields' => array_keys($updateData),
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Translate job failed', [
                'post_id' => $this->post->id,
                'error' => $e->getMessage(),
            ]);

            if ($this->attempts() >= $this->tries) {
                // Log final failure as usage record with failed status
                try {
                    \App\Models\TranslationUsage::create([
                        'post_id' => $this->post->id,
                        'from_locale' => $this->from,
                        'to_locale' => $this->to,
                        'character_count' => 0,
                        'cost_estimate' => 0,
                        'status' => 'failed',
                    ]);
                } catch (\Throwable $inner) {
                    // Silently fail on usage logging
                }
            }

            throw $e;
        }
    }
}
