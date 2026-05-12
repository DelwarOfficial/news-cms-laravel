{{-- Horizontal Card: text left, small thumbnail right --}}
{{-- Usage: <x-cards.horizontal :article="$a" /> --}}

@props(['article', 'showCategory' => true, 'showTime' => true, 'showExcerpt' => false, 'imageWidth' => 88, 'imageHeight' => 50, 'titleSize' => 15, 'class' => ''])

<a href="{{ route('article.show', $article['slug']) }}" class="group flex items-start gap-3 py-3 border-b border-border last:border-b-0 {{ $class }}">
    <div class="flex-1 min-w-0">
        @if($showCategory && !empty($article['category']))
            <span class="text-[#e2231a] font-bold text-[12px] block mb-0.5">{{ $article['category'] }} &bull;</span>
        @endif
        <h3 class="font-serif font-bold text-[{{ $titleSize }}px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3">
            {{ $article['title'] }}
        </h3>
        @if($showExcerpt && !empty($article['excerpt']))
            <p class="text-fg-secondary text-[12px] line-clamp-2 mt-1 leading-relaxed">{{ $article['excerpt'] }}</p>
        @endif
        @if($showTime && !empty($article['time_ago']))
            <div class="text-[11px] text-fg-muted mt-1">{{ $article['time_ago'] }}</div>
        @endif
    </div>
    @if(!empty($article['image_url']))
        <div class="w-[{{ $imageWidth }}px] h-[{{ $imageHeight }}px] shrink-0 overflow-hidden">
            <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
        </div>
    @endif
</a>
