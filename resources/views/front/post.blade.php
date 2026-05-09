<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->meta_title ?? $post->title }} | NewsCore</title>
    <meta name="description" content="{{ $post->meta_description ?? Str::limit(strip_tags($post->excerpt), 160) }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white border-b">
        <div class="max-w-4xl mx-auto px-8 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-x-3">
                <div class="w-9 h-9 bg-black rounded-2xl flex items-center justify-center">
                    <span class="text-white text-xl font-bold">N</span>
                </div>
                <span class="text-2xl font-bold">NewsCore</span>
            </a>
            <a href="{{ route('home') }}" class="text-sm">← Back to Home</a>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-8 py-12">
        <article>
            @if($post->is_breaking)
                <div class="mb-4">
                    <span class="px-4 py-1 bg-red-600 text-white text-xs font-bold tracking-widest rounded-full">BREAKING NEWS</span>
                </div>
            @endif

            <h1 class="text-5xl font-bold tracking-tighter leading-none">{{ $post->title }}</h1>
            
            <div class="flex items-center gap-x-4 mt-6 text-sm text-gray-600">
                <div>{{ $post->author->name ?? 'Staff' }}</div>
                <div>•</div>
                <div>{{ $post->created_at->format('F d, Y') }}</div>
                <div>•</div>
                <div>{{ $post->reading_time }} min read</div>
            </div>

            @if($post->featured_image)
                <div class="my-10 rounded-3xl overflow-hidden">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full" alt="{{ $post->featured_image_alt }}">
                </div>
            @endif

            <div class="prose prose-lg max-w-none text-gray-800">
                {!! nl2br(e($post->content)) !!}
            </div>
        </article>

        <!-- Facebook Comments -->
        <div class="mt-16 pt-10 border-t">
            <h3 class="text-2xl font-bold mb-6">Comments</h3>
            <div class="fb-comments" data-href="{{ url()->current() }}" data-width="100%" data-numposts="5"></div>
        </div>
    </div>

    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v19.0"></script>
</body>
</html>