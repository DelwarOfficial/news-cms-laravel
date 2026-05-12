@props([
    'featured' => null,
    'centerGrid' => [],
    'leftCol' => [],
    'rightCol' => [],
])

<div class="w-full max-w-screen-xl mx-auto px-4 hero-section">
  <div class="grid grid-cols-1 md:hidden gap-3 border-t border-border">
    @if($featured)
      <a href="{{ route('article.show', $featured['slug']) }}" class="group flex flex-col py-4 border-b border-border">
        <div class="w-full aspect-[16/9] overflow-hidden rounded-sm mb-2">
          <img src="{{ $featured['image_url'] }}" alt="{{ $featured['title'] }}" loading="lazy"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]" />
        </div>
        <h2 class="font-serif font-bold text-[16px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
          {{ $featured['title'] }}
        </h2>
        <p class="text-fg-secondary text-[12px] line-clamp-2 mt-1 min-h-[2em]">{{ $featured['excerpt'] }}</p>
      </a>
    @endif

    @if($centerGrid)
      <div class="grid grid-cols-3 gap-3 py-2 border-b border-border">
        @foreach($centerGrid as $a)
          <a href="{{ route('article.show', $a['slug']) }}" class="group flex flex-col">
            @if(!empty($a['image_url']))
              <div class="w-full aspect-[4/3] overflow-hidden rounded-sm mb-2">
                <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]" />
              </div>
            @endif
            <span class="text-[#e2231a] font-bold text-[11px] block mb-0.5">{{ $a['category'] }}</span>
            <h3 class="font-serif font-bold text-[13px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
              {{ $a['title'] }}
            </h3>
          </a>
        @endforeach
      </div>
    @endif

    @if($leftCol)
      <div class="py-2">
        @foreach($leftCol as $a)
          <a href="{{ route('article.show', $a['slug']) }}" class="group flex items-start gap-3 py-3 border-b border-border last:border-b-0">
            <div class="flex-1 min-w-0">
              <span class="text-[#e2231a] font-bold text-[12px] block mb-0.5">{{ $a['category'] }}</span>
              <h3 class="font-serif font-bold text-[14px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
                {{ $a['title'] }}
              </h3>
            </div>
            @if(!empty($a['image_url']))
              <div class="w-[120px] h-[68px] shrink-0 overflow-hidden rounded-sm">
                <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]" />
              </div>
            @endif
          </a>
        @endforeach
      </div>
    @endif

    @if($rightCol)
      <div class="py-2">
        @foreach($rightCol as $a)
          <a href="{{ route('article.show', $a['slug']) }}" class="group flex items-start gap-3 py-3 border-b border-border last:border-b-0">
            @if(!empty($a['image_url']))
              <div class="w-[120px] h-[68px] shrink-0 overflow-hidden rounded-sm">
                <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]" />
              </div>
            @endif
            <div class="flex-1 min-w-0">
              <span class="text-[#e2231a] font-bold text-[12px] block mb-0.5">{{ $a['category'] }}</span>
              <h3 class="font-serif font-bold text-[14px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
                {{ $a['title'] }}
              </h3>
            </div>
          </a>
        @endforeach
      </div>
    @endif
  </div>

  <div class="hidden md:grid grid-cols-1 lg:grid-cols-[27%_46%_27%] hero-grid border-t border-border">
    <div class="py-4 pr-0 lg:pr-5 lg:border-r border-border order-2 lg:order-1">
      @foreach($leftCol as $a)
        <a href="{{ route('article.show', $a['slug']) }}"
          class="group flex flex-col-reverse md:flex-row items-start gap-3 py-3 border-b border-border last:border-b-0">
          <div class="flex-1 min-w-0">
            <span class="text-[#e2231a] font-bold text-[12px] block mb-0.5">{{ $a['category'] }} &bull;</span>
            <h3 class="font-serif font-bold text-[15px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
              {{ $a['title'] }}
            </h3>
            @if(!empty($a['excerpt']))
              <p class="text-fg-secondary text-[12px] line-clamp-2 mt-1">{{ $a['excerpt'] }}</p>
            @endif
            <div class="text-[11px] text-fg-muted mt-1">{{ $a['time_ago'] }}</div>
          </div>
          @if(!empty($a['image_url']))
            <div class="w-full md:w-[88px] h-[140px] md:h-[50px] shrink-0 overflow-hidden rounded-sm mb-2 md:mb-0">
              <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]" />
            </div>
          @endif
        </a>
      @endforeach
    </div>

    <div class="py-4 px-0 lg:px-5 lg:border-r border-border order-1 lg:order-2">
      @if($featured)
        <a href="{{ route('article.show', $featured['slug']) }}" class="group flex flex-col mb-3 pb-3 border-b border-border">
          <div class="w-full aspect-[16/9] overflow-hidden rounded-sm mb-2">
            <img src="{{ $featured['image_url'] }}" alt="{{ $featured['title'] }}" loading="lazy"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]" />
          </div>
          <h2 class="font-serif font-bold text-[22px] leading-[1.25] text-fg group-hover:text-[#e2231a] transition-colors mb-2">
            {{ $featured['title'] }}
          </h2>
          @if(!empty($featured['excerpt']))
            <p class="text-fg-secondary text-[13px] line-clamp-2 leading-relaxed min-h-[2.5em]">{{ $featured['excerpt'] }}</p>
          @endif
        </a>
      @endif

      @if($centerGrid)
        <div class="grid grid-cols-3 gap-x-4 gap-y-4">
          @foreach($centerGrid as $a)
            <a href="{{ route('article.show', $a['slug']) }}" class="group flex flex-col">
              <div class="w-full aspect-[16/9] overflow-hidden mb-1.5">
                <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
              </div>
              <h3 class="font-serif font-bold text-[13px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
                {{ $a['title'] }}
              </h3>
              <div class="text-[11px] text-fg-muted mt-0.5">{{ $a['time_ago'] }}</div>
            </a>
          @endforeach
        </div>
      @endif
    </div>

    <div class="py-4 pl-0 lg:pl-5 order-3 lg:order-3">
      <div class="w-full h-[250px] mb-4 overflow-hidden rounded-lg">
        <img src="{{ asset('images/coming-soon-ad.webp') }}" alt="Advertisement" class="w-full h-full object-cover" />
      </div>

      @foreach($rightCol as $a)
        <a href="{{ route('article.show', $a['slug']) }}"
          class="group flex flex-col-reverse md:flex-row items-start gap-3 py-3 border-b border-border last:border-b-0">
          @if(!empty($a['image_url']))
            <div class="w-full md:w-[88px] h-[140px] md:h-[50px] shrink-0 overflow-hidden rounded-sm mb-2 md:mb-0">
              <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]" />
            </div>
          @endif
          <div class="flex-1 min-w-0">
            <span class="text-[#e2231a] font-bold text-[12px] block mb-0.5">{{ $a['category'] }} &bull;</span>
            <h3 class="font-serif font-bold text-[15px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
              {{ $a['title'] }}
            </h3>
            @if(!empty($a['excerpt']))
              <p class="text-fg-secondary text-[12px] line-clamp-2 mt-1">{{ $a['excerpt'] }}</p>
            @endif
            <div class="text-[11px] text-fg-muted mt-1">{{ $a['time_ago'] }}</div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</div>
