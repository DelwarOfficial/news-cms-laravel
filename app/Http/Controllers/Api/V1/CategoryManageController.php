<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use App\Support\FrontendCache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryManageController extends BaseApiController
{
    public function index()
    {
        $categories = Category::with(Category::apiRelations())->withCount('posts')
            ->orderBy('order')->orderBy('name')->get();

        return $this->success(CategoryResource::collection($categories));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'parent_id' => $validated['parent_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'order' => $validated['order'] ?? 0,
            'status' => $validated['status'] ?? 'active',
        ]);

        FrontendCache::flushCategories();

        return $this->created(new CategoryResource($category));
    }

    public function show($id)
    {
        $category = Category::with(Category::apiRelations())->withCount('posts')->findOrFail($id);
        return $this->success(new CategoryResource($category));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'status' => 'sometimes|in:active,inactive',
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $category->update($validated);

        FrontendCache::flushCategories();

        return $this->success(new CategoryResource($category->load(Category::apiRelations())));
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->posts()->exists()) {
            return $this->error('Conflict', 'Category has posts. Reassign or delete them first.', 409);
        }

        $category->delete();

        FrontendCache::flushCategories();

        return $this->noContent();
    }
}
