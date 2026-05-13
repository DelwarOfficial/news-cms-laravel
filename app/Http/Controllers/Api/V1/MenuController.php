<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $menus = Menu::with(['items' => function ($q) {
            $q->with('children');
        }])->get();

        return $this->success($menus);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menus,slug',
            'location' => 'nullable|string|max:255',
        ]);

        $menu = Menu::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'location' => $validated['location'] ?? null,
        ]);

        return $this->created($menu);
    }

    public function show($id): JsonResponse
    {
        $menu = Menu::with(['items' => fn ($q) => $q->with('children')])->findOrFail($id);
        return $this->success($menu);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:menus,slug,' . $id,
            'location' => 'nullable|string|max:255',
        ]);

        $menu->update($validated);

        return $this->success($menu);
    }

    public function destroy($id): JsonResponse
    {
        Menu::findOrFail($id)->delete();
        return $this->noContent();
    }

    // --- Menu Items ---

    public function storeItem(Request $request, $menuId): JsonResponse
    {
        Menu::findOrFail($menuId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'target' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
        ]);

        $item = MenuItem::create([
            'menu_id' => $menuId,
            'title' => $validated['title'],
            'url' => $validated['url'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'target' => $validated['target'] ?? '_self',
            'order' => $validated['order'] ?? 0,
            'reference_type' => $validated['reference_type'] ?? null,
            'reference_id' => $validated['reference_id'] ?? null,
        ]);

        return $this->created($item);
    }

    public function updateItem(Request $request, $menuId, $itemId): JsonResponse
    {
        $item = MenuItem::where('menu_id', $menuId)->findOrFail($itemId);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'url' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:menu_items,id',
            'target' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ]);

        $item->update($validated);

        return $this->success($item);
    }

    public function destroyItem($menuId, $itemId): JsonResponse
    {
        MenuItem::where('menu_id', $menuId)->findOrFail($itemId)->delete();
        return $this->noContent();
    }
}
