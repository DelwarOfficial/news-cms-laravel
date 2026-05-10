@extends('front.layouts.app')
@section('title', $author->name . ' - NewsCore Author')

@section('content')
<div class="bg-white border-b border-gray-200 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-24 h-24 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-4xl font-bold mx-auto mb-6">
            {{ substr($author->name, 0, 1) }}
        </div>
        <h1 class="text-4xl font-serif font-black mb-2">{{ $author->name }}</h1>
        <p class="text-blue-600 font-bold uppercase tracking-widest text-sm mb-6">Journalist &middot; {{ $posts->total() }} Articles</p>
        
        @if($author->bio)
        <p class="text-gray-600 max-w-2xl mx-auto text-lg">{{ $author->bio }}</p>
        @endif

        <div class="flex justify-center gap-4 mt-6">
            @if($author->twitter)
            <a href="{{ $author->twitter }}" target="_blank" class="text-gray-400 hover:text-blue-400 transition"><i class="fab fa-twitter text-xl"></i></a>
            @endif
            @if($author->facebook)
            <a href="{{ $author->facebook }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition"><i class="fab fa-facebook-f text-xl"></i></a>
            @endif
            @if($author->website)
            <a href="{{ $author->website }}" target="_blank" class="text-gray-400 hover:text-gray-900 transition"><i class="fas fa-link text-xl"></i></a>
            @endif
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-black uppercase tracking-wider mb-8 border-b-2 border-black pb-4">Latest from {{ $author->name }}</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        @forelse($posts as $post)
        <article class="group">
            <a href="{{ route('post.show', $post->slug) }}" class="block overflow-hidden rounded-2xl mb-4 aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1585829365295-ab7cd400c167?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            </a>
            <div>
                <h3 class="text-xl font-serif font-bold leading-snug mb-3 group-hover:text-blue-600 transition">
                    <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                <p class="text-gray-600 text-sm line-clamp-2 mb-4">
                    {{ Str::limit($post->excerpt ?? strip_tags($post->content), 100) }}
                </p>
                <div class="text-xs text-gray-500 font-medium uppercase tracking-wider">
                    {{ $post->created_at->format('M d, Y') }} &middot; {{ $post->reading_time }} MIN READ
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-3 text-center py-12 text-gray-400">
            No articles published by this author yet.
        </div>
        @endforelse
    </div>
    
    @if($posts->hasPages())
    <div class="mt-12 pt-8 border-t border-gray-200">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
