<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Support\AdminTableSort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $allowedSorts = ['name', 'slug', 'created_at', 'updated_at'];
        [$sortBy, $sortDirection] = AdminTableSort::resolve($request, $allowedSorts);
        
        $query = Tag::query()
            ->when($search, function ($q) use ($search) {
                return $q->where('name', 'like', '%' . $search . '%');
            })
            ->withCount('posts');

        $tags = AdminTableSort::apply($query, $allowedSorts, $sortBy, $sortDirection)
            ->paginate(20)
            ->withQueryString();

        return view('admin.tags.index', compact('tags', 'search', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $this->authorize('create', Tag::class);
        
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Tag::class);
        
        $validated = $request->validate([
            'name' => 'required|max:100|unique:tags,name',
            'description' => 'nullable|max:500',
            'meta_description' => 'nullable|max:160',
        ]);

        try {
            Tag::create($validated);
            return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully!');
        } catch (\Exception $e) {
            Log::error('Tag creation failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create tag: ' . $e->getMessage());
        }
    }

    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);
        
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);
        
        $validated = $request->validate([
            'name' => 'required|max:100|unique:tags,name,' . $tag->id,
            'description' => 'nullable|max:500',
            'meta_description' => 'nullable|max:160',
        ]);

        try {
            $tag->update($validated);
            return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully!');
        } catch (\Exception $e) {
            Log::error('Tag update failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update tag: ' . $e->getMessage());
        }
    }

    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);
        
        // Prevent deletion if tag has posts
        if ($tag->posts()->count() > 0) {
            return back()->with('error', 'Cannot delete tag with existing posts. Please remove the tag from posts first.');
        }
        
        try {
            $tag->delete();
            return back()->with('success', 'Tag deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Tag deletion failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete tag: ' . $e->getMessage());
        }
    }
}
