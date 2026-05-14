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
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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

        /* Sidebar */
        .sidebar-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 1rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; }

        /* Rich text editor */
        .newscore-richtext trix-toolbar { border: 1px solid #e2e8f0; border-radius: 0.75rem 0.75rem 0 0; background: #f8fafc; padding: 0.5rem 0.75rem 0; overflow-x: auto; max-width: 100%; }
        .dark .newscore-richtext trix-toolbar { border-color: #334155; background: #1e293b; }
        .newscore-richtext trix-editor { min-height: 220px; border: 1px solid #e2e8f0; border-top: 0; border-radius: 0 0 0.75rem 0.75rem; padding: 0.75rem 1rem; font-size: 0.875rem; line-height: 1.75; outline: none; background: #fff; max-width: 100%; overflow-wrap: anywhere; }
        .dark .newscore-richtext trix-editor { border-color: #334155; background: #0f172a; }
        .newscore-richtext trix-editor:focus { box-shadow: 0 0 0 2px #3b82f6; border-color: transparent; }
        .cms-editor-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.25rem; border: 1px solid #e2e8f0; border-radius: 0.75rem 0.75rem 0 0; background: #f8fafc; padding: 0.5rem 0.75rem; }
        .dark .cms-editor-toolbar { border-color: #334155; background: #1e293b; }
        .cms-editor-button { display: inline-flex; height: 2rem; min-width: 2rem; align-items: center; justify-content: center; border-radius: 0.5rem; padding: 0 0.5rem; font-size: 0.75rem; font-weight: 600; color: #64748b; transition: all .15s; }
        .cms-editor-button:hover { background: #fff; color: #3b82f6; }
        .dark .cms-editor-button:hover { background: #1e293b; color: #60a5fa; }
        .cms-body-editor { min-height: 320px; width: 100%; overflow-y: auto; border: 1px solid #e2e8f0; border-top: 0; border-radius: 0 0 0.75rem 0.75rem; background: #fff; padding: 0.75rem 1rem; font-size: 0.875rem; line-height: 1.75; outline: none; overflow-wrap: anywhere; }
        .dark .cms-body-editor { border-color: #334155; background: #0f172a; color: #e2e8f0; }
        .cms-body-editor:focus { box-shadow: 0 0 0 2px #3b82f6; border-color: transparent; }
        .cms-body-editor:empty:before { content: attr(data-placeholder); color: #94a3b8; }
        .trix-content .text-right { text-align: right; }

        /* ── Theme System ─────────────────────────────── */
        /* Light */
        .admin-body { --bg-page: #f1f5f9; --bg-card: #ffffff; --bg-subtle: #f8fafc; --bg-hover: #f1f5f9; --border: #e2e8f0; --border-light: #f0f2f5; --text-primary: #0f172a; --text-secondary: #475569; --text-muted: #94a3b8; }
        /* Dark */
        .dark .admin-body { --bg-page: #0b1120; --bg-card: #131c31; --bg-subtle: #1a2640; --bg-hover: #1e2a45; --border: #1e2f4a; --border-light: #1a2a40; --text-primary: #f1f5f9; --text-secondary: #94a3b8; --text-muted: #64748b; }
        .admin-body { background: var(--bg-page); color: var(--text-primary); }

        /* Cards */
        .admin-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: .875rem; box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 1px 2px rgba(0,0,0,.03); transition: box-shadow .2s; }
        .admin-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.06), 0 2px 4px rgba(0,0,0,.04); }

        /* Page header area */
        .admin-page-header { background: var(--bg-card); border-bottom: 1px solid var(--border); }

        /* Stats cards */
        .admin-stat { background: var(--bg-card); border: 1px solid var(--border); border-radius: .875rem; padding: 1.5rem; transition: box-shadow .2s, transform .2s; }
        .admin-stat:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateY(-1px); }

        /* ── Tables ───────────────────────────────────── */
        .admin-table { width: 100%; font-size: .875rem; border-collapse: separate; border-spacing: 0; }
        .admin-table thead { background: var(--bg-subtle); }
        .admin-table thead th { font-weight: 600; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); padding: .875rem 1.25rem; white-space: nowrap; }
        .admin-table tbody tr { transition: background .12s; }
        .admin-table tbody tr:hover { background: var(--bg-hover); }
        .admin-table tbody td { padding: .875rem 1.25rem; border-bottom: 1px solid var(--border-light); color: var(--text-secondary); }
        .admin-table tbody td:first-child { color: var(--text-primary); font-weight: 500; }
        thead.bg-gray-50 { background: var(--bg-subtle) !important; }
        thead.bg-gray-50 th { font-weight: 600; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); padding: .875rem 1.25rem !important; white-space: nowrap; }
        .dark thead.bg-gray-50 { background: var(--bg-subtle) !important; }
        tbody.divide-y.divide-gray-50 > tr:hover { background: var(--bg-hover) !important; }
        .dark tbody.divide-y.divide-gray-50 > tr:hover { background: var(--bg-hover) !important; }

        /* ── Buttons ───────────────────────────────────── */
        .btn { display: inline-flex; align-items: center; gap: .5rem; font-size: .8125rem; font-weight: 600; padding: .625rem 1.25rem; border-radius: .625rem; transition: all .15s; letter-spacing: -.01em; cursor: pointer; border: none; line-height: 1; }
        .btn-sm { font-size: .8125rem; padding: .5rem 1rem; border-radius: .5rem; }
        .btn-xs { font-size: .75rem; padding: .375rem .75rem; border-radius: .4375rem; }
        .btn-primary { background: #2563eb; color: #fff; box-shadow: 0 1px 2px rgba(37,99,235,.2); }
        .btn-primary:hover { background: #1d4ed8; box-shadow: 0 4px 12px rgba(37,99,235,.3); transform: translateY(-.5px); }
        .btn-success { background: #059669; color: #fff; box-shadow: 0 1px 2px rgba(5,150,105,.2); }
        .btn-success:hover { background: #047857; box-shadow: 0 4px 12px rgba(5,150,105,.3); transform: translateY(-.5px); }
        .btn-warning { background: #d97706; color: #fff; box-shadow: 0 1px 2px rgba(217,119,6,.2); }
        .btn-warning:hover { background: #b45309; box-shadow: 0 4px 12px rgba(217,119,6,.3); transform: translateY(-.5px); }
        .btn-danger { background: #dc2626; color: #fff; box-shadow: 0 1px 2px rgba(220,38,38,.2); }
        .btn-danger:hover { background: #b91c1c; box-shadow: 0 4px 12px rgba(220,38,38,.3); transform: translateY(-.5px); }
        .btn-ghost { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--bg-hover); border-color: #cbd5e1; }
        .dark .btn-ghost:hover { border-color: #334155; }

        /* Override existing blue primary */
        [class*="bg-blue-600 hover:bg-blue-700"] { transition: all .15s; color: #fff !important; }
        [class*="bg-blue-600 hover:bg-blue-700"]:hover { transform: translateY(-.5px); }

        /* Action icon buttons */
        .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 2.125rem; height: 2.125rem; border-radius: .5rem; transition: all .15s; color: #94a3b8; }
        .action-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
        .action-btn-edit:hover { background: #eff6ff; color: #2563eb; }
        .action-btn-delete:hover { background: #fef2f2; color: #dc2626; }
        .action-btn-clone:hover { background: #fffbeb; color: #d97706; }
        .action-btn-view:hover { background: #f1f5f9; color: #475569; }
        .dark .action-btn-edit:hover { background: rgba(37,99,235,.12); color: #60a5fa; }
        .dark .action-btn-delete:hover { background: rgba(220,38,38,.12); color: #fca5a5; }
        .dark .action-btn-clone:hover { background: rgba(217,119,6,.12); color: #fbbf24; }
        .dark .action-btn-view:hover { background: rgba(255,255,255,.06); color: #cbd5e1; }

        /* Override old action icon selectors */
        [class*="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50"] { color: #94a3b8; }
        [class*="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50"]:hover { color: #2563eb; background: #eff6ff; }
        [class*="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50"] { color: #94a3b8; }
        [class*="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50"]:hover { color: #dc2626; background: #fef2f2; }
        [class*="text-amber-600 hover:text-amber-800 p-2 rounded-lg hover:bg-amber-50"] { color: #94a3b8; }
        [class*="text-amber-600 hover:text-amber-800 p-2 rounded-lg hover:bg-amber-50"]:hover { color: #d97706; background: #fffbeb; }

        /* ── Form elements ─────────────────────────────── */
        input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):not([type="submit"]):focus,
        select:focus, textarea:focus { border-color: #93c5fd !important; box-shadow: 0 0 0 3px rgba(59,130,246,.12) !important; }
        label:where(.block) { font-size: .8125rem; font-weight: 600; color: #334155; margin-bottom: .5rem; letter-spacing: -.01em; }
        .dark label:where(.block) { color: #cbd5e1; }
        select:not([multiple]) { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right .75rem center; background-size: 12px; padding-right: 2.25rem !important; -webkit-appearance: none; appearance: none; }
        input, select, textarea { transition: all .15s; }
        .dark input, .dark select, .dark textarea { background: #1a2640; border-color: #1e2f4a; color: #e2e8f0; }
        .dark input:focus, .dark select:focus, .dark textarea:focus { background: #131c31; }

        /* ── Badges ────────────────────────────────────── */
        .badge { display: inline-flex; align-items: center; padding: .125rem .625rem; border-radius: 9999px; font-size: .75rem; font-weight: 600; letter-spacing: .01em; line-height: 1.25rem; }
        .badge-green { background: #ecfdf5; color: #059669; }
        .badge-amber { background: #fffbeb; color: #d97706; }
        .badge-red { background: #fef2f2; color: #dc2626; }
        .badge-indigo { background: #eef2ff; color: #4f46e5; }
        .badge-blue { background: #eff6ff; color: #2563eb; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }
        .badge-sky { background: #f0f9ff; color: #0284c7; }
        .dark .badge-green { background: rgba(5,150,105,.15); color: #6ee7b7; }
        .dark .badge-amber { background: rgba(217,119,6,.15); color: #fbbf24; }
        .dark .badge-red { background: rgba(220,38,38,.15); color: #fca5a5; }
        .dark .badge-indigo { background: rgba(79,70,229,.15); color: #a5b4fc; }
        .dark .badge-blue { background: rgba(37,99,235,.15); color: #93c5fd; }
        .dark .badge-gray { background: rgba(255,255,255,.06); color: #9ca3af; }
        .dark .badge-sky { background: rgba(2,132,199,.15); color: #7dd3fc; }

        /* Override inline Tailwind badges */
        span:where([class*="bg-green-50 text-green-700"]) { background: #ecfdf5 !important; color: #059669 !important; border-radius: 9999px; padding: .125rem .625rem !important; font-weight: 600 !important; font-size: .75rem !important; }
        span:where([class*="bg-gray-100 text-gray-500"]) { background: #f3f4f6 !important; color: #6b7280 !important; border-radius: 9999px; padding: .125rem .625rem !important; font-weight: 600 !important; font-size: .75rem !important; }
        span:where([class*="bg-amber-100 text-amber-700"]) { background: #fffbeb !important; color: #d97706 !important; border-radius: 9999px; padding: .125rem .625rem !important; font-weight: 600 !important; font-size: .75rem !important; }
        span:where([class*="bg-red-100 text-red-700"]) { background: #fef2f2 !important; color: #dc2626 !important; border-radius: 9999px; padding: .125rem .625rem !important; font-weight: 600 !important; font-size: .75rem !important; }
        span:where([class*="bg-indigo-100 text-indigo-700"]) { background: #eef2ff !important; color: #4f46e5 !important; border-radius: 9999px; padding: .125rem .625rem !important; font-weight: 600 !important; font-size: .75rem !important; }
        span:where([class*="bg-blue-50 text-blue-700"]) { background: #eff6ff !important; color: #2563eb !important; border-radius: 9999px; padding: .125rem .625rem !important; font-weight: 600 !important; font-size: .75rem !important; }
        .dark span:where([class*="bg-green-50 text-green-700"]) { background: rgba(5,150,105,.15) !important; color: #6ee7b7 !important; }
        .dark span:where([class*="bg-gray-100 text-gray-500"]) { background: rgba(255,255,255,.06) !important; color: #9ca3af !important; }
        .dark span:where([class*="bg-amber-100 text-amber-700"]) { background: rgba(217,119,6,.15) !important; color: #fbbf24 !important; }
        .dark span:where([class*="bg-red-100 text-red-700"]) { background: rgba(220,38,38,.15) !important; color: #fca5a5 !important; }
        .dark span:where([class*="bg-indigo-100 text-indigo-700"]) { background: rgba(79,70,229,.15) !important; color: #a5b4fc !important; }
        .dark span:where([class*="bg-blue-50 text-blue-700"]) { background: rgba(37,99,235,.15) !important; color: #93c5fd !important; }

        /* ── Alerts / Flash messages ──────────────────── */
        .alert { border-radius: .75rem; padding: .875rem 1.25rem; font-size: .875rem; display: flex; align-items: center; gap: .75rem; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .dark .alert-success { background: rgba(5,150,105,.12); border-color: rgba(5,150,105,.25); color: #6ee7b7; }
        .dark .alert-error { background: rgba(220,38,38,.12); border-color: rgba(220,38,38,.25); color: #fca5a5; }
        [class*="bg-green-50 dark:bg-green-900/30"] { background: #ecfdf5 !important; border-color: #a7f3d0 !important; color: #065f46 !important; border-radius: .75rem !important; padding: .875rem 1.25rem !important; }
        .dark [class*="bg-green-50 dark:bg-green-900/30"] { background: rgba(5,150,105,.12) !important; border-color: rgba(5,150,105,.25) !important; color: #6ee7b7 !important; }
        [class*="bg-red-50 dark:bg-red-900/30"] { background: #fef2f2 !important; border-color: #fecaca !important; color: #991b1b !important; border-radius: .75rem !important; padding: .875rem 1.25rem !important; }
        .dark [class*="bg-red-50 dark:bg-red-900/30"] { background: rgba(220,38,38,.12) !important; border-color: rgba(220,38,38,.25) !important; color: #fca5a5 !important; }

        /* ── Misc refinements ──────────────────────────── */
        .fa-sort, .fa-sort-up, .fa-sort-down { font-size: .6875rem !important; }
        input[type="checkbox"].rounded { border-radius: .375rem !important; }
        .dark [class*="bg-white"]:not(.dark\:bg-gray-800):not(.dark\:bg-gray-900):not(button):not(a) { background: var(--bg-card) !important; border-color: var(--border) !important; }
        .dark [class*="text-gray-900"]:not(.dark\:text-white):not(.dark\:text-gray-100) { color: var(--text-primary) !important; }
        .dark [class*="text-gray-500"]:not(.dark\:text-gray-400) { color: var(--text-secondary) !important; }
        .dark [class*="text-gray-600"] { color: var(--text-secondary) !important; }
        .dark [class*="text-gray-700"] { color: var(--text-primary) !important; }
        .dark [class*="border-gray-100"]:not(.dark\:border-gray-700):not(.dark\:border-gray-800) { border-color: var(--border) !important; }
        .dark [class*="border-gray-200"]:not(.dark\:border-gray-700):not(.dark\:border-gray-800) { border-color: var(--border) !important; }
        .dark [class*="bg-gray-50"]:not(.dark\:bg-gray-800):not(.dark\:bg-gray-900):not(dark\:bg-gray-950):not([class*="hover"]) { background: var(--bg-subtle) !important; }

        /* Hover states in dark */
        .dark [class*="hover:bg-gray-100"]:hover { background: rgba(255,255,255,.06) !important; }
        .dark [class*="hover:bg-gray-50"]:hover { background: rgba(255,255,255,.04) !important; }

        /* Dividers */
        [class*="divide-y divide-gray-50"] > * { border-color: var(--border-light) !important; }
        [class*="divide-y divide-gray-100"] > * { border-color: var(--border-light) !important; }

        /* Empty states */
        [class*="px-6 py-14 text-center"] [class*="mx-auto"] + [class*="font-semibold"] { margin-top: .75rem; font-size: .875rem; }
        [class*="px-6 py-14 text-center"] [class*="text-sm"] { margin-top: .25rem; }

        /* Pagination disabled */
        .dark [class*="border-gray-100 text-gray-300"] { border-color: rgba(255,255,255,.06) !important; color: #475569 !important; }
        .dark [class*="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50"] { border-color: var(--border) !important; color: var(--text-secondary) !important; }
        .dark [class*="rounded-xl border border-gray-200 px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-50"]:hover { background: var(--bg-hover) !important; }

        /* Mobile card items in dark */
        .dark [class*="lg:hidden divide-y"] { border-color: var(--border-light) !important; }
        .dark [class*="text-gray-400"]:where(.text-xs) { color: var(--text-muted) !important; }

        /* Pagination */
        .dark [class*="bg-white"][class*="border-gray-200"] { background: var(--bg-card) !important; border-color: var(--border) !important; color: var(--text-secondary) !important; }
        .dark [class*="bg-blue-600 text-white"] { box-shadow: 0 1px 2px rgba(59,130,246,.3); }

        /* Shadow on dark cards */
        .dark .shadow-sm { box-shadow: 0 1px 2px rgba(0,0,0,.2), 0 1px 3px rgba(0,0,0,.3) !important; }

        /* Search/filter toolbar */
        .dark [class*="bg-gray-900 hover:bg-gray-800"][class*="text-white"] { background: #2563eb !important; }
        .dark [class*="bg-gray-900 hover:bg-gray-800"][class*="text-white"]:hover { background: #1d4ed8 !important; }

        /* Mobile action buttons */
        .dark [class*="border-amber-200"][class*="text-amber-600"] { border-color: rgba(217,119,6,.35) !important; }
        .dark [class*="border-red-200"][class*="text-red-600"] { border-color: rgba(220,38,38,.35) !important; }

        /* Filter tab count badge */
        .dark [class*="bg-white text-gray-500"]:not(.dark\:bg-gray-800):not(table) { background: rgba(255,255,255,.08) !important; color: #94a3b8 !important; }

        /* Headings in cards */
        .dark h1, .dark h2, .dark h3, .dark h4 { color: var(--text-primary); }

        /* Search wrapper */
        .search-wrapper { position: relative; }
        .search-wrapper .fa-search { position: absolute; left: .875rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .8125rem; pointer-events: none; }
    </style>
    @stack('styles')
</head>
<body class="admin-body bg-gray-50 text-gray-900 antialiased">

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
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 scrollbar-thin" :class="sidebarCollapsed ? 'overflow-x-hidden' : ''">
            @php
                $groups = [
                    'dashboard' => [
                        'items' => [
                            'dashboard' => ['route' => 'admin.dashboard', 'icon' => 'fa-gauge-high', 'perm' => 'dashboard.view', 'label' => 'Dashboard'],
                        ],
                    ],
                    'content' => [
                        'label' => 'Content',
                        'icon' => 'fa-pen-nib',
                        'items' => [
                            'posts' => ['route' => 'admin.posts.index', 'icon' => 'fa-newspaper', 'perm' => 'posts.create', 'label' => 'Posts'],
                            'categories' => ['route' => 'admin.categories.index', 'icon' => 'fa-folder-tree', 'perm' => 'categories.manage', 'label' => 'Categories'],
                            'tags' => ['route' => 'admin.tags.index', 'icon' => 'fa-tags', 'perm' => 'tags.manage', 'label' => 'Tags'],
                            'media' => ['route' => 'admin.media.index', 'icon' => 'fa-images', 'perm' => 'media.manage', 'label' => 'Media'],
                            'comments' => ['route' => 'admin.comments.index', 'icon' => 'fa-comments', 'perm' => 'comments.manage', 'label' => 'Comments'],
                        ],
                    ],
                    'display' => [
                        'label' => 'Display',
                        'icon' => 'fa-palette',
                        'items' => [
                            'placements' => ['route' => 'admin.placements.index', 'icon' => 'fa-layer-group', 'perm' => 'posts.create', 'label' => 'Placements'],
                            'locations' => ['route' => 'admin.locations.index', 'icon' => 'fa-map-location-dot', 'perm' => 'categories.manage', 'label' => 'Locations'],
                            'widgets' => ['route' => 'admin.widgets.index', 'icon' => 'fa-puzzle-piece', 'perm' => 'menus.manage', 'label' => 'Widgets'],
                            'ads' => ['route' => 'admin.advertisements.index', 'icon' => 'fa-rectangle-ad', 'perm' => 'ads.manage', 'label' => 'Ads'],
                        ],
                    ],
                    'members' => [
                        'label' => 'Members',
                        'icon' => 'fa-id-card',
                        'items' => [
                            'members' => ['route' => 'admin.members.index', 'icon' => 'fa-id-badge', 'perm' => 'users.create', 'label' => 'Members'],
                        ],
                    ],
                    'system' => [
                        'label' => 'System',
                        'icon' => 'fa-gear',
                        'items' => [
                            'users' => ['route' => 'admin.users.index', 'icon' => 'fa-users', 'perm' => 'users.manage', 'label' => 'Users'],
                            'roles' => ['route' => 'admin.roles.index', 'icon' => 'fa-user-shield', 'perm' => 'roles.manage', 'label' => 'Roles'],
                            'api-keys' => ['route' => 'admin.api-keys.index', 'icon' => 'fa-key', 'perm' => 'users.manage', 'label' => 'API Keys'],
                            'api-docs' => ['route' => 'admin.api-docs.index', 'icon' => 'fa-book', 'perm' => 'users.manage', 'label' => 'API Docs'],
                            'backups' => ['route' => 'admin.backups.index', 'icon' => 'fa-hard-drive', 'perm' => 'backups.manage', 'label' => 'Backups'],
                            'settings' => ['route' => 'admin.settings.index', 'icon' => 'fa-sliders', 'perm' => 'settings.manage', 'label' => 'Settings'],
                        ],
                    ],
                ];

            @endphp

            @foreach($groups as $gKey => $group)
                @php
                    $visibleItems = array_filter($group['items'], fn($i) => auth()->user()->can($i['perm']));
                @endphp
                @if(empty($visibleItems))
                    @continue
                @endif

                @if($gKey === 'dashboard')
                    {{-- Dashboard -- standalone link, always visible --}}
                    <div class="mb-2">
                        @can('dashboard.view')
                            <a href="{{ route('admin.dashboard') }}"
                               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white shadow-sm font-medium' : 'text-white/60 hover:text-white hover:bg-white/10' }}"
                               :title="sidebarCollapsed ? 'Dashboard' : ''">
                                <i class="fas fa-gauge-high w-5 text-center shrink-0"></i>
                                <span x-show="!sidebarCollapsed" x-cloak class="font-semibold tracking-wide">Dashboard</span>
                            </a>
                        @endcan
                    </div>
                @else
                    {{-- Collapsible Group --}}
                    <div x-data="{ open: localStorage.getItem('sidebar_group_{{ $gKey }}') !== 'false' }"
                         x-init="if (!sidebarCollapsed && !localStorage.getItem('sidebar_group_{{ $gKey }}')) { localStorage.setItem('sidebar_group_{{ $gKey }}', 'true') }"
                         class="space-y-0.5">
                        <button @@click="open = !open; localStorage.setItem('sidebar_group_{{ $gKey }}', open)"
                                class="w-full flex items-center gap-3 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest text-white/40 hover:text-white/70 transition-colors sidebar-group-btn"
                                x-show="!sidebarCollapsed" x-cloak>
                            <i class="fas {{ $group['icon'] }} w-5 text-center shrink-0 text-[11px]"></i>
                            <span class="flex-1 text-left">{{ $group['label'] }}</span>
                            <i class="fas fa-chevron-down text-[9px] transition-transform duration-200"
                               :class="{ 'rotate-180': open }"></i>
                        </button>
                        <template x-if="sidebarCollapsed">
                            <div>
                                @foreach($visibleItems as $key => $item)
                                    @can($item['perm'])
                                        <a href="{{ route($item['route']) }}"
                                           class="sidebar-link {{ request()->routeIs($item['route'] . '*') ? 'bg-white/20 text-white shadow-sm' : 'text-white/60 hover:text-white hover:bg-white/10' }}"
                                           :title="'{{ $item['label'] }}'">
                                            <i class="fas {{ $item['icon'] }} w-5 text-center shrink-0"></i>
                                        </a>
                                    @endcan
                                @endforeach
                            </div>
                        </template>
                        <template x-if="!sidebarCollapsed">
                            <div x-show="open" x-collapse.duration.200ms>
                                @foreach($visibleItems as $key => $item)
                                    @can($item['perm'])
                                        <a href="{{ route($item['route']) }}"
                                           class="sidebar-link {{ request()->routeIs($item['route'] . '*') ? 'bg-white/20 text-white shadow-sm font-medium' : 'text-white/60 hover:text-white hover:bg-white/10' }}"
                                           :title="sidebarCollapsed ? '{{ $item['label'] }}' : ''">
                                            <i class="fas {{ $item['icon'] }} w-5 text-center shrink-0"></i>
                                            <span x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                                        </a>
                                    @endcan
                                @endforeach
                            </div>
                        </template>
                    </div>
                @endif
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
