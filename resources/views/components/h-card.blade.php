{{-- Horizontal Card: image left + text right (for sidebars/lists) --}}
{{-- Usage: <x-h-card :article="$article" /> --}}

@props(['article', 'showTime' => false, 'showCategory' => false])

<a href="{{ route('article.show', $article['slug']) }}" class="group flex items-start gap-3">
    <div class="w-[80px] h-[55px] shrink-0 overflow-hidden rounded-sm">
        <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" loading="lazy"
             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
    </div>
    <div class="flex-1 min-w-0 pt-0.5">
        @if($showCategory && $article['category'] ?? false)
            <span class="text-primary text-[11px] font-semibold">{{ $article['category'] }}</span>
        @endif
        <h3 class="font-serif font-extrabold text-fg text-[14px] leading-snug group-hover:text-primary transition-colors line-clamp-2">
            {{ $article['title'] }}
        </h3>
        @if($showTime && $article['time_ago'] ?? false)
            <span class="text-fg-muted text-[11px] mt-0.5">{{ $article['time_ago'] }}</span>
        @endif
    </div>
</a>
