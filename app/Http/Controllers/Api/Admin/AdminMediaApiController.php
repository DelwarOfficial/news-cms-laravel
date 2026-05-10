<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class AdminMediaApiController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('media.manage')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $media = Media::latest()->paginate($request->get('per_page', 20));
        return response()->json(['status' => 'success', 'data' => $media]);
    }

    public function store(Request $request)
    {
        if (!$request->user()->can('media.manage')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file|max:10240' // 10MB max
        ]);

        // Mock saving to DB
        $media = Media::create([
            'user_id' => $request->user()->id,
            'name' => $request->file('file')->getClientOriginalName(),
            'file_name' => $request->file('file')->hashName(),
            'file_path' => 'uploads/' . date('Y/m'),
            'file_type' => $request->file('file')->getMimeType(),
            'file_size' => $request->file('file')->getSize(),
        ]);

        // Dispatch background queue job for optimization/thumbnails
        \App\Jobs\ProcessMediaUpload::dispatch($media->id);

        return response()->json([
            'status' => 'success', 
            'message' => 'File uploaded successfully and queued for processing.',
            'data' => $media
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('media.manage')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $media = Media::findOrFail($id);
        $media->delete();

        return response()->json(['status' => 'success', 'message' => 'Media deleted successfully']);
    }
}
