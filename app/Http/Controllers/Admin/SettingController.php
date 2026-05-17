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

        $rules = [
            // General
            'general_site_title' => 'nullable|max:255',
            'general_site_description' => 'nullable|max:1000',
            'general_admin_email' => 'nullable|email',
            'general_timezone' => 'nullable|max:100',
            'general_default_language' => 'nullable|max:10',
            'general_favicon_url' => 'nullable|url|max:500',
            'general_logo_url' => 'nullable|url|max:500',

            // SEO
            'seo_default_meta_title' => 'nullable|max:255',
            'seo_default_meta_description' => 'nullable|max:500',
            'seo_default_og_image' => 'nullable|url|max:500',
            'seo_title_separator' => 'nullable|max:10',
            'seo_robots_txt' => 'nullable|max:5000',
            'seo_enable_sitemap' => 'boolean',
            'seo_sitemap_entries_per_page' => 'nullable|integer|min:1|max:50000',

            // Social
            'social_facebook_url' => 'nullable|url|max:500',
            'social_twitter_url' => 'nullable|url|max:500',
            'social_youtube_url' => 'nullable|url|max:500',
            'social_instagram_url' => 'nullable|url|max:500',
            'social_linkedin_url' => 'nullable|url|max:500',

            // Media
            'media_max_upload_size' => 'nullable|integer|min:1|max:500',
            'media_allowed_file_types' => 'nullable|max:500',
            'media_upload_folder' => 'nullable|max:255',
            'media_auto_webp' => 'boolean',
            'media_image_quality' => 'nullable|integer|min:1|max:100',

            // Appearance
            'appearance_primary_color' => 'nullable|max:20',
            'appearance_posts_per_page' => 'nullable|integer|min:1|max:100',
            'appearance_show_dark_mode_toggle' => 'boolean',
            'appearance_custom_header_html' => 'nullable|max:10000',
            'appearance_custom_footer_html' => 'nullable|max:10000',
            'appearance_custom_body_html' => 'nullable|max:10000',

            // Advanced
            'advanced_maintenance_mode' => 'boolean',
            'advanced_cache_enabled' => 'boolean',
            'advanced_cache_duration' => 'nullable|integer|min:0|max:525600',
            'advanced_api_enabled' => 'boolean',
            'advanced_rate_limit' => 'nullable|integer|min:1|max:100000',

            // Backups
            'backup_disk' => 'nullable|in:local,s3',
            'backup_retention_days' => 'nullable|integer|min:1|max:365',
            'backup_auto_enabled' => 'boolean',
            'backup_frequency' => 'nullable|in:daily,weekly,monthly',
            'backup_include_media' => 'boolean',

            // Legacy fields (backward compatible)
            'site_name' => 'nullable|max:255',
            'site_description' => 'nullable|max:500',
            'contact_email' => 'nullable|email',
            'site_url' => 'nullable|url',
            'site_logo' => 'nullable|url',
            'site_favicon' => 'nullable|url',
            'posts_per_page' => 'nullable|integer|min:1|max:100',
            'default_language' => 'nullable|max:10',
            'timezone' => 'nullable|max:100',
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
        ];

        $validated = $request->validate($rules);

        // Normalize boolean checkboxes
        foreach ([
            'seo_enable_sitemap', 'media_auto_webp', 'appearance_show_dark_mode_toggle',
            'advanced_maintenance_mode', 'advanced_cache_enabled', 'advanced_api_enabled',
            'backup_auto_enabled', 'backup_include_media',
            'enable_comments', 'require_comment_approval', 'enable_registration',
        ] as $key) {
            if ($request->has($key)) {
                $validated[$key] = $request->boolean($key) ? '1' : '0';
            }
        }

        // Map legacy field names to new names
        $legacyMap = [
            'site_name' => 'general_site_title',
            'site_description' => 'general_site_description',
            'contact_email' => 'general_admin_email',
            'timezone' => 'general_timezone',
            'default_language' => 'general_default_language',
            'site_logo' => 'general_logo_url',
            'site_favicon' => 'general_favicon_url',
            'meta_title' => 'seo_default_meta_title',
            'meta_description' => 'seo_default_meta_description',
            'posts_per_page' => 'appearance_posts_per_page',
        ];

        foreach ($legacyMap as $old => $new) {
            if (isset($validated[$old])) {
                $validated[$new] = $validated[$old];
                unset($validated[$old]);
            }
        }

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        Cache::forget(['settings', 'cms_settings', 'cms_settings_v1']);

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
            return back()->with('error', 'Invalid settings file.');
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

        Cache::forget(['settings', 'cms_settings', 'cms_settings_v1']);

        return back()->with('success', "Imported {$count} settings successfully.");
    }
}
