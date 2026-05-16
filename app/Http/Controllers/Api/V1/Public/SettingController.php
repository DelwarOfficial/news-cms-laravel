<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Http\Resources\Api\V1\SettingResource;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingController extends BaseApiController
{
    public function index()
    {
        $settings = Cache::remember('v1:settings:public', 3600, function () {
            return Setting::where('group', 'general')
                ->orWhere('group', 'site')
                ->orWhere('group', 'seo')
                ->orWhere('group', 'social')
                ->orWhere('group', 'appearance')
                ->get();
        });

        return $this->success(SettingResource::collection($settings));
    }

    public function show(string $key)
    {
        $setting = Cache::remember("v1:settings:public:{$key}", 3600, function () use ($key) {
            return Setting::where('key', $key)->first();
        });

        if (! $setting) {
            return $this->error('Not Found', 'Setting not found.', 404);
        }

        return $this->success(new SettingResource($setting));
    }

    public function group(string $group)
    {
        $allowed = ['general', 'site', 'seo', 'social', 'appearance'];

        if (! in_array($group, $allowed, true)) {
            return $this->error('Forbidden', 'This settings group is not publicly accessible.', 403);
        }

        $settings = Cache::remember("v1:settings:group:{$group}", 3600, function () use ($group) {
            return Setting::where('group', $group)->get();
        });

        return $this->success(SettingResource::collection($settings));
    }
}
