<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

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
        if (! class_exists(ImageManager::class)) {
            // Production-safe: do not fail the upload pipeline if image processing deps are missing.
            Log::warning('Skipping media processing: Intervention Image is not installed.');
            return;
        }

        if (! $this->isImage($this->media->file_type)) {
            Log::info("Skipping image processing for non-image media ID: {$this->media->id}");
            return;
        }

        $disk = Storage::disk('public');
        $path = $this->media->file_path;

        if (! $disk->exists($path)) {
            Log::warning("Media file not found for processing: {$path}");
            return;
        }

        try {
            $manager = ImageManager::withDriver(config('image.driver', 'gd'));
            $image = $manager->read($disk->path($path));
            $originalExt = pathinfo($this->media->file_name, PATHINFO_EXTENSION);
            $baseName = pathinfo($this->media->file_name, PATHINFO_FILENAME);
            $directory = dirname($path);

            $webpPath = "{$directory}/{$baseName}.webp";
            $image->toWebp(config('image.webp_quality', 80))->save($disk->path($webpPath));

            $thumbnails = [];
            foreach (config('image.thumbnail_sizes', []) as $size => [$width, $height]) {
                $thumbImage = $manager->read($disk->path($path));
                $thumbImage->cover($width, $height);
                $thumbPath = "{$directory}/{$baseName}_{$size}.{$originalExt}";
                $thumbImage->save($disk->path($thumbPath));
                $thumbnails[$size] = Storage::url($thumbPath);

                $webpThumbPath = "{$directory}/{$baseName}_{$size}.webp";
                $thumbImage->toWebp(config('image.webp_quality', 80))->save($disk->path($webpThumbPath));
                $thumbnails["{$size}_webp"] = Storage::url($webpThumbPath);
            }

            $this->media->update([
                'file_url' => Storage::url($webpPath),
                'file_path_webp' => $webpPath,
                'thumbnails' => $thumbnails,
            ]);

            Log::info("Media processed successfully: {$this->media->id}");
        } catch (\Throwable $e) {
            Log::error("Media processing failed for ID {$this->media->id}: " . $e->getMessage());
            if ($this->attempts() >= $this->tries) {
                return;
            }
            throw $e;
        }
    }

    private function isImage(?string $mimeType): bool
    {
        return $mimeType !== null && str_starts_with($mimeType, 'image/')
            && ! in_array($mimeType, ['image/svg+xml', 'image/gif'], true);
    }
}
