@props([
    'leftPosts' => [],
    'heroPost' => null,
    'rightPosts' => [],
    'divisions' => [],
])

{{-- Location-fed section.
     Future CMS source: posts filtered by division_id -> district_id -> upazila_id. --}}
<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <x-section-header title="সারাদেশ" :moreUrl="route('category.parent', 'country-news')" />

  <x-location-news-filter
    :divisions="$divisions"
    selected-division=""
    selected-district=""
    selected-upazila=""
  />

  <div class="grid grid-cols-1 md:grid-cols-[1fr_2.2fr_1.3fr] gap-5 md:gap-0 md:divide-x divide-border">
    <div class="md:pr-5 flex flex-col justify-between gap-5">
      @foreach($leftPosts as $post)
        <a href="{{ route('article.show', $post['slug']) }}" class="group flex flex-col">
          <div class="w-full aspect-[16/9] overflow-hidden mb-2">
            <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] }}" loading="lazy"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
          </div>
          <h3 class="font-serif font-bold text-[15px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3">
            {{ $post['title'] }}
          </h3>
        </a>
      @endforeach
    </div>

    <div class="md:px-5">
      @if($heroPost)
        <a href="{{ route('article.show', $heroPost['slug']) }}" class="group flex flex-col">
          <div class="w-full aspect-[16/9] overflow-hidden mb-3">
            <img src="{{ $heroPost['image_url'] }}" alt="{{ $heroPost['title'] }}" loading="lazy"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
          </div>
          <h3 class="font-serif font-extrabold text-[21px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2 mb-2">
            {{ $heroPost['title'] }}
          </h3>
          @if(!empty($heroPost['excerpt']))
            <p class="text-fg-secondary text-[13px] leading-relaxed line-clamp-2">
              {{ $heroPost['excerpt'] }}
            </p>
          @endif
        </a>
      @endif
    </div>

    <div class="md:pl-5 flex flex-col divide-y divide-border">
      @foreach($rightPosts as $post)
        <a href="{{ route('article.show', $post['slug']) }}"
          class="group flex items-start gap-3 py-3 first:pt-0 last:pb-0">
          <h3 class="font-serif font-bold text-[14px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3 flex-1">
            {{ $post['title'] }}
          </h3>
          <div class="w-[68px] h-[38px] shrink-0 overflow-hidden">
            <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] }}" loading="lazy"
              class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
