<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index()
    {
        $folders = MediaFolder::with('children')->whereNull('parent_id')->get();
        $media = Media::with('folder')->latest()->paginate(24);

        return view('admin.media.index', compact('folders', 'media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'folder_id' => 'nullable|exists:media_folders,id',
        ]);

        $file = $request->file('file');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $fileName, 'public');

        Media::create([
            'folder_id' => $request->folder_id,
            'user_id' => Auth::id(),
            'name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'file_path' => $path,
            'file_url' => Storage::url($path),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'File uploaded successfully!');
    }

    public function destroy(Media $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();

        return back()->with('success', 'File deleted successfully!');
    }
}