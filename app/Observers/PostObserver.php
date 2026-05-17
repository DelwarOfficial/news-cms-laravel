<?php

namespace App\Observers;

use App\Models\Post;
use App\Support\FrontendCache;
use App\Support\ViewCounter;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function created(Post $post): void
    {
        $this->clearCaches($post);
    }

    public function updated(Post $post): void
    {
        $changed = array_keys($post->getChanges());

        $trackedOnly = empty(array_diff($changed, ['view_count', 'updated_at', 'reading_time']));

        if ($trackedOnly) {
            if (in_array('view_count', $changed, true)) {
                app(ViewCounter::class)->syncToDatabase($post->id);
            }
            return;
        }

        $this->clearCaches($post);
    }

    public function deleted(Post $post): void
    {
        $this->clearCaches($post);
    }

    public function restored(Post $post): void
    {
        $this->clearCaches($post);
    }

    public function forceDeleted(Post $post): void
    {
        $this->clearCaches($post);
    }

    private function clearCaches(Post $post): void
    {
        Post::forgetCached($post);
        FrontendCache::flushContent();

        Cache::forget("placement_home.breaking");
        Cache::forget("placement_home.featured");
        Cache::forget("placement_home.sticky");
        Cache::forget("placement_home.trending");
        Cache::forget("placement_home.editors_pick");

        if ($post->primaryCategory) {
            Cache::forget("category_{$post->primaryCategory->slug}");
        }
    }
}
