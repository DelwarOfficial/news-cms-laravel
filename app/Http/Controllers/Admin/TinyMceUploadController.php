<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TinyMceUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $file = $request->file('file') ?: collect($request->allFiles())->first();

        abort_unless($file, 422, 'No image was uploaded.');

        validator(['file' => $file], [
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ])->validate();

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::uuid()->toString().'.'.$extension;

        Storage::disk('public')->putFileAs('news', $file, $filename);

        return response()->json([
            'location' => "/storage/news/{$filename}",
        ]);
    }
}
