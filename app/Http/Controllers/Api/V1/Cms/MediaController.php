<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Api\V1\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends BaseApiController
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimetypes:image/jpeg,image/png,image/gif,image/webp,application/pdf',
            'alt_text' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $fileName, 'public');

        if (! $path) {
            return $this->error('Upload Failed', 'Could not store file.', 500);
        }

        $media = \App\Models\Media::create([
            'user_id' => $request->get('api_key_owner') ?: 1,
            'name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'file_path' => $path,
            'file_url' => Storage::url($path),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'alt_text' => $validated['alt_text'] ?? null,
        ]);

        return $this->created([
            'id' => $media->id,
            'name' => $media->name,
            'file_url' => $media->file_url,
            'file_size' => $media->file_size,
            'file_type' => $media->file_type,
        ]);
    }
}
