<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';
    protected $description = 'Publish posts that are scheduled and past their published_at time';

    public function handle(): int
    {
        $count = Post::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->update(['status' => 'published', 'published_at' => now()]);

        $this->info("Published {$count} scheduled post(s).");

        return self::SUCCESS;
    }
}
