<?php

namespace App\Observers;

use App\Models\Category;
use App\Support\FrontendCache;

class CategoryObserver
{
    public function created(Category $category): void
    {
        FrontendCache::flushCategories();
    }

    public function updated(Category $category): void
    {
        FrontendCache::flushCategories();
    }

    public function deleted(Category $category): void
    {
        FrontendCache::flushCategories();
    }
}
