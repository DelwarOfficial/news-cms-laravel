<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\FileUploadSecurity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TinyMceUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $file = $request->file('file') ?: collect($request->allFiles())->first();

        abort_unless($file, 422, 'No image was uploaded.');

        validator(['file' => $file], [
            'file' => ['required', ...FileUploadSecurity::imageRules()],
        ])->validate();

        $filename = FileUploadSecurity::storageName($file);

        Storage::disk('public')->putFileAs('news', $file, $filename);

        return response()->json([
            'location' => "/storage/news/{$filename}",
        ]);
    }
}
