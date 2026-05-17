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
        $ids = Post::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->info('No scheduled posts to publish.');
            return self::SUCCESS;
        }

        Post::whereIn('id', $ids)->update(['status' => 'published', 'published_at' => now()]);

        foreach ($ids as $id) {
            Post::forgetCached($id);
        }

        FrontendCache::flushContent();

        $this->info("Published {$ids->count()} scheduled post(s): {$ids->implode(', ')}");

        return self::SUCCESS;
    }
}
