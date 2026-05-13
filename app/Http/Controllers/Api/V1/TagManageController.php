<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagManageController extends BaseApiController
{
    public function index()
    {
        return $this->success(Tag::withCount('posts')->orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'slug' => 'nullable|string|max:255|unique:tags,slug',
        ]);

        $tag = Tag::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
        ]);

        return $this->created($tag);
    }

    public function show($id)
    {
        return $this->success(Tag::withCount('posts')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:tags,name,' . $id,
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $id,
        ]);

        if (isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $tag->update($validated);

        return $this->success($tag);
    }

    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);

        if ($tag->posts()->exists()) {
            return $this->error('Conflict', 'Tag is attached to posts. Remove associations first.', 409);
        }

        $tag->delete();

        return $this->noContent();
    }
}
