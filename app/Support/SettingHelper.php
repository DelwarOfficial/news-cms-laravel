<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingHelper
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('cms_settings', 3600, function () {
            return Setting::pluck('value', 'key')->all();
        })[$key] ?? $default;
    }

    public static function flush(): void
    {
        Cache::forget('cms_settings');
    }
}
