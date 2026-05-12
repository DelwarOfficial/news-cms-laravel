{{-- Grid Card: image on top, category, title, time below --}}
{{-- Usage: <x-cards.grid :article="$a" /> --}}

@props(['article', 'showCategory' => true, 'showTime' => true, 'aspectRatio' => '16/9', 'titleSize' => 15, 'class' => ''])

<a href="{{ route('article.show', $article['slug']) }}" class="group flex flex-col {{ $class }}">
    @if(!empty($article['image_url']))
        <div class="w-full overflow-hidden mb-2" style="aspect-ratio: {{ $aspectRatio }};">
            <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
        </div>
    @endif
    @if($showCategory && !empty($article['category']))
        <span class="text-[#e2231a] font-bold text-[12px] mb-0.5">{{ $article['category'] }}</span>
    @endif
    <h3 class="font-serif font-bold text-[{{ $titleSize }}px] text-fg leading-tight group-hover:text-[#e2231a] transition-colors line-clamp-2">
        {{ $article['title'] }}
    </h3>
    @if($showTime && !empty($article['time_ago']))
        <div class="text-[11px] text-fg-muted mt-1">{{ $article['time_ago'] }}</div>
    @endif
</a>
