<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\GoogleTranslateService;
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
        public readonly string $method = 'ai',
    ) {
        $this->onQueue('translations');
    }

    public function handle(): void
    {
        $posts = Post::whereIn('id', $this->postIds)->get();

        if ($this->method === 'google') {
            $google = app(GoogleTranslateService::class);
            foreach ($posts as $post) {
                $google->translatePost($post, $this->from, $this->to);
            }
            return;
        }

        foreach ($posts as $post) {
            TranslatePostJob::dispatch($post, $this->from, $this->to);
        }
    }
}
