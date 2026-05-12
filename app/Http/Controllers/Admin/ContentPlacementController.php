<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPlacement;
use App\Models\Post;
use App\Support\FrontendCache;
use Illuminate\Http\Request;

class ContentPlacementController extends Controller
{
    public function index(Request $request)
    {
        $placements = ContentPlacement::query()
            ->with(['post:id,title,slug,status,published_at'])
            ->when($request->filled('placement_key'), fn ($query) => $query->where('placement_key', $request->string('placement_key')))
            ->orderBy('placement_key')
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->latest('updated_at')
            ->paginate(30)
            ->withQueryString();

        $posts = Post::query()
            ->published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'title', 'slug', 'published_at']);

        $placementKeys = ContentPlacement::query()
            ->select('placement_key')
            ->distinct()
            ->orderBy('placement_key')
            ->pluck('placement_key');

        $defaultPlacementKeys = collect([
            'home.breaking',
            'home.featured',
            'home.center_grid',
            'home.left_column',
            'home.right_column',
            'home.sticky',
            'home.trending',
            'home.editors_pick',
        ]);

        $placementOptions = $defaultPlacementKeys
            ->merge($placementKeys)
            ->unique()
            ->values();

        return view('admin.placements.index', compact('placements', 'posts', 'placementOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
            'placement_key' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ContentPlacement::updateOrCreate(
            [
                'post_id' => $validated['post_id'],
                'placement_key' => $validated['placement_key'],
            ],
            [
                'sort_order' => $validated['sort_order'] ?? null,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ],
        );

        $this->flushFrontendContentCache();

        return redirect()
            ->route('admin.placements.index', ['placement_key' => $validated['placement_key']])
            ->with('success', 'Content placement saved successfully.');
    }

    public function destroy(ContentPlacement $placement)
    {
        $placementKey = $placement->placement_key;

        $placement->delete();

        $this->flushFrontendContentCache();

        return redirect()
            ->route('admin.placements.index', ['placement_key' => $placementKey])
            ->with('success', 'Content placement removed successfully.');
    }

    private function flushFrontendContentCache(): void
    {
        FrontendCache::flushContent();
    }
}
