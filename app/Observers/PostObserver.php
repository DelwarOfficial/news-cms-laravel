<?php

namespace App\Observers;

use App\Models\Post;
use App\Support\FrontendCache;

class PostObserver
{
    public function created(Post $post): void
    {
        FrontendCache::flushContent();
    }

    public function updated(Post $post): void
    {
        $changedColumns = array_keys($post->getChanges());

        if (empty(array_diff($changedColumns, ['view_count', 'updated_at']))) {
            return;
        }

        FrontendCache::flushContent();
    }

    public function deleted(Post $post): void
    {
        FrontendCache::flushContent();
    }

    public function restored(Post $post): void
    {
        FrontendCache::flushContent();
    }

    public function forceDeleted(Post $post): void
    {
        FrontendCache::flushContent();
    }
}
