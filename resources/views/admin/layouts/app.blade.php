<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — NewsCore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <x-rich-text::styles theme="richtextlaravel" />
    <script type="module">
        import Trix from "{{ asset('vendor/rich-text-laravel/trix.esm.js') }}";

        Trix.config.blockAttributes.alignRight = {
            tagName: 'div',
            className: 'text-right',
            nestable: false,
        };

        document.addEventListener('trix-initialize', (event) => {
            const toolbar = event.target.toolbarElement;
            const blockTools = toolbar?.querySelector('.trix-button-group--block-tools');

            if (!blockTools || blockTools.querySelector('[data-trix-attribute="alignRight"]')) {
                return;
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'trix-button';
            button.setAttribute('data-trix-attribute', 'alignRight');
            button.setAttribute('title', 'Align right');
            button.setAttribute('tabindex', '-1');
            button.textContent = 'R';
            blockTools.appendChild(button);
        });
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#eff6ff', 100:'#dbeafe', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8', 800:'#1e40af', 900:'#1e3a8a' }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        body { font-family: 'Inter', sans-serif; }
        .font-bengali, [lang="bn"], .newscore-richtext.font-bengali trix-editor { font-family: 'Noto Sans Bengali', 'Inter', sans-serif; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200; }
        .sidebar-link:hover { @apply bg-white/10; }
        .sidebar-link.active { @apply bg-white text-blue-700 shadow-md; }
        .newscore-richtext trix-toolbar { @apply border border-gray-200 rounded-t-xl bg-gray-50 px-3 pt-2; }
        .newscore-richtext trix-toolbar { overflow-x: auto; max-width: 100%; }
        .newscore-richtext trix-toolbar .trix-button-row { flex-wrap: wrap; gap: 0.25rem; }
        .newscore-richtext trix-toolbar .trix-button-group { margin-bottom: 0.25rem; }
        .newscore-richtext trix-toolbar .trix-button--icon-attach,
        .newscore-richtext trix-toolbar .trix-button-group--file-tools { display: none; }
        .newscore-richtext trix-toolbar .trix-dialogs { max-width: 100%; }
        .newscore-richtext trix-toolbar .trix-dialog { position: static; max-width: 100%; }
        .newscore-richtext trix-toolbar .trix-dialog__link-fields { flex-wrap: wrap; }
        .newscore-richtext trix-toolbar .trix-input--dialog { min-width: 0; width: 100%; }
        .newscore-richtext trix-editor { @apply min-h-[220px] border border-gray-200 border-t-0 rounded-b-xl px-4 py-3 text-sm leading-7 outline-none bg-white; max-width: 100%; overflow-wrap: anywhere; }
        .newscore-richtext trix-editor[aria-label="summary_en"],
        .newscore-richtext trix-editor[aria-label="summary_bn"] { min-height: 120px; }
        .newscore-richtext trix-editor:focus { @apply ring-2 ring-blue-500 border-transparent; }
        .cms-editor-toolbar { @apply flex flex-wrap items-center gap-1 border border-gray-200 rounded-t-xl bg-gray-50 px-3 py-2; }
        .cms-editor-button { @apply inline-flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-xs font-semibold text-gray-600 hover:bg-white hover:text-blue-700; }
        .cms-editor-button.active { @apply bg-white text-blue-700 shadow-sm; }
        .cms-body-editor { @apply min-h-[320px] w-full overflow-y-auto rounded-b-xl border border-t-0 border-gray-200 bg-white px-4 py-3 text-sm leading-7 outline-none; overflow-wrap: anywhere; }
        .cms-body-editor:focus { @apply ring-2 ring-blue-500 border-transparent; }
        .cms-body-editor:empty:before { content: attr(data-placeholder); color: #9ca3af; }
        .trix-content .text-right { text-align: right; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside class="w-64 bg-gradient-to-b from-blue-700 to-blue-900 text-white flex flex-col flex-shrink-0 overflow-y-auto">
        <div class="px-6 py-5 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-bolt text-blue-700 text-sm"></i>
                </div>
                <span class="font-bold text-lg tracking-tight">NewsCore</span>
            </a>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1">
            @can('dashboard.view')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-home w-4"></i> Dashboard
                </a>
            @endcan
            @can('posts.create')
                <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.posts*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-newspaper w-4"></i> Posts
                </a>
            @endcan
            @can('categories.manage')
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-folder w-4"></i> Categories
                </a>
            @endcan
            @can('posts.create')
                <a href="{{ route('admin.placements.index') }}" class="sidebar-link {{ request()->routeIs('admin.placements*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-layer-group w-4"></i> Placements
                </a>
            @endcan
            @can('categories.manage')
                <a href="{{ route('admin.locations.index') }}" class="sidebar-link {{ request()->routeIs('admin.locations*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-map-location-dot w-4"></i> Locations
                </a>
            @endcan
            @can('tags.manage')
                <a href="{{ route('admin.tags.index') }}" class="sidebar-link {{ request()->routeIs('admin.tags*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-tags w-4"></i> Tags
                </a>
            @endcan
            @can('media.manage')
                <a href="{{ route('admin.media.index') }}" class="sidebar-link {{ request()->routeIs('admin.media*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-images w-4"></i> Media
                </a>
            @endcan
            @can('comments.manage')
                <a href="{{ route('admin.comments.index') }}" class="sidebar-link {{ request()->routeIs('admin.comments*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-comments w-4"></i> Comments
                </a>
            @endcan
            @can('users.create')
                <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-id-badge w-4"></i> Members
                </a>
            @endcan
            <div class="pt-3 pb-1 px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">System</div>
            @can('users.manage')
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-users w-4"></i> Users
                </a>
            @endcan
            @can('roles.manage')
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-user-shield w-4"></i> Roles
                </a>
            @endcan
            @can('settings.manage')
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : 'text-white/80' }}">
                    <i class="fas fa-cog w-4"></i> Settings
                </a>
            @endcan
        </nav>

        <div class="px-4 py-4 border-t border-white/10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-xs text-white/60 truncate">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full text-left sidebar-link text-white/70 hover:text-white">
                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto">
        <div class="sticky top-0 z-10 bg-white border-b px-8 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-3">
                @yield('page-title', 'Dashboard')
                <span class="px-2 py-0.5 text-xs rounded-md bg-blue-50 text-blue-700 border border-blue-200 font-medium">
                    {{ app()->getLocale() === 'bn' ? 'বাংলা' : 'English' }}
                </span>
            </h1>
            <div class="flex items-center gap-4">
                <div class="relative group">
                    <button type="button" class="flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-blue-600 transition">
                        <i class="fas fa-globe"></i> {{ app()->getLocale() === 'bn' ? 'বাংলা' : 'English' }}
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <div class="py-1">
                            <a href="?admin_locale=bn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ app()->getLocale() === 'bn' ? 'font-bold text-blue-600' : '' }}">বাংলা</a>
                            <a href="?admin_locale=en" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ app()->getLocale() === 'en' ? 'font-bold text-blue-600' : '' }}">English</a>
                        </div>
                    </div>
                </div>
                <div class="h-5 w-px bg-gray-200"></div>
                <a href="{{ url('/') }}" target="_blank" class="text-sm text-gray-500 hover:text-gray-900 flex items-center gap-1.5">
                    <i class="fas fa-external-link-alt text-xs"></i> View Site
                </a>
                @yield('header-actions')
            </div>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-2xl">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

</body>
</html>
