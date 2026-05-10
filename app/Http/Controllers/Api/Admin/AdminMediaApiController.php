<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminMediaApiController extends Controller
{
    protected $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];

    public function index(Request $request)
    {
        if ($request->user()->cannot('viewAny', Media::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'folder_id' => 'nullable|exists:media_folders,id',
        ]);

        $query = Media::with('user', 'folder');
        
        if ($request->has('folder_id')) {
            $query->where('folder_id', $validated['folder_id']);
        }

        $media = $query->latest()->paginate($validated['per_page'] ?? 20);
        
        return response()->json([
            'status' => 'success',
            'data' => $media
        ]);
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Media::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240|mimetypes:' . implode(',', $this->allowedMimes),
            'folder_id' => 'nullable|exists:media_folders,id',
            'alt_text' => 'nullable|max:255',
        ]);

        try {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('media', $fileName, 'public');

            if (!$path) {
                throw new \Exception('Failed to store file.');
            }

            $media = Media::create([
                'folder_id' => $validated['folder_id'] ?? null,
                'user_id' => $request->user()->id,
                'name' => $file->getClientOriginalName(),
                'file_name' => $fileName,
                'file_path' => $path,
                'file_url' => Storage::url($path),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'alt_text' => $validated['alt_text'] ?? null,
            ]);

            // Dispatch background job for optimization if needed
            // \App\Jobs\ProcessMediaUpload::dispatch($media->id);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'data' => $media
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        if ($request->user()->cannot('delete', $media)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Media deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete media: ' . $e->getMessage()
            ], 500);
        }
    }
}
