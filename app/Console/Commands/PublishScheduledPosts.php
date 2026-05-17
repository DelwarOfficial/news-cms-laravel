<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Support\FrontendCache;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';
    protected $description = 'Publish posts that are scheduled and past their published_at time';

    public function handle(): int
    {
        $posts = Post::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get(['id', 'slug', 'slug_en', 'slug_bn']);

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts to publish.');
            return self::SUCCESS;
        }

        $ids = $posts->pluck('id');
        Post::whereIn('id', $ids)->update(['status' => 'published', 'published_at' => now()]);

        foreach ($posts as $post) {
            Post::forgetCached($post);
        }

        FrontendCache::flushContent();

        $this->info("Published {$ids->count()} scheduled post(s): {$ids->implode(', ')}");

        return self::SUCCESS;
    }
}
