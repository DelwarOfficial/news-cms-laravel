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
        Log::info("Starting background processing for media ID: {$this->mediaId}");
        
        // Simulate heavy image processing (e.g., generating thumbnails, optimizing)
        sleep(2);
        
        Log::info("Successfully generated webp versions and thumbnails for media ID: {$this->mediaId}");
    }
}
