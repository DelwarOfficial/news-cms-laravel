<?php

namespace App\Observers;

use App\Models\Post;
use App\Support\FrontendCache;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function created(Post $post): void
    {
        $this->clearCaches($post);
    }

    public function updated(Post $post): void
    {
        $changedColumns = array_keys($post->getChanges());

        if (empty(array_diff($changedColumns, ['view_count', 'updated_at']))) {
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
        FrontendCache::flushContent();

        Cache::forget("post_{$post->slug}");
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
