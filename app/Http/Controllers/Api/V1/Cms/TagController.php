<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(Tag::orderBy('name')->get(['id', 'name', 'slug']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
        ]);

        $tag = Tag::firstOrCreate(
            ['name' => $validated['name']],
            ['slug' => $validated['slug'] ?? Str::slug($validated['name'])],
        );

        return $this->created([
            'id' => $tag->id,
            'name' => $tag->name,
            'slug' => $tag->slug,
        ]);
    }
}
