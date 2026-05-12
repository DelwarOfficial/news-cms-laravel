@props([
    'title',
    'posts' => [],
    'moreUrl' => null,
    'showAd' => true,
])

@php
    $sectionPosts = collect($posts)->values();
    $featuredPost = $sectionPosts->first();
    $listPosts = $sectionPosts->slice(1, 4)->values();
@endphp

{{-- Category-fed feature/list section.
     Future CMS source: $category->posts()->published()->latest('published_at'). --}}
<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <x-section-header :title="$title" :moreUrl="$moreUrl" />

  @if($featuredPost)
    <div class="grid grid-cols-1 md:grid-cols-[2fr_2fr_1fr] gap-0 divide-x divide-border">
      <div class="pr-6">
        <a href="{{ route('article.show', $featuredPost['slug']) }}" class="group block">
          <div class="w-full aspect-[16/9] overflow-hidden mb-3">
            <img src="{{ $featuredPost['image_url'] }}" alt="{{ $featuredPost['title'] }}" loading="lazy"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
          </div>
          <h3 class="font-serif font-extrabold text-[20px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors mb-2 line-clamp-3">
            {{ $featuredPost['title'] }}
          </h3>
          @if(!empty($featuredPost['excerpt']))
            <p class="text-fg-secondary text-[13px] leading-relaxed line-clamp-3">{{ $featuredPost['excerpt'] }}</p>
          @endif
          @if(!empty($featuredPost['time_ago']))
            <div class="text-[11px] text-fg-muted mt-2">{{ $featuredPost['time_ago'] }}</div>
          @endif
        </a>
      </div>

      <div class="px-6 flex flex-col divide-y divide-border">
        @foreach($listPosts as $post)
          <a href="{{ route('article.show', $post['slug']) }}"
            class="group flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
            <h3 class="font-serif font-extrabold text-[15px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3 flex-1">
              {{ $post['title'] }}
            </h3>
            <div class="w-[72px] h-[40px] shrink-0 overflow-hidden">
              <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] }}" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
            </div>
          </a>
        @endforeach
      </div>

      @if($showAd)
        <div class="pl-6 flex flex-col items-center">
          <div class="ad-container bg-surface border border-border flex flex-col items-center justify-center relative">
            <span class="text-[#e2231a] text-[11px] font-bold tracking-wide mb-1 absolute top-2 right-2 z-10">বিজ্ঞাপন</span>
            <img src="{{ asset('images/coming-soon-ad.webp') }}" alt="Advertisement" class="w-full h-full object-cover opacity-50" />
          </div>
        </div>
      @endif
    </div>
  @endif
</section>
