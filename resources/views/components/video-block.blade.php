@props([
    'articles' => [],
    'videoFeatured' => null,
    'videoSmall' => [],
])

@php
    $sectionArticles = collect($articles)->values();
    $featuredVideo = $videoFeatured ?: $sectionArticles->first();
    $smallVideos = $sectionArticles->isNotEmpty() ? $sectionArticles->slice(1, 3)->values()->all() : $videoSmall;
@endphp

{{-- Category-fed video block.
     Future CMS source: Videos category posts; a placement can still pin the first item later. --}}

<div class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <x-section-header title="ভিডিও" :moreUrl="route('category.parent', 'videos')" />
  
  <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 lg:gap-8">
    
    {{-- Left: Large Featured Video (Now 60% of width) --}}
    @if($featuredVideo)
      <a href="{{ route('article.show', $featuredVideo['slug']) }}" class="group flex flex-col">
        <div class="w-full overflow-hidden mb-3 relative aspect-video rounded-sm">
          <img src="{{ $featuredVideo['image_url'] }}" alt="{{ $featuredVideo['title'] }}" loading="lazy"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="bg-[#e2231a] rounded-full flex items-center justify-center opacity-90 group-hover:opacity-100 transition-opacity w-14 h-14">
              <svg viewBox="0 0 24 24" fill="white" class="w-7 h-7 ml-1"><path d="M8 5v14l11-7z" /></svg>
            </div>
          </div>
        </div>
        <h3 class="font-serif font-bold text-[18px] md:text-[22px] text-fg leading-tight group-hover:text-[#e2231a] transition-colors line-clamp-2">
          {{ $featuredVideo['title'] }}
        </h3>
        <div class="text-[12px] text-fg-muted mt-1.5">{{ $featuredVideo['time_ago'] }}</div>
      </a>
    @endif

    {{-- Right: Small Video List (Now 40% of width) --}}
    @if($smallVideos && count($smallVideos) > 0)
      <div class="flex flex-col gap-4 border-t border-border pt-5 mt-2 lg:border-t-0 lg:pt-0 lg:mt-0 lg:border-l lg:pl-8">
        @foreach($smallVideos as $a)
          <a href="{{ route('article.show', $a['slug']) }}"
            class="group flex items-start gap-3">
            <div class="w-[140px] aspect-video shrink-0 overflow-hidden relative rounded-sm">
              <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
              <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-8 h-8 bg-[#e2231a] rounded-full flex items-center justify-center opacity-90">
                  <svg viewBox="0 0 24 24" fill="white" class="w-4 h-4 ml-0.5"><path d="M8 5v14l11-7z" /></svg>
                </div>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="font-serif font-bold text-[14px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3">
                {{ $a['title'] }}
              </h3>
              <div class="text-[11px] text-fg-muted mt-1.5">{{ $a['time_ago'] }}</div>
            </div>
          </a>
        @endforeach
      </div>
    @endif
    
  </div>
</div>
