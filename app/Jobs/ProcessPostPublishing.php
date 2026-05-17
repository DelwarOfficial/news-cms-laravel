<?php

namespace App\Jobs;

use App\Models\Post;
use App\Support\FrontendCache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ProcessPostPublishing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries = 3;

    public function __construct(
        public Post $post,
    ) {
        $this->post->loadMissing('primaryCategory');
        $this->onQueue('publishing');
    }

    public function handle()
    {
        FrontendCache::flushContent();

        Post::forgetCached($this->post);
        Cache::forget("post_{$this->post->slug}");

        if ($this->post->relationLoaded('primaryCategory') && $this->post->primaryCategory) {
            Cache::forget("category_{$this->post->primaryCategory->slug}");
        }

        \Log::info("Post published: {$this->post->title}");
    }
}