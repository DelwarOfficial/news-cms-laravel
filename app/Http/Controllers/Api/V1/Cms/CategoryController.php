<?php

namespace App\Http\Controllers\Api\V1\Cms;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Category;
use App\Support\FrontendCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(Category::with(Category::apiRelations())->orderBy('name')->get(['id', 'name', 'slug', 'parent_id']));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_slug' => 'nullable|string|max:255|exists:categories,slug',
        ]);

        $parentId = null;
        if (! empty($validated['parent_slug'])) {
            $parentId = Category::where('slug', $validated['parent_slug'])->value('id');
        }

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        $category = Category::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $validated['name'],
                'parent_id' => $parentId,
                'status' => 'active',
            ],
        );

        if (! $category->wasRecentlyCreated && ! empty($validated['name'])) {
            $category->update(['name' => $validated['name'], 'parent_id' => $parentId ?? $category->parent_id]);
        }

        FrontendCache::flushCategories();

        return $this->created([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
        ]);
    }
}
