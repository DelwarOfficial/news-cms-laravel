<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — NewsCore Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200; }
        .sidebar-link:hover { @apply bg-white/10; }
        .sidebar-link.active { @apply bg-white text-blue-700 shadow-md; }
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
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-home w-4"></i> Dashboard
            </a>
            <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ request()->routeIs('admin.posts*') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-newspaper w-4"></i> Posts
            </a>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-folder w-4"></i> Categories
            </a>
            <a href="{{ route('admin.tags.index') }}" class="sidebar-link {{ request()->routeIs('admin.tags*') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-tags w-4"></i> Tags
            </a>
            <a href="{{ route('admin.media.index') }}" class="sidebar-link {{ request()->routeIs('admin.media*') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-images w-4"></i> Media
            </a>
            <a href="{{ route('admin.comments.index') }}" class="sidebar-link {{ request()->routeIs('admin.comments*') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-comments w-4"></i> Comments
            </a>
            <div class="pt-3 pb-1 px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">System</div>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-users w-4"></i> Users
            </a>
            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : 'text-white/80' }}">
                <i class="fas fa-cog w-4"></i> Settings
            </a>
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
            <h1 class="text-xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-3">
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
