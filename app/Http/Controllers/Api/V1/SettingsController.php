<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $settings = Cache::remember('cms_settings', 3600, function () {
            return Setting::pluck('value', 'key')->all();
        });

        return $this->success($settings);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value],
            );
        }

        Cache::forget('cms_settings');

        return $this->success(['message' => 'Settings updated.']);
    }
}
