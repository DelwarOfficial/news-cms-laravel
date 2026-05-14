@extends('front.layouts.app')
@section('title', $category->name . ' - NewsCore')

@section('content')
<div class="bg-black text-white py-20 mb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl md:text-7xl font-serif font-black mb-4">{{ $category->name }}</h1>
        @if($category->description)
        <p class="text-xl text-gray-400 max-w-2xl mx-auto">{{ $category->description }}</p>
        @endif
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 mb-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        @forelse($posts as $post)
        <article class="group">
            <a href="{{ route('post.show', $post->slug) }}" class="block overflow-hidden rounded-2xl mb-4 aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1585829365295-ab7cd400c167?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Thumbnail" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            </a>
            <div>
                <h3 class="text-2xl font-serif font-bold leading-snug mb-3 group-hover:text-blue-600 transition">
                    <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                <p class="text-gray-600 text-base line-clamp-3 mb-4">
                    {{ $post->excerpt ?? strip_tags($post->content) }}
                </p>
                <div class="flex items-center gap-3 text-xs text-gray-500 font-medium uppercase tracking-wider">
                    <span>{{ $post->created_at->format('M d, Y') }}</span>
                    <span>&middot;</span>
                    <span>{{ $post->reading_time }} MIN READ</span>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-3 text-center py-20">
            <i class="fas fa-folder-open text-6xl text-gray-200 mb-4 block"></i>
            <h3 class="text-2xl font-bold text-gray-600">No posts found</h3>
            <p class="text-gray-400 mt-2">Check back later for updates in this category.</p>
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