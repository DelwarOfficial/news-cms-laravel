<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $pageTitle = trim($__env->yieldContent('title', $metaTitle ?? 'ঢাকা ম্যাগাজিন'));
        $pageDescription = trim($__env->yieldContent('meta_description', $metaDescription ?? 'ঢাকা ম্যাগাজিন - বাংলাদেশের নির্ভরযোগ্য অনলাইন নিউজ পোর্টাল'));
        $pageCanonical = $canonicalUrl ?? url()->current();
        $pageImageUrl = $pageImage ?? asset('images/dhaka-magazine-color-logo.svg');
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $pageCanonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:image" content="{{ $pageImageUrl }}">
    <meta property="og:site_name" content="Dhaka Magazine">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImageUrl }}">
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-bg text-fg">

    @include('components.dhaka-magazine-scroll.scroll-nav')
    @include('partials.header')

    <main class="flex-1 w-full">
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
