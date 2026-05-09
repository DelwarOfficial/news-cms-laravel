<!DOCTYPE html>
<html>
<head>
    <title>NewsCore - Latest News</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-x-3">
                <div class="w-10 h-10 bg-black rounded-2xl flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">N</span>
                </div>
                <span class="text-3xl font-bold">NewsCore</span>
            </div>
            <a href="/admin" class="px-5 py-2 bg-black text-white rounded-2xl text-sm">Admin Panel</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-8 py-12">
        <h1 class="text-5xl font-bold tracking-tighter mb-10">Latest Stories</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($latestPosts as $post)
                <div class="bg-white rounded-2xl overflow-hidden border">
                    <div class="h-48 bg-gray-200"></div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl">{{ $post->title }}</h3>
                        <div class="text-sm text-gray-500 mt-3">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-20 text-gray-500">No articles published yet.</div>
            @endforelse
        </div>
    </div>
</body>
</html>