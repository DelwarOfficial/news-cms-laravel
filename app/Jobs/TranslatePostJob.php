<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\TranslationLog;
use App\Translation\TranslationManager;
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
        public readonly int $targetLanguageId,
        public readonly string $targetLangCode,
        public readonly ?string $preferredDriver = null,
    ) {
        $this->onQueue('translations');
    }

    public function handle(TranslationManager $manager): void
    {
        $driver = $manager->driver($this->preferredDriver);

        $sourceText = implode("\n\nSEPARATOR\n\n", array_filter([
            $this->post->title ?: $this->post->title_bn ?: $this->post->title_en,
            $this->post->body_bn?->toPlainText(),
            $this->post->summary_bn?->toPlainText(),
        ]));

        $result = $driver->translate($sourceText ?: '', 'bn', $this->targetLangCode);

        \DB::transaction(function () use ($result): void {
            PostTranslation::updateOrCreate(
                ['post_id' => $this->post->id, 'language_id' => $this->targetLanguageId],
                [
                    'title'             => $this->translateTitle($result->content),
                    'content'           => $result->content,
                    'locale'            => $this->targetLangCode,
                    'status'            => 'draft',
                    'translation_method' => 'ai',
                    'ai_provider'       => $result->provider,
                ],
            );

            TranslationLog::create([
                'post_id'       => $this->post->id,
                'provider'      => $result->provider,
                'model'         => $result->model,
                'input_tokens'  => $result->inputTokens,
                'output_tokens' => $result->outputTokens,
                'cost_usd'      => $result->costUsd,
                'status'        => 'completed',
            ]);
        });
    }

    public function failed(\Throwable $e): void
    {
        TranslationLog::create([
            'post_id'       => $this->post->id,
            'provider'      => $this->preferredDriver ?? 'unknown',
            'status'        => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }

    private function translateTitle(?string $content): ?string
    {
        $lines = explode("\n", trim($content ?? ''));
        $first = $lines[0] ?? '';

        return mb_strlen($first) > 5 ? $first : null;
    }
}
