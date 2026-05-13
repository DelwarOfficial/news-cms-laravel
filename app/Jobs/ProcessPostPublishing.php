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

    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function handle()
    {
        // Clear frontend caches so published post appears immediately
        FrontendCache::flushContent();
        Cache::forget("post_{$this->post->slug}");

        if ($this->post->primaryCategory) {
            Cache::forget("category_{$this->post->primaryCategory->slug}");
        }

        \Log::info("Post published: {$this->post->title}");

        // TODO: Send notification to subscribers
        // TODO: Update sitemap
        // TODO: Push notification via WebSocket / Firebase
    }
}