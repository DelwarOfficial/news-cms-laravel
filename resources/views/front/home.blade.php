@extends('front.layouts.app')
@section('title', 'NewsCore - Latest Updates')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Top Headline Section --}}
    @if($latestPosts->isNotEmpty())
        @php $featured = $latestPosts->first(); @endphp
        <div class="mb-16">
            <a href="{{ route('post.show', $featured->slug) }}" class="group block cursor-pointer">
                <div class="relative h-[600px] rounded-3xl overflow-hidden mb-6">
                    <img src="https://images.unsplash.com/photo-1495020689067-958852a7765e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="News Image" class="w-full h-full object-cover transform group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-10 w-full md:w-3/4">
                        <span class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-bold uppercase tracking-wider rounded-full mb-4">Breaking</span>
                        <h1 class="text-4xl md:text-6xl font-serif font-black text-white leading-tight mb-4 group-hover:underline decoration-4 underline-offset-8">
                            {{ $featured->title }}
                        </h1>
                        <p class="text-gray-300 text-lg md:text-xl line-clamp-2">
                            {{ $featured->excerpt ?? strip_tags($featured->content) }}
                        </p>
                    </div>
                </div>
            </a>
        </div>
    @endif

    {{-- News Grid Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        <div class="lg:col-span-8">
            <div class="flex items-center justify-between border-b-2 border-black pb-4 mb-8">
                <h2 class="text-2xl font-black uppercase tracking-wider">Latest Stories</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($latestPosts->skip(1) as $post)
                <article class="group">
                    <a href="{{ route('post.show', $post->slug) }}" class="block overflow-hidden rounded-2xl mb-4 aspect-video">
                        <img src="https://images.unsplash.com/photo-1585829365295-ab7cd400c167?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </a>
                    <div>
                        <span class="text-blue-600 text-xs font-bold uppercase tracking-wider mb-2 block">Technology</span>
                        <h3 class="text-xl font-serif font-bold leading-snug mb-2 group-hover:text-blue-600 transition">
                            <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
                        </h3>
                        <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                            {{ $post->excerpt ?? strip_tags($post->content) }}
                        </p>
                        <div class="text-xs text-gray-400 font-medium uppercase tracking-wider">
                            {{ $post->created_at->format('M d, Y') }} &middot; {{ $post->reading_time }} MIN READ
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
        
        {{-- Sidebar --}}
        <div class="lg:col-span-4 space-y-12">
            <div>
                <div class="flex items-center justify-between border-b-2 border-black pb-4 mb-8">
                    <h2 class="text-xl font-black uppercase tracking-wider">Trending</h2>
                </div>
                <div class="space-y-6">
                    @foreach($latestPosts->take(5) as $i => $post)
                    <a href="{{ route('post.show', $post->slug) }}" class="group flex gap-4 items-start">
                        <span class="text-4xl font-black text-gray-200">{{ $i + 1 }}</span>
                        <div>
                            <h4 class="font-bold font-serif leading-tight group-hover:text-blue-600 transition">{{ $post->title }}</h4>
                            <span class="text-xs text-gray-400 uppercase tracking-widest mt-2 block">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-gray-100 p-8 rounded-3xl text-center">
                <h3 class="font-black text-2xl mb-3 font-serif">Stay Updated</h3>
                <p class="text-sm text-gray-600 mb-6">Join our newsletter to receive the latest news daily.</p>
                <input type="email" placeholder="Email Address" class="w-full px-4 py-3 rounded-xl border border-gray-300 mb-3 outline-none focus:ring-2 focus:ring-blue-500">
                <button class="w-full bg-black text-white font-bold py-3 rounded-xl hover:bg-gray-800 transition">Subscribe</button>
            </div>
        </div>
    </div>
</div>
@endsection