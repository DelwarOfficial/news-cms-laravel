<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;

class ProcessMediaUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 3;

    public function __construct(
        public Media $media,
    ) {
        $this->onQueue('media');
    }

    public function handle(): void
    {
        try {
            if (!str_starts_with($this->media->file_type, 'image/')) {
                Log::info('Non-image media, skipping processing: ' . $this->media->id);
                return;
            }

            $sourcePath = storage_path('app/public/' . $this->media->file_path);
            if (!file_exists($sourcePath)) {
                Log::error('Media file not found: ' . $sourcePath);
                return;
            }

            $image = Image::read($sourcePath);
            $dir   = dirname($sourcePath);
            $base   = pathinfo($this->media->file_name, PATHINFO_FILENAME);
            $originalWidth = $image->width();

            foreach ([400 => 'sm', 800 => 'md', 1200 => 'lg'] as $width => $suffix) {
                if ($originalWidth <= $width) {
                    continue;
                }
                $clone = clone $image;
                $clone->scale(width: $width)
                      ->toWebp(quality: 85)
                      ->save("{$dir}/{$base}-{$suffix}.webp");
            }

            $webpPath = "{$dir}/{$base}.webp";
            $image->toWebp(quality: 85)->save($webpPath);

            $webpUrl = str_replace($this->media->file_name, "{$base}.webp", $this->media->file_url);
            $this->media->update(['file_url' => $webpUrl]);

            Log::info('Media processed: ' . $this->media->id);
        } catch (\Throwable $e) {
            Log::error('Media processing failed', [
                'media_id' => $this->media->id,
                'error'    => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
