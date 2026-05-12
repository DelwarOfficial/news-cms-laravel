<?php

namespace App\Observers;

use App\Models\ContentPlacement;
use App\Support\FrontendCache;

class ContentPlacementObserver
{
    public function created(ContentPlacement $contentPlacement): void
    {
        FrontendCache::flushContent();
    }

    public function updated(ContentPlacement $contentPlacement): void
    {
        FrontendCache::flushContent();
    }

    public function deleted(ContentPlacement $contentPlacement): void
    {
        FrontendCache::flushContent();
    }
}
