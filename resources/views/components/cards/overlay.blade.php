{{-- Overlay Card: image with gradient overlay, white text at bottom --}}
{{-- Usage: <x-cards.overlay :article="$a" size="large" /> --}}

@props(['article', 'size' => 'medium', 'titleSize' => null, 'class' => ''])

@php
    $sizes = [
        'large'  => ['title' => 20, 'badge' => 'absolute bottom-0 left-0 right-0 px-4 py-3'],
        'medium' => ['title' => 14, 'badge' => 'absolute bottom-0 left-0 right-0 px-2.5 py-2'],
        'small'  => ['title' => 13, 'badge' => 'absolute bottom-0 left-0 right-0 px-2 py-1.5'],
    ];
    $s = $sizes[$size] ?? $sizes['medium'];
    $ts = $titleSize ?? $s['title'];
@endphp

<a href="{{ route('article.show', $article['slug']) }}" class="group relative w-full overflow-hidden {{ $class }}">
    <div class="w-full aspect-video overflow-hidden">
        <img src="{{ $article['image_url'] ?? '' }}" alt="{{ $article['title'] }}" loading="lazy"
             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
    </div>
    <div class="{{ $s['badge'] }}">
        <h3 class="font-serif font-extrabold text-[{{ $ts }}px] text-white leading-tight group-hover:text-[#f8a0a0] transition-colors line-clamp-2 drop-shadow">
            {{ $article['title'] }}
        </h3>
        @if(!empty($article['time_ago']))
            <div class="text-[12px] text-white/70 mt-1">{{ $article['time_ago'] }}</div>
        @endif
    </div>
</a>
