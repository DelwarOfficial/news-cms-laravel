<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Advertisement;
use App\Support\FileUploadSecurity;
use App\Support\FrontendCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(Advertisement::latest()->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'type' => 'required|in:image,code',
            'image' => ['required_if:type,image', 'nullable', ...FileUploadSecurity::imageRules()],
            'code' => 'required_if:type,code|nullable|string|max:5000',
            'url' => 'nullable|url|max:500',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $ad = new Advertisement();
        $ad->title = $validated['title'];
        $ad->position = $validated['position'];
        $ad->type = $validated['type'];
        $ad->url = $validated['url'] ?? null;
        $ad->start_date = $validated['start_date'] ?? null;
        $ad->end_date = $validated['end_date'] ?? null;
        $ad->is_active = $request->boolean('is_active', true);

        if ($validated['type'] === 'image' && $request->hasFile('image')) {
            $ad->image = $request->file('image')->storeAs('advertisements', FileUploadSecurity::storageName($request->file('image')), 'public');
        } elseif ($validated['type'] === 'code') {
            $ad->code = $validated['code'] ?? null;
        }

        $ad->save();
        FrontendCache::flushAds();

        return $this->created($ad);
    }

    public function show($id): JsonResponse
    {
        return $this->success(Advertisement::findOrFail($id));
    }

    public function update(Request $request, $id): JsonResponse
    {
        $ad = Advertisement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:100',
            'type' => 'sometimes|in:image,code',
            'image' => ['nullable', ...FileUploadSecurity::imageRules()],
            'code' => 'nullable|string|max:5000',
            'url' => 'nullable|url|max:500',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($validated['title'])) { $ad->title = $validated['title']; }
        if (isset($validated['position'])) { $ad->position = $validated['position']; }
        if (isset($validated['type'])) { $ad->type = $validated['type']; }
        if (isset($validated['url'])) { $ad->url = $validated['url']; }
        if (isset($validated['start_date'])) { $ad->start_date = $validated['start_date']; }
        if (isset($validated['end_date'])) { $ad->end_date = $validated['end_date']; }
        if ($request->has('is_active')) { $ad->is_active = $request->boolean('is_active'); }

        if ($request->hasFile('image')) {
            if ($ad->image) { Storage::disk('public')->delete($ad->image); }
            $ad->image = $request->file('image')->storeAs('advertisements', FileUploadSecurity::storageName($request->file('image')), 'public');
            $ad->code = null;
        } elseif ($request->filled('code')) {
            if ($ad->image) { Storage::disk('public')->delete($ad->image); $ad->image = null; }
            $ad->code = $validated['code'];
        }

        $ad->save();
        FrontendCache::flushAds();

        return $this->success($ad);
    }

    public function destroy($id): JsonResponse
    {
        $ad = Advertisement::findOrFail($id);
        if ($ad->image) { Storage::disk('public')->delete($ad->image); }
        $ad->delete();
        FrontendCache::flushAds();

        return $this->noContent();
    }
}
