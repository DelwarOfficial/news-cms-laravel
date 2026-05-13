<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Widget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WidgetController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(Widget::orderBy('area')->orderBy('order')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area' => 'required|string|max:100',
            'type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string|max:5000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $widget = Widget::create([
            ...$validated,
            'order' => $validated['order'] ?? Widget::where('area', $validated['area'])->max('order') + 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return $this->created($widget);
    }

    public function show($id): JsonResponse
    {
        return $this->success(Widget::findOrFail($id));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $widget = Widget::findOrFail($id);

        $validated = $request->validate([
            'area' => 'sometimes|string|max:100',
            'type' => 'sometimes|string|max:100',
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string|max:5000',
            'config' => 'nullable|array',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $widget->update($validated);

        return $this->success($widget);
    }

    public function destroy($id): JsonResponse
    {
        Widget::findOrFail($id)->delete();
        return $this->noContent();
    }

    public function toggle($id): JsonResponse
    {
        $widget = Widget::findOrFail($id);
        $widget->update(['is_active' => ! $widget->is_active]);

        return $this->success(['is_active' => $widget->is_active]);
    }
}
