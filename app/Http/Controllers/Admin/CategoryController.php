<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\AdminTableSort;
use App\Support\FrontendCache;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        
        $allowedSorts = ['name', 'slug', 'order', 'created_at'];
        [$sortBy, $sortDirection] = AdminTableSort::resolve($request, $allowedSorts, 'order', 'asc');

        $categories = AdminTableSort::apply(
            Category::with('parent')->withCount('posts'),
            $allowedSorts,
            $sortBy,
            $sortDirection
        )->get();

        return view('admin.categories.index', compact('categories', 'sortBy', 'sortDirection'));
    }

    public function create()
    {
        $this->authorize('create', Category::class);
        
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        
        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories',
            'slug' => 'nullable|max:255|unique:categories,slug|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'meta_title' => 'nullable|max:70',
            'meta_description' => 'nullable|max:170',
        ]);

        // Prevent circular references
        if ($validated['parent_id'] ?? false) {
            $parent = Category::find($validated['parent_id']);
            if ($parent && $this->hasCircularReference($parent)) {
                return back()->withInput()->with('error', 'Cannot set parent category as a child of itself.');
            }
        }

        Category::create($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        
        $parents = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        
        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|max:1000',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:160',
        ]);

        // Prevent circular references
        if ($validated['parent_id'] ?? false) {
            $parent = Category::find($validated['parent_id']);
            if ($this->hasCircularReference($parent, $category->id)) {
                return back()->withInput()->with('error', 'Cannot create circular category hierarchy.');
            }
        }

        $category->update($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        
        // Prevent deletion if category has posts
        if ($category->posts()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing posts. Please move or delete posts first.');
        }
        
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }

    public function reorder(Request $request)
    {
        $this->authorize('update', Category::class);
        
        $items = $request->input('items', []);
        
        if (!is_array($items) || empty($items)) {
            return response()->json(['success' => false, 'message' => 'Invalid items'], 400);
        }
        
        foreach ($items as $item) {
            if (!isset($item['id']) || !isset($item['order'])) {
                return response()->json(['success' => false, 'message' => 'Invalid item structure'], 400);
            }
            
            Category::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        FrontendCache::flushCategories();
        
        return response()->json(['success' => true, 'message' => 'Categories reordered successfully']);
    }

    /**
     * Check if setting parent would create a circular reference
     */
    protected function hasCircularReference(Category $parent, ?int $excludeId = null): bool
    {
        $current = $parent->parent;
        
        while ($current) {
            if ($current->id === ($excludeId ?? null)) {
                return true;
            }
            $current = $current->parent;
        }
        
        return false;
    }
}
