@extends('front.layouts.app')
@section('title', $post->title . ' - NewsCore')

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <header class="text-center mb-10">
        <a href="#" class="inline-block text-blue-600 font-bold uppercase tracking-widest text-sm mb-4 hover:underline">
            {{ $post->categories->first()->name ?? 'Uncategorized' }}
        </a>
        <h1 class="text-4xl md:text-6xl font-serif font-black leading-tight mb-6">
            {{ $post->title }}
        </h1>
        @if($post->excerpt)
        <p class="text-xl text-gray-600 font-serif italic mb-8 max-w-3xl mx-auto">
            {{ $post->excerpt }}
        </p>
        @endif
        
        <div class="flex items-center justify-center gap-4 text-sm text-gray-500 uppercase tracking-widest font-medium">
            <span class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-700 font-bold">{{ substr($post->author->name ?? 'A', 0, 1) }}</div>
                By {{ $post->author->name ?? 'Admin' }}
            </span>
            <span>&middot;</span>
            <span>{{ $post->created_at->format('F d, Y') }}</span>
            <span>&middot;</span>
            <span>{{ $post->reading_time }} MIN READ</span>
        </div>
    </header>

    <figure class="mb-12">
        <img src="https://images.unsplash.com/photo-1495020689067-958852a7765e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Featured" class="w-full rounded-3xl object-cover h-[500px]">
        @if($post->featured_image_caption)
        <figcaption class="text-center text-sm text-gray-500 mt-3">{{ $post->featured_image_caption }}</figcaption>
        @endif
    </figure>

    <div class="prose prose-lg prose-blue mx-auto font-serif">
        {!! nl2br(e($post->content)) !!}
    </div>

    <div class="mt-16 pt-8 border-t border-gray-200">
        <h3 class="font-black uppercase tracking-wider text-sm mb-4">Share this article</h3>
        <div class="flex gap-4">
            <a href="#" class="w-12 h-12 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-500 hover:text-blue-500 transition"><i class="fab fa-twitter"></i></a>
            <a href="#" class="w-12 h-12 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-600 hover:text-blue-600 transition"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="w-12 h-12 rounded-full border border-gray-300 flex items-center justify-center hover:border-blue-400 hover:text-blue-400 transition"><i class="fas fa-link"></i></a>
        </div>
    </div>

</article>

{{-- Schema.org JSON-LD --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "{{ $post->title }}",
  "image": [
    "https://images.unsplash.com/photo-1495020689067-958852a7765e?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80"
   ],
  "datePublished": "{{ $post->created_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": [{
      "@type": "Person",
      "name": "{{ $post->author->name ?? 'Admin' }}",
      "url": "{{ route('author.show', $post->author->username ?? 'admin') }}"
    }]
}
</script>
@endsection