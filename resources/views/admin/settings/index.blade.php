@extends('admin.layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900">Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your site configuration</p>
    </div>

    {{-- Success/Error --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl">
            <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl text-sm">{{ $errors->first() }}</div>
    @endif

    {{-- Tabs --}}
    @php
        $tabs = [
            'general' => ['icon' => 'fa-globe', 'label' => 'General'],
            'seo' => ['icon' => 'fa-search', 'label' => 'SEO'],
            'social' => ['icon' => 'fa-share-alt', 'label' => 'Social'],
            'media' => ['icon' => 'fa-image', 'label' => 'Media'],
            'appearance' => ['icon' => 'fa-palette', 'label' => 'Appearance'],
            'advanced' => ['icon' => 'fa-wrench', 'label' => 'Advanced'],
        ];
        $activeTab = request('tab', 'general');
        $val = fn ($key, $default = '') => old($key, $settings[$key] ?? $default);
    @endphp

    {{-- Tab Navigation --}}
    <div class="flex flex-wrap gap-1 bg-gray-100 rounded-xl p-1.5">
        @foreach($tabs as $key => $tab)
            <a href="{{ route('admin.settings.index', ['tab' => $key]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors
                      {{ $activeTab === $key ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                <i class="fas {{ $tab['icon'] }}"></i>
                <span class="hidden sm:inline">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>

    {{-- General Tab --}}
    @if($activeTab === 'general')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100">
        @csrf
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fas fa-globe text-blue-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">General Settings</h2>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Title</label>
                    <input type="text" name="general_site_title" value="{{ $val('general_site_title') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="My News Site">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Admin Email</label>
                    <input type="email" name="general_admin_email" value="{{ $val('general_admin_email') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="admin@example.com">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Site Description</label>
                <textarea name="general_site_description" rows="3" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="A brief description of your site...">{{ $val('general_site_description') }}</textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Timezone</label>
                    <input type="text" name="general_timezone" value="{{ $val('general_timezone', 'Asia/Dhaka') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="UTC">
                    <p class="text-xs text-gray-400 mt-1">e.g. UTC, Asia/Dhaka, America/New_York</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Default Language</label>
                    <input type="text" name="general_default_language" value="{{ $val('general_default_language', 'en') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="en">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Favicon URL</label>
                    <input type="url" name="general_favicon_url" value="{{ $val('general_favicon_url') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="/favicon.ico">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Logo URL</label>
                    <input type="url" name="general_logo_url" value="{{ $val('general_logo_url') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="/logo.png">
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i> Save General Settings
            </button>
        </div>
    </form>
    @endif

    {{-- SEO Tab --}}
    @if($activeTab === 'seo')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100">
        @csrf
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center"><i class="fas fa-search text-green-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">SEO Settings</h2>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Default Meta Title</label>
                    <input type="text" name="seo_default_meta_title" value="{{ $val('seo_default_meta_title') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Default meta title">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title Separator</label>
                    <input type="text" name="seo_title_separator" value="{{ $val('seo_title_separator', '|') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="|">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Default Meta Description</label>
                <textarea name="seo_default_meta_description" rows="3" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Default meta description for SEO...">{{ $val('seo_default_meta_description') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Default OG Image URL</label>
                <input type="url" name="seo_default_og_image" value="{{ $val('seo_default_og_image') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="https://example.com/og-image.jpg">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Robots.txt Content</label>
                <textarea name="seo_robots_txt" rows="5" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder="User-agent: *&#10;Allow: /">{{ $val('seo_robots_txt') }}</textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-5">
                <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="seo_enable_sitemap" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('seo_enable_sitemap', '1') === '1')>
                    <span class="text-sm font-semibold text-gray-700">Enable Sitemap</span>
                </label>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sitemap Entries Per Page</label>
                    <input type="number" name="seo_sitemap_entries_per_page" value="{{ $val('seo_sitemap_entries_per_page', '1000') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="1000">
                </div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i> Save SEO Settings
            </button>
        </div>
    </form>
    @endif

    {{-- Social Tab --}}
    @if($activeTab === 'social')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100">
        @csrf
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-sky-50 flex items-center justify-center"><i class="fas fa-share-alt text-sky-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">Social Media Settings</h2>
        </div>
        <div class="p-6 space-y-4">
            @php $socials = ['facebook' => 'Facebook', 'twitter' => 'Twitter / X', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn']; @endphp
            @foreach($socials as $key => $label)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $label }} URL</label>
                <input type="url" name="social_{{ $key }}_url" value="{{ $val("social_{$key}_url") }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="https://{{ $key }}.com/yoursite">
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i> Save Social Settings
            </button>
        </div>
    </form>
    @endif

    {{-- Media Tab --}}
    @if($activeTab === 'media')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100">
        @csrf
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center"><i class="fas fa-image text-purple-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">Media Settings</h2>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Upload Size (MB)</label>
                    <input type="number" name="media_max_upload_size" value="{{ $val('media_max_upload_size', '10') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="10">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Image Quality (1-100)</label>
                    <input type="number" name="media_image_quality" value="{{ $val('media_image_quality', '85') }}" min="1" max="100" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="85">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Allowed File Types</label>
                <input type="text" name="media_allowed_file_types" value="{{ $val('media_allowed_file_types', 'jpg,jpeg,png,gif,webp,svg,pdf,doc,docx') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="jpg,jpeg,png,gif,webp">
                <p class="text-xs text-gray-400 mt-1">Comma-separated list of file extensions</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Folder</label>
                <input type="text" name="media_upload_folder" value="{{ $val('media_upload_folder', 'media') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="/uploads">
            </div>
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="media_auto_webp" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('media_auto_webp', '0') === '1')>
                <div>
                    <span class="text-sm font-semibold text-gray-700">Auto WebP Conversion</span>
                    <p class="text-xs text-gray-400">Automatically convert uploaded images to WebP format</p>
                </div>
            </label>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i> Save Media Settings
            </button>
        </div>
    </form>
    @endif

    {{-- Appearance Tab --}}
    @if($activeTab === 'appearance')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100">
        @csrf
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-pink-50 flex items-center justify-center"><i class="fas fa-palette text-pink-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">Appearance Settings</h2>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="appearance_primary_color" value="{{ $val('appearance_primary_color', '#2563eb') }}" class="h-10 w-14 rounded-lg border border-gray-200 cursor-pointer">
                        <input type="text" name="appearance_primary_color_text" value="{{ $val('appearance_primary_color', '#2563eb') }}" class="flex-1 border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder="#2563eb">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Posts Per Page</label>
                    <input type="number" name="appearance_posts_per_page" value="{{ $val('appearance_posts_per_page', '12') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="12">
                </div>
            </div>
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="appearance_show_dark_mode_toggle" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('appearance_show_dark_mode_toggle', '1') === '1')>
                <span class="text-sm font-semibold text-gray-700">Show Dark Mode Toggle</span>
            </label>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Header HTML</label>
                <textarea name="appearance_custom_header_html" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder="&lt;!-- Custom HTML to inject in &lt;head&gt; --&gt;">{{ $val('appearance_custom_header_html') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Footer HTML</label>
                <textarea name="appearance_custom_footer_html" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder="&lt;!-- Custom HTML before &lt;/body&gt; --&gt;">{{ $val('appearance_custom_footer_html') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Body HTML</label>
                <textarea name="appearance_custom_body_html" rows="4" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-mono text-sm" placeholder="&lt;!-- Custom HTML after &lt;body&gt; --&gt;">{{ $val('appearance_custom_body_html') }}</textarea>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i> Save Appearance Settings
            </button>
        </div>
    </form>
    @endif

    {{-- Advanced Tab --}}
    @if($activeTab === 'advanced')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100">
        @csrf
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center"><i class="fas fa-wrench text-gray-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">Advanced Settings</h2>
        </div>
        <div class="p-6 space-y-5">
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="advanced_maintenance_mode" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('advanced_maintenance_mode', '0') === '1')>
                <div>
                    <span class="text-sm font-semibold text-gray-700">Maintenance Mode</span>
                    <p class="text-xs text-gray-400">When enabled, visitors will see a maintenance page</p>
                </div>
            </label>
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="advanced_cache_enabled" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('advanced_cache_enabled', '1') === '1')>
                <div>
                    <span class="text-sm font-semibold text-gray-700">Cache Enabled</span>
                    <p class="text-xs text-gray-400">Enable server-side caching for improved performance</p>
                </div>
            </label>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cache Duration (minutes)</label>
                    <input type="number" name="advanced_cache_duration" value="{{ $val('advanced_cache_duration', '300') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="300">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rate Limit (requests/min)</label>
                    <input type="number" name="advanced_rate_limit" value="{{ $val('advanced_rate_limit', '60') }}" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none" placeholder="60">
                </div>
            </div>
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="advanced_api_enabled" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('advanced_api_enabled', '1') === '1')>
                <div>
                    <span class="text-sm font-semibold text-gray-700">API Enabled</span>
                    <p class="text-xs text-gray-400">Allow external API access to your content</p>
                </div>
            </label>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                <i class="fas fa-save"></i> Save Advanced Settings
            </button>
        </div>
    </form>
    @endif

    {{-- Database & Backups Section (always visible) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center"><i class="fas fa-hdd text-indigo-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">Database & Backups</h2>
        </div>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6 space-y-5">
            @csrf
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Backup Disk</label>
                    <select name="backup_disk" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="local" @selected($val('backup_disk', 'local') === 'local')>Local Storage</option>
                        <option value="s3" @selected($val('backup_disk') === 's3')>Amazon S3</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Retention (days)</label>
                    <input type="number" name="backup_retention_days" value="{{ $val('backup_retention_days', '7') }}" min="1" max="365" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Auto Backup Frequency</label>
                    <select name="backup_frequency" class="w-full border border-gray-200 px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="daily" @selected($val('backup_frequency', 'daily') === 'daily')>Daily</option>
                        <option value="weekly" @selected($val('backup_frequency') === 'weekly')>Weekly</option>
                        <option value="monthly" @selected($val('backup_frequency') === 'monthly')>Monthly</option>
                    </select>
                </div>
                <div class="flex items-end pb-3">
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 w-full cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="backup_auto_enabled" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('backup_auto_enabled', '0') === '1')>
                        <span class="text-sm font-semibold text-gray-700">Enable Auto Backup</span>
                    </label>
                </div>
                <div class="flex items-end pb-3">
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 w-full cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="backup_include_media" value="1" class="rounded border-gray-300 text-blue-600" @checked($val('backup_include_media', '1') === '1')>
                        <span class="text-sm font-semibold text-gray-700">Include Media in Backups</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold inline-flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Backup Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
