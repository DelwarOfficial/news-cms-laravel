<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Setting::class);
        
        $settings = Setting::all()->keyBy('key')->map(function ($setting) {
            return $setting->value;
        });

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorize('update', Setting::class);
        
        $validated = $request->validate([
            'site_name' => 'required|max:255',
            'site_description' => 'nullable|max:500',
            'contact_email' => 'required|email',
            'site_url' => 'nullable|url',
            'site_logo' => 'nullable|url',
            'site_favicon' => 'nullable|url',
            'posts_per_page' => 'required|integer|min:1|max:100',
            'default_language' => 'required|max:10',
            'timezone' => 'required|max:100',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'enable_comments' => 'boolean',
            'require_comment_approval' => 'boolean',
            'enable_registration' => 'boolean',
            'smtp_host' => 'nullable|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|max:255',
            'smtp_password' => 'nullable|max:255',
            'google_analytics_id' => 'nullable|max:255',
            'recaptcha_site_key' => 'nullable|max:255',
            'recaptcha_secret_key' => 'nullable|max:255',
            'backup_disk' => 'nullable|in:local,s3',
            'backup_retention_days' => 'nullable|integer|min:1|max:365',
            'backup_auto_enabled' => 'boolean',
            'backup_frequency' => 'nullable|in:daily,weekly,monthly',
            'backup_include_media' => 'boolean',
        ]);

        foreach (['enable_comments', 'require_comment_approval', 'enable_registration'] as $key) {
            $validated[$key] = $request->boolean($key) ? '1' : '0';
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // Clear settings cache
        Cache::forget('settings');
        Cache::forget('cms_settings');

        return back()->with('success', 'Settings updated successfully!');
    }

    public function export()
    {
        $this->authorize('viewAny', Setting::class);

        $settings = Setting::all()->keyBy('key')->map(fn ($s) => $s->value);

        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="news-core-settings-' . now()->format('Y-m-d') . '.json"')
            ->header('Content-Type', 'application/json');
    }

    public function import(Request $request)
    {
        $this->authorize('update', Setting::class);

        $request->validate([
            'settings_file' => 'required|file|mimes:json|max:512',
        ]);

        $json = json_decode($request->file('settings_file')->get(), true);

        if (! is_array($json) || $json === []) {
            return back()->with('error', 'Invalid settings file. Expected a JSON object of key-value pairs.');
        }

        $count = 0;
        foreach ($json as $key => $value) {
            if (is_string($key) && (is_string($value) || is_numeric($value) || is_bool($value))) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => (string) $value],
                );
                $count++;
            }
        }

        Cache::forget('settings');
        Cache::forget('cms_settings');

        return back()->with('success', "Imported {$count} settings successfully.");
    }
}
