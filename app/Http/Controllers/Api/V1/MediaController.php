<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends BaseApiController
{
    public function index(Request $request)
    {
        $perPage = min((int) $request->get('limit', 30), 100);

        $query = Media::with('folder');

        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }
        if ($request->filled('type')) {
            $query->where('file_type', 'like', $request->type . '%');
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%");
        }

        $media = $query->latest()->paginate($perPage);

        return $this->paginated($media);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimetypes:image/jpeg,image/png,image/gif,image/webp,application/pdf',
            'folder_id' => 'nullable|exists:media_folders,id',
            'name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $fileName, 'public');

        if (! $path) {
            return $this->error('Upload Failed', 'Could not store file.', 500);
        }

        $media = Media::create([
            'folder_id' => $validated['folder_id'] ?? null,
            'user_id' => $request->get('api_key_owner') ?: 1,
            'name' => $validated['name'] ?? $file->getClientOriginalName(),
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
            'file_name' => $media->file_name,
            'file_url' => $media->file_url,
            'file_type' => $media->file_type,
            'file_size' => $media->file_size,
        ]);
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);

        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return $this->noContent();
    }
}
