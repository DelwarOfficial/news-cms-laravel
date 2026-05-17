<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = Cache::remember('cms_settings_v1', 86400, fn () =>
            Setting::pluck('value', 'key')->toArray()
        );

        return $all[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('cms_settings_v1');
    }
}
