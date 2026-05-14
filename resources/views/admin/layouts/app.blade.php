<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'bn' ? 'bn' : 'en' }}"
      x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true', dark: localStorage.getItem('darkMode') === 'true' }"
      x-init="() => { if (dark) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); }"
      x-on:dark-mode-toggle.window="dark = !dark; localStorage.setItem('darkMode', dark); document.documentElement.classList.toggle('dark', dark);"
      :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — NewsCore Admin</title>
    @vite(['resources/css/app.css'])
    @vite(['resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <x-rich-text::styles theme="richtextlaravel" />
    <script type="module">
        import Trix from "{{ asset('vendor/rich-text-laravel/trix.esm.js') }}";
        Trix.config.blockAttributes.alignRight = { tagName: 'div', className: 'text-right', nestable: false };
        document.addEventListener('trix-initialize', (event) => {
            const toolbar = event.target.toolbarElement;
            const blockTools = toolbar?.querySelector('.trix-button-group--block-tools');
            if (!blockTools || blockTools.querySelector('[data-trix-attribute="alignRight"]')) return;
            const button = document.createElement('button');
            button.type = 'button'; button.className = 'trix-button';
            button.setAttribute('data-trix-attribute', 'alignRight');
            button.setAttribute('title', 'Align right'); button.setAttribute('tabindex', '-1');
            button.textContent = 'R';
            blockTools.appendChild(button);
        });
    </script>
    <style>
        body { font-family: 'Inter', 'Noto Sans Bengali', sans-serif; }
        [x-cloak] { display: none !important; }
        @media (min-width: 1024px) { #admin-sidebar { display: flex !important; } }
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; }
        .newscore-richtext trix-toolbar { border: 1px solid var(--color-gray-200); border-radius: 0.75rem 0.75rem 0 0; background: var(--color-gray-50); padding: 0.5rem 0.75rem 0; overflow-x: auto; max-width: 100%; }
        .dark .newscore-richtext trix-toolbar { border-color: var(--color-gray-600); background: var(--color-gray-800); }
        .newscore-richtext trix-toolbar .trix-button-row { flex-wrap: wrap; gap: 0.25rem; }
        .newscore-richtext trix-toolbar .trix-button-group { margin-bottom: 0.25rem; }
        .newscore-richtext trix-toolbar .trix-button--icon-attach, .newscore-richtext trix-toolbar .trix-button-group--file-tools { display: none; }
        .newscore-richtext trix-toolbar .trix-dialogs { max-width: 100%; }
        .newscore-richtext trix-toolbar .trix-dialog { position: static; max-width: 100%; }
        .newscore-richtext trix-toolbar .trix-dialog__link-fields { flex-wrap: wrap; }
        .newscore-richtext trix-editor { min-height: 220px; border: 1px solid var(--color-gray-200); border-top: 0; border-radius: 0 0 0.75rem 0.75rem; padding: 0.75rem 1rem; font-size: 0.875rem; line-height: 1.75; outline: none; background: var(--color-white); max-width: 100%; overflow-wrap: anywhere; }
        .dark .newscore-richtext trix-editor { border-color: var(--color-gray-600); background: var(--color-gray-900); }
        .newscore-richtext trix-editor[aria-label="summary_en"], .newscore-richtext trix-editor[aria-label="summary_bn"] { min-height: 120px; }
        .newscore-richtext trix-editor:focus { --tw-ring-shadow: 0 0 0 calc(2px + 0) var(--tw-ring-color, #3b82f6); box-shadow: 0 0 0 2px #3b82f6; border-color: transparent; }
        .cms-editor-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.25rem; border: 1px solid var(--color-gray-200); border-radius: 0.75rem 0.75rem 0 0; background: var(--color-gray-50); padding: 0.5rem 0.75rem; }
        .dark .cms-editor-toolbar { border-color: var(--color-gray-600); background: var(--color-gray-800); }
        .cms-editor-button { display: inline-flex; height: 2rem; min-width: 2rem; align-items: center; justify-content: center; border-radius: 0.5rem; padding: 0 0.5rem; font-size: 0.75rem; font-weight: 600; color: var(--color-gray-600); }
        .dark .cms-editor-button { color: var(--color-gray-300); }
        .cms-editor-button:hover { background: var(--color-white); color: #3b82f6; }
        .dark .cms-editor-button:hover { background: var(--color-gray-700); }
        .cms-body-editor { min-height: 320px; width: 100%; overflow-y: auto; border: 1px solid var(--color-gray-200); border-top: 0; border-radius: 0 0 0.75rem 0.75rem; background: var(--color-white); padding: 0.75rem 1rem; font-size: 0.875rem; line-height: 1.75; outline: none; overflow-wrap: anywhere; }
        .dark .cms-body-editor { border-color: var(--color-gray-600); background: var(--color-gray-900); color: var(--color-gray-100); }
        .cms-body-editor:focus { --tw-ring-shadow: 0 0 0 calc(2px + 0) var(--tw-ring-color, #3b82f6); box-shadow: 0 0 0 2px #3b82f6; border-color: transparent; }
        .cms-body-editor:empty:before { content: attr(data-placeholder); color: #9ca3af; }
        .trix-content .text-right { text-align: right; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" x-cloak @@click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside id="admin-sidebar" :class="sidebarCollapsed ? 'w-[68px]' : 'w-64'"
           class="fixed lg:static inset-y-0 left-0 z-50 flex flex-col bg-gradient-to-b from-blue-700 to-blue-900 dark:from-gray-900 dark:to-gray-950 text-white transition-all duration-300 ease-in-out overflow-hidden"
           x-show="sidebarOpen" x-cloak
           @@keydown.window.escape="sidebarOpen = false">

        {{-- Logo + Collapse --}}
        <div class="flex items-center justify-between px-4 py-4 border-b border-white/10 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0 overflow-hidden">
                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shrink-0">
                    <i class="fas fa-bolt text-blue-700 text-sm"></i>
                </div>
                <span x-show="!sidebarCollapsed" class="font-bold text-lg tracking-tight whitespace-nowrap">NewsCore</span>
            </a>
            <div class="flex items-center gap-1">
                <button @@click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
                        class="hidden lg:flex items-center justify-center w-7 h-7 rounded-lg hover:bg-white/10 transition-colors shrink-0" title="Toggle sidebar">
                    <i class="fas fa-chevron-left text-xs" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
                </button>
                <button @@click="sidebarOpen = false" class="lg:hidden flex items-center justify-center w-7 h-7 rounded-lg hover:bg-white/10 transition-colors shrink-0">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Nav Links --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 scrollbar-thin" :class="sidebarCollapsed ? 'overflow-x-hidden' : ''">
            @php
                $nav = [
                    'dashboard' => ['route' => 'admin.dashboard', 'icon' => 'fa-home', 'perm' => 'dashboard.view', 'label' => 'Dashboard'],
                    'posts' => ['route' => 'admin.posts.index', 'icon' => 'fa-newspaper', 'perm' => 'posts.create', 'label' => 'Posts'],
                    'categories' => ['route' => 'admin.categories.index', 'icon' => 'fa-folder', 'perm' => 'categories.manage', 'label' => 'Categories'],
                    'placements' => ['route' => 'admin.placements.index', 'icon' => 'fa-layer-group', 'perm' => 'posts.create', 'label' => 'Placements'],
                    'locations' => ['route' => 'admin.locations.index', 'icon' => 'fa-map-location-dot', 'perm' => 'categories.manage', 'label' => 'Locations'],
                    'widgets' => ['route' => 'admin.widgets.index', 'icon' => 'fa-puzzle-piece', 'perm' => 'menus.manage', 'label' => 'Widgets'],
                    'ads' => ['route' => 'admin.advertisements.index', 'icon' => 'fa-ad', 'perm' => 'ads.manage', 'label' => 'Ads'],
                    'tags' => ['route' => 'admin.tags.index', 'icon' => 'fa-tags', 'perm' => 'tags.manage', 'label' => 'Tags'],
                    'media' => ['route' => 'admin.media.index', 'icon' => 'fa-images', 'perm' => 'media.manage', 'label' => 'Media'],
                    'comments' => ['route' => 'admin.comments.index', 'icon' => 'fa-comments', 'perm' => 'comments.manage', 'label' => 'Comments'],
                    'members' => ['route' => 'admin.members.index', 'icon' => 'fa-id-badge', 'perm' => 'users.create', 'label' => 'Members'],
                ];
                $system = [
                    'users' => ['route' => 'admin.users.index', 'icon' => 'fa-users', 'perm' => 'users.manage', 'label' => 'Users'],
                    'roles' => ['route' => 'admin.roles.index', 'icon' => 'fa-user-shield', 'perm' => 'roles.manage', 'label' => 'Roles'],
                    'api-keys' => ['route' => 'admin.api-keys.index', 'icon' => 'fa-key', 'perm' => 'users.manage', 'label' => 'API Keys'],
                    'api-docs' => ['route' => 'admin.api-docs.index', 'icon' => 'fa-book', 'perm' => 'users.manage', 'label' => 'API Docs'],
                    'backups' => ['route' => 'admin.backups.index', 'icon' => 'fa-hdd', 'perm' => 'backups.manage', 'label' => 'Backups'],
                    'settings' => ['route' => 'admin.settings.index', 'icon' => 'fa-cog', 'perm' => 'settings.manage', 'label' => 'Settings'],
                ];
            @endphp

            @foreach($nav as $key => $item)
                @can($item['perm'])
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-link {{ request()->routeIs($item['route'] . '*') ? 'bg-white/20 text-white shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}"
                       :title="sidebarCollapsed ? '{{ $item['label'] }}' : ''">
                        <i class="fas {{ $item['icon'] }} w-5 text-center shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                    </a>
                @endcan
            @endforeach

            <div class="pt-4 pb-1 px-4 text-xs font-semibold text-white/40 uppercase tracking-wider"
                 x-show="!sidebarCollapsed" x-cloak>System</div>

            @foreach($system as $key => $item)
                @can($item['perm'])
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-link {{ request()->routeIs($item['route'] . '*') ? 'bg-white/20 text-white shadow-sm' : 'text-white/70 hover:text-white hover:bg-white/10' }}"
                       :title="sidebarCollapsed ? '{{ $item['label'] }}' : ''">
                        <i class="fas {{ $item['icon'] }} w-5 text-center shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                    </a>
                @endcan
            @endforeach
        </nav>

        {{-- User Footer --}}
        <div class="px-3 py-4 border-t border-white/10 shrink-0" :class="sidebarCollapsed ? 'text-center' : ''">
            <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : ''">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div x-show="!sidebarCollapsed" x-cloak class="min-w-0 flex-1">
                    <div class="text-sm font-medium truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-xs text-white/60 truncate">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-medium text-white/60 hover:text-white hover:bg-white/10 transition-all duration-200" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <i class="fas fa-sign-out-alt w-5 text-center shrink-0"></i>
                    <span x-show="!sidebarCollapsed" x-cloak>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden" :class="sidebarCollapsed ? 'lg:ml-[68px]' : 'lg:ml-0'">
        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm">
            <div class="flex items-center justify-between px-4 lg:px-8 py-3 gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <button @@click="sidebarOpen = true" class="lg:hidden flex items-center justify-center w-9 h-9 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button @@click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
                            class="hidden lg:flex items-center justify-center w-9 h-9 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-400 transition-colors">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-lg lg:text-xl font-bold text-gray-900 dark:text-white truncate flex items-center gap-3">
                        @yield('page-title', 'Dashboard')
                        <span class="hidden sm:inline-flex px-2 py-0.5 text-xs rounded-md bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 font-medium">
                            {{ app()->getLocale() === 'bn' ? 'বাংলা' : 'English' }}
                        </span>
                    </h1>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    {{-- Dark Mode Toggle --}}
                    <button @@click="$dispatch('dark-mode-toggle')"
                            class="flex items-center justify-center w-9 h-9 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors" title="Toggle dark mode">
                        <i x-show="!dark" class="fas fa-moon text-sm"></i>
                        <i x-show="dark" x-cloak class="fas fa-sun text-sm text-amber-400"></i>
                    </button>

                    {{-- Locale Switcher --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @@click="open = !open" @@click.outside="open = false"
                                class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-globe"></i>
                            <span class="hidden sm:inline">{{ app()->getLocale() === 'bn' ? 'বাংলা' : 'English' }}</span>
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </button>
                        <div x-show="open" x-cloak @@click.outside="open = false"
                             class="absolute right-0 mt-2 w-36 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                            <form method="POST" action="{{ route('admin.locale.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="bn">
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 {{ app()->getLocale() === 'bn' ? 'font-bold text-blue-600 dark:text-blue-400' : '' }}">বাংলা</button>
                            </form>
                            <form method="POST" action="{{ route('admin.locale.switch') }}">
                                @csrf
                                <input type="hidden" name="locale" value="en">
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'font-bold text-blue-600 dark:text-blue-400' : '' }}">English</button>
                            </form>
                        </div>
                    </div>

                    <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

                    <a href="{{ url('/') }}" target="_blank"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <i class="fas fa-external-link-alt text-xs"></i> View Site
                    </a>

                    @yield('header-actions')
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="flex-1 overflow-y-auto p-4 lg:p-8">
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-5 py-4 rounded-2xl">
                    <i class="fas fa-check-circle text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-5 py-4 rounded-2xl">
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
