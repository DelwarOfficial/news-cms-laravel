{{-- Hero Card: large image, large title, excerpt, time --}}
{{-- Usage: <x-cards.hero :article="$featured" /> --}}

@props(['article', 'imagePosition' => 'left', 'showTime' => true, 'titleSize' => 22, 'excerptLines' => 3, 'class' => ''])

<a href="{{ route('article.show', $article['slug']) }}" class="group {{ $imagePosition === 'left' ? 'flex gap-4' : 'flex flex-col' }} {{ $class }}">
    @if(!empty($article['image_url']))
        <div class="{{ $imagePosition === 'left' ? 'w-[55%] shrink-0' : 'w-full' }} {{ $imagePosition === 'left' ? 'aspect-[16/9]' : 'aspect-[16/9] mb-3' }} overflow-hidden">
            <img src="{{ $article['image_url'] }}" alt="{{ $article['title'] }}" loading="lazy"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
        </div>
    @endif
    <div class="{{ $imagePosition === 'left' ? 'flex-1 flex flex-col pt-1' : 'flex-1' }}">
        @if(!empty($article['category']))
            <span class="text-[#e2231a] font-bold text-[12px] uppercase mb-1 block">{{ $article['category'] }}</span>
        @endif
        <h2 class="font-serif font-bold text-[{{ $titleSize }}px] leading-[1.25] text-fg group-hover:text-[#e2231a] transition-colors {{ $imagePosition === 'left' ? 'mb-2' : 'mb-2' }}">
            {{ $article['title'] }}
        </h2>
        @if(!empty($article['excerpt']))
            <p class="text-fg-secondary text-[13px] leading-relaxed line-clamp-{{ $excerptLines }}">{{ $article['excerpt'] }}</p>
        @endif
        @if($showTime && !empty($article['time_ago']))
            <div class="text-[11px] text-fg-muted mt-2">{{ $article['time_ago'] }}</div>
        @endif
    </div>
</a>
