<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPlacement;
use App\Models\Post;
use App\Support\FrontendCache;
use Illuminate\Http\Request;

class ContentPlacementController extends Controller
{
    public const SLOTS = [
        'home.breaking'     => 'Breaking News Ticker',
        'home.featured'     => 'Hero Featured Story',
        'home.sticky'       => 'Sticky / Pinned Post',
        'home.trending'     => 'Trending Section',
        'home.editors_pick' => "Editor's Pick",
        'home.center_grid'  => 'Center Grid',
        'home.left_column'  => 'Left Column',
        'home.right_column' => 'Right Column',
    ];

    public function index(Request $request)
    {
        $placements = ContentPlacement::with(['post:id,title,slug,status,published_at,featured_image'])
            ->when($request->filled('slot'), fn ($q) => $q->where('placement_key', $request->slot))
            ->orderBy('placement_key')
            ->orderByRaw('CASE WHEN sort_order IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->latest('updated_at')
            ->paginate(30)
            ->withQueryString();

        $posts = Post::published()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'title', 'slug', 'published_at', 'featured_image']);

        $activeSlots = ContentPlacement::select('placement_key')
            ->distinct()->orderBy('placement_key')->pluck('placement_key');

        $slots = collect(self::SLOTS);

        $stats = [
            'total' => ContentPlacement::count(),
            'active' => ContentPlacement::where('is_active', true)->count(),
            'slots_used' => ContentPlacement::select('placement_key')->distinct()->count(),
            'slots_total' => count(self::SLOTS),
        ];

        return view('admin.placements.index', compact('placements', 'posts', 'slots', 'activeSlots', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => ['required', 'exists:posts,id'],
            'placement_key' => ['required', 'string', 'in:' . implode(',', array_keys(self::SLOTS))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ContentPlacement::updateOrCreate(
            ['post_id' => $validated['post_id'], 'placement_key' => $validated['placement_key']],
            [
                'sort_order' => $validated['sort_order'] ?? null,
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ],
        );

        FrontendCache::flushContent();

        return redirect()
            ->route('admin.placements.index', ['slot' => $validated['placement_key']])
            ->with('success', 'Content placement saved successfully.');
    }

    public function update(Request $request, ContentPlacement $placement)
    {
        $validated = $request->validate([
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'post_id' => ['nullable', 'exists:posts,id'],
        ]);

        if (isset($validated['post_id'])) {
            $placement->post_id = $validated['post_id'];
        }
        if ($request->has('sort_order')) {
            $placement->sort_order = $validated['sort_order'];
        }
        if ($request->has('starts_at')) {
            $placement->starts_at = $validated['starts_at'];
        }
        if ($request->has('ends_at')) {
            $placement->ends_at = $validated['ends_at'];
        }
        if ($request->has('is_active')) {
            $placement->is_active = $request->boolean('is_active');
        }

        $placement->save();
        FrontendCache::flushContent();

        return back()->with('success', 'Placement updated.');
    }

    public function edit(ContentPlacement $placement)
    {
        $posts = Post::published()
            ->orderByDesc('published_at')
            ->limit(200)
            ->get(['id', 'title', 'slug', 'published_at', 'featured_image']);

        return view('admin.placements.edit', compact('placement', 'posts'));
    }

    public function destroy(ContentPlacement $placement)
    {
        $slot = $placement->placement_key;
        $placement->delete();
        FrontendCache::flushContent();

        return redirect()
            ->route('admin.placements.index', ['slot' => $slot])
            ->with('success', 'Placement removed.');
    }
}
