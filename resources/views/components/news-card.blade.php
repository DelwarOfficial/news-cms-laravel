{{-- News Card: image + title + optional excerpt + meta --}}
{{-- Usage:
    <x-news-card :article="$article" size="sm|md|lg" />
--}}

@props([
    'article',
    'size' => 'md',
    'showExcerpt' => false,
    'showTime' => false,
    'showCategory' => false,
    'overlay' => false,
])

@php
    $imageSizes = [
        'sm' => 'w-[110px] h-[80px]',
        'md' => 'w-full aspect-[16/9]',
        'lg' => 'w-full aspect-[335/364]',
    ];
    $imageClass = $imageSizes[$size] ?? $imageSizes['md'];
@endphp

<a href="{{ route('article.show', $article['slug']) }}" class="group flex {{ $size === 'sm' ? 'flex-row gap-3' : 'flex-col' }}">
    @if($overlay)
        <div class="relative overflow-hidden {{ $imageClass }} rounded-md">
            <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <h3 class="font-serif font-extrabold text-white text-[16px] leading-snug line-clamp-2">
                    {{ $article['title'] }}
                </h3>
                @if($showExcerpt && $article['excerpt'] ?? false)
                    <p class="text-white/70 text-[13px] mt-1 line-clamp-2">{{ $article['excerpt'] }}</p>
                @endif
                @if($showTime && $article['time_ago'] ?? false)
                    <span class="text-white/60 text-[11px] mt-1">{{ $article['time_ago'] }}</span>
                @endif
            </div>
        </div>
    @else
        <div class="overflow-hidden {{ $imageClass }} mb-2">
            <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
        </div>
        @if($showCategory && $article['category'] ?? false)
            <span class="text-primary text-[11px] font-semibold mb-1">{{ $article['category'] }}</span>
        @endif
        <h3 class="font-serif font-extrabold text-fg {{ $size === 'sm' ? 'text-[14px]' : 'text-[16px]' }} leading-snug group-hover:text-primary transition-colors line-clamp-2">
            {{ $article['title'] }}
        </h3>
        @if($showExcerpt && $article['excerpt'] ?? false)
            <p class="text-fg-secondary text-[13px] mt-1 line-clamp-2 leading-relaxed">{{ $article['excerpt'] }}</p>
        @endif
        @if($showTime && $article['time_ago'] ?? false)
            <span class="text-fg-muted text-[11px] mt-1">{{ $article['time_ago'] }}</span>
        @endif
    @endif
</a>
