<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BulkTranslateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(
        public readonly array $postIds,
        public readonly string $from = 'bn',
        public readonly string $to = 'en',
        public readonly ?string $provider = null,
    ) {
        $this->onQueue('translations');
    }

    public function handle(): void
    {
        $posts = \App\Models\Post::whereIn('id', $this->postIds)->get();

        foreach ($posts as $post) {
            TranslatePostJob::dispatch(
                $post,
                $this->from,
                $this->to,
                $this->provider,
            );
        }
    }
}
