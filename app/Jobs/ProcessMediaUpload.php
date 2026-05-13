<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessMediaUpload implements ShouldQueue
{
    use Queueable;

    public $mediaId;

    public function __construct($mediaId)
    {
        $this->mediaId = $mediaId;
    }

    public function handle(): void
    {
        Log::info("Processing media ID: {$this->mediaId}");

        // TODO: Add intervention/image WebP conversion here
        // TODO: Generate thumbnails for responsive image sets
    }
}
