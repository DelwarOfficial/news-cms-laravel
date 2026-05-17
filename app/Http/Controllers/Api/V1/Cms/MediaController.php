<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Jobs\ProcessMediaUpload;
use App\Models\Media;
use App\Support\FileUploadSecurity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends BaseApiController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', ...FileUploadSecurity::mediaRules()],
            'alt_text' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');

        $allowedMimes = FileUploadSecurity::mediaMimes();
        $realMime = mime_content_type($file->getRealPath());
        if (!in_array($realMime, $allowedMimes, true)) {
            return $this->error('Upload Rejected', 'File type mismatch detected.', 422);
        }

        $blockedExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'pht', 'phar', 'inc', 'pl', 'py', 'sh', 'exe', 'bat', 'cmd', 'com', 'msi', 'scr', 'jar', 'cgi', 'htaccess'];
        $originalExt = strtolower($file->getClientOriginalExtension());
        if (in_array($originalExt, $blockedExtensions, true)) {
            return $this->error('Upload Rejected', 'File extension is not allowed.', 422);
        }

        $fileName = FileUploadSecurity::storageName($file);
        $path = $file->storeAs('media', $fileName, 'public');

        if (! $path) {
            return $this->error('Upload Failed', 'Could not store file.', 500);
        }

        $media = Media::create([
            'user_id' => $request->get('api_key_owner') ?: 1,
            'name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'file_path' => $path,
            'file_url' => Storage::url($path),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'alt_text' => $validated['alt_text'] ?? null,
        ]);

        ProcessMediaUpload::dispatch($media)->onQueue('media');

        return $this->created([
            'id' => $media->id,
            'name' => $media->name,
            'file_url' => $media->file_url,
            'file_size' => $media->file_size,
            'file_type' => $media->file_type,
        ]);
    }
}
