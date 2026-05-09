<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} | NewsCore</title>
    <meta name="description" content="{{ $category->meta_description ?? 'Latest news in ' . $category->name }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-x-3">
                <div class="w-10 h-10 bg-black rounded-2xl flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">N</span>
                </div>
                <span class="text-3xl font-bold">NewsCore</span>
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-8 py-12">
        <div class="mb-10">
            <h1 class="text-5xl font-bold tracking-tighter">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-xl text-gray-600 mt-4 max-w-2xl">{{ $category->description }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <div class="bg-white rounded-2xl overflow-hidden border hover:shadow-xl transition">
                    <div class="h-48 bg-gray-200"></div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl leading-tight">{{ $post->title }}</h3>
                        <div class="text-sm text-gray-500 mt-4">{{ $post->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 text-gray-500">No articles in this category yet.</div>
            @endforelse
        </div>
        
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</body>
</html>