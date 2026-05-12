<?php

namespace App\Observers;

use App\Models\Advertisement;
use App\Support\FrontendCache;

class AdvertisementObserver
{
    public function created(Advertisement $advertisement): void
    {
        FrontendCache::flushAds();
    }

    public function updated(Advertisement $advertisement): void
    {
        FrontendCache::flushAds();
    }

    public function deleted(Advertisement $advertisement): void
    {
        FrontendCache::flushAds();
    }
}
