<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WidgetController extends Controller
{
    protected $allowedTypes = ['text', 'featured_posts', 'recent_posts', 'categories', 'tags', 'newsletter', 'custom_html'];
    protected $allowedAreas = ['sidebar', 'footer_col_1', 'footer_col_2', 'footer_col_3', 'header'];

    public function index()
    {
        $widgets = Widget::orderBy('area')->orderBy('order')->get();
        $areas = $this->allowedAreas;
        $types = $this->allowedTypes;
        
        return view('admin.widgets.index', compact('widgets', 'areas', 'types'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Widget::class);
        
        $validated = $request->validate([
            'area' => 'required|in:' . implode(',', $this->allowedAreas),
            'type' => 'required|in:' . implode(',', $this->allowedTypes),
            'title' => 'required|max:255',
            'content' => 'nullable|max:5000',
            'order' => 'nullable|integer|min:0',
            'enabled' => 'boolean',
        ]);

        try {
            Widget::create([
                ...$validated,
                'order' => $validated['order'] ?? Widget::where('area', $validated['area'])->max('order') + 1,
            ]);

            return back()->with('success', 'Widget created successfully!');
        } catch (\Exception $e) {
            Log::error('Widget creation failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create widget: ' . $e->getMessage());
        }
    }

    public function edit(Widget $widget)
    {
        $this->authorize('update', $widget);
        
        $areas = $this->allowedAreas;
        $types = $this->allowedTypes;
        
        return view('admin.widgets.edit', compact('widget', 'areas', 'types'));
    }

    public function update(Request $request, Widget $widget)
    {
        $this->authorize('update', $widget);
        
        $validated = $request->validate([
            'area' => 'required|in:' . implode(',', $this->allowedAreas),
            'type' => 'required|in:' . implode(',', $this->allowedTypes),
            'title' => 'required|max:255',
            'content' => 'nullable|max:5000',
            'order' => 'nullable|integer|min:0',
            'enabled' => 'boolean',
        ]);

        try {
            $widget->update($validated);
            return back()->with('success', 'Widget updated successfully!');
        } catch (\Exception $e) {
            Log::error('Widget update failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update widget: ' . $e->getMessage());
        }
    }

    public function destroy(Widget $widget)
    {
        $this->authorize('delete', $widget);
        
        try {
            $widget->delete();
            return back()->with('success', 'Widget deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Widget deletion failed:', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete widget: ' . $e->getMessage());
        }
    }

    public function toggle(Widget $widget)
    {
        $this->authorize('update', $widget);

        $widget->update(['is_active' => ! $widget->is_active]);

        return back()->with('success', 'Widget status toggled successfully.');
    }
}