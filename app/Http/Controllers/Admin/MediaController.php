<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Support\AdminTableSort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    protected $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    public function index(Request $request)
    {
        $this->authorize('viewAny', Media::class);

        $allowedSorts = ['name', 'file_type', 'file_size', 'created_at', 'updated_at'];
        [$sortBy, $sortDirection] = AdminTableSort::resolve($request, $allowedSorts);
        $folders = MediaFolder::with('children')->whereNull('parent_id')->get();
        $media = AdminTableSort::apply(
            Media::with('folder', 'user'),
            $allowedSorts,
            $sortBy,
            $sortDirection
        )->paginate(24)->withQueryString();

        return view('admin.media.index', compact('folders', 'media', 'sortBy', 'sortDirection'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Media::class);

        $request->validate([
            'file' => 'required|file|max:10240|mimetypes:' . implode(',', $this->allowedMimes),
            'folder_id' => 'nullable|exists:media_folders,id',
        ], [
            'file.mimetypes' => 'File type not allowed. Allowed types: JPG, PNG, GIF, WebP, PDF, DOC, DOCX',
        ]);

        try {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('media', $fileName, 'public');

            if (!$path) {
                throw new \Exception('Failed to store file.');
            }

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
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function destroy(Media $media)
    {
        $this->authorize('delete', $media);

        try {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
            return back()->with('success', 'File deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete file: ' . $e->getMessage());
        }
    }
}
