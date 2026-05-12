@props(['sportsArticles' => [], 'primaryArticle' => null, 'secondaryArticles' => [], 'sportsSubcatArticles' => []])

@php
  $primaryArticle = $primaryArticle ?? ($sportsArticles[0] ?? null);
  $secondaryArticles = $secondaryArticles ?: collect($sportsArticles)->skip(1)->take(2)->values()->all();
@endphp

{{-- Category-fed sports block.
     Future CMS source: Sports category plus child-category collections. --}}

<div class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6 lg:gap-8 lg:divide-x divide-border">

    {{-- PRIMARY COLUMN: Sports Component --}}
    <div class="lg:pr-4">
      <x-section-header title="খেলা" :moreUrl="route('category.parent', 'sports')" />
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 mt-4">
        {{-- Left: Hero Sports --}}
        @if($primaryArticle)
          <div>
            <a href="{{ route('article.show', $primaryArticle['slug']) }}" class="group block mb-4">
              <div class="relative w-full aspect-[16/9] overflow-hidden rounded-sm">
                <img src="{{ $primaryArticle['image_url'] }}" alt="{{ $primaryArticle['title'] }}" loading="lazy"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 px-4 py-3">
                  <h3 class="font-serif font-extrabold text-[20px] md:text-[22px] text-white leading-tight group-hover:text-[#e2231a] transition-colors line-clamp-2 drop-shadow">
                    {{ $primaryArticle['title'] }}
                  </h3>
                  <div class="text-[12px] text-white/70 mt-1.5">{{ $primaryArticle['time_ago'] }}</div>
                </div>
              </div>
            </a>

            <div class="grid grid-cols-2 gap-4">
              @foreach($secondaryArticles as $i => $a)
                <a href="{{ route('article.show', $a['slug']) }}" class="group flex flex-col">
                  <div class="relative w-full aspect-[16/9] overflow-hidden rounded-sm mb-2">
                    <img src="{{ $a['image_url'] }}" alt="{{ $a['title'] }}" loading="lazy"
                      class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
                  </div>
                  <h3 class="font-serif font-extrabold text-[13px] text-fg leading-tight group-hover:text-[#e2231a] transition-colors line-clamp-2">
                    {{ $a['title'] }}
                  </h3>
                </a>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Right: Sports Subcat --}}
        <div>
          @if(isset($sportsSubcatArticles))
            @foreach($sportsSubcatArticles as $item)
              @continue(empty($item['article']))
              <a href="{{ route('article.show', $item['article']['slug']) }}"
                class="group flex items-start gap-3 py-3 border-b border-border last:border-b-0 first:pt-0">
                <div class="flex-1 min-w-0">
                  <span class="text-[#e2231a] font-bold text-[12px]">{{ $item['subcat'] }} &bull;</span>
                  <h3 class="font-serif font-bold text-[15px] text-fg leading-tight group-hover:text-[#e2231a] transition-colors line-clamp-2 mt-0.5">
                    {{ $item['article']['title'] }}
                  </h3>
                  <div class="text-[11px] text-fg-muted mt-1.5">{{ $item['article']['time_ago'] }}</div>
                </div>
                <div class="w-[100px] aspect-video shrink-0 overflow-hidden rounded-sm">
                  <img src="{{ $item['article']['image_url'] }}" alt="{{ $item['article']['title'] }}" loading="lazy"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
                </div>
              </a>
            @endforeach
          @endif
        </div>
      </div>
    </div>

    {{-- SECONDARY COLUMN: Sidebar & Prayer Times --}}
    <div class="lg:pl-4 pt-6 lg:pt-0 border-t lg:border-t-0 border-border">
      <div class="w-full bg-surface border border-border flex items-center justify-center h-[80px] rounded-sm mb-6">
        <span class="text-fg-muted text-[12px] tracking-widest uppercase">বিজ্ঞাপন</span>
      </div>

      {{-- Namaz Timing Widget (Custom Layout) --}}
      <div class="border border-border rounded-sm bg-surface shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="py-3 text-center border-b border-border">
          <h3 class="font-serif font-extrabold text-[20px] text-fg">নামাজের সময়সূচি</h3>
        </div>

        {{-- Sub-header (Date & Sun times) --}}
        <div class="bg-[#1d2640] text-white p-3 flex justify-between items-center">
          <div class="font-serif font-bold">
            <div class="text-white/80 text-[14px]">২১ জিলকদ ১৪৪৬</div>
            <div class="text-white text-[14px] mt-0.5">শুক্রবার - ০৮ মে</div>
          </div>
          <div class="flex gap-4 text-center font-serif font-bold">
            <div>
              <div class="text-white/80 text-[13px]">সূর্যোদয়</div>
              <div class="text-white text-[14px] mt-0.5">৫:২০</div>
            </div>
            <div>
              <div class="text-white/80 text-[13px]">সূর্যাস্ত</div>
              <div class="text-white text-[14px] mt-0.5">৬:৩০</div>
            </div>
          </div>
        </div>

        {{-- Main Body --}}
        <div class="p-4 grid grid-cols-[1fr_1.2fr] gap-4 items-center">
          
          {{-- Left: Current Prayer & Countdown --}}
          <div class="text-center border-r border-border pr-4">
            <div id="upcoming-prayer-name" class="font-serif font-extrabold text-[28px] text-fg leading-none tracking-tight">লোড হচ্ছে...</div>
            <div class="text-[12px] text-fg-muted mt-2">ওয়াক্তের সময় বাকি</div>
            <div id="upcoming-prayer-countdown" class="font-serif font-bold text-[16px] text-fg mt-1">-- ঘণ্টা -- মিনিট</div>
            
            <div class="mt-4 text-[14px] text-fg-secondary font-bold space-y-1 font-serif">
              <div>সেহরি শেষ ৩:৫২</div>
              <div>ইফতার শুরু ৬:৩০</div>
            </div>
          </div>

          {{-- Right: Prayer List --}}
          <div class="space-y-3 font-serif">
            <div class="flex justify-between items-center text-[15px]">
              <span class="text-fg-secondary font-bold">ফজর</span>
              <span class="text-[#e2231a] font-extrabold tracking-wide">৩:৫৮ - <span class="text-fg">৫:১৫</span></span>
            </div>
            <div class="flex justify-between items-center text-[15px]">
              <span class="text-fg-secondary font-bold">জোহর</span>
              <span class="text-[#e2231a] font-extrabold tracking-wide">১১:৫৫ - <span class="text-fg">৪:২২</span></span>
            </div>
            <div class="flex justify-between items-center text-[15px]">
              <span class="text-fg-secondary font-bold">আছর</span>
              <span class="text-[#e2231a] font-extrabold tracking-wide">৪:৩২ - <span class="text-fg">৬:২৫</span></span>
            </div>
            <div class="flex justify-between items-center text-[15px]">
              <span class="text-fg-secondary font-bold">মাগরিব</span>
              <span class="text-[#e2231a] font-extrabold tracking-wide">৬:৩০ - <span class="text-fg">৭:৪৭</span></span>
            </div>
            <div class="flex justify-between items-center text-[15px]">
              <span class="text-fg-secondary font-bold">ইশা</span>
              <span class="text-[#e2231a] font-extrabold tracking-wide">৭:৫২ - <span class="text-fg">৩:৫৩</span></span>
            </div>
          </div>

        </div>

        {{-- Footer Note --}}
        <div class="py-2.5 text-center border-t border-border">
          <span class="text-[12px] font-serif text-fg-muted">*স্থানভেদে সময়ের পার্থক্য হতে পারে</span>
        </div>
      </div>

    </div>

  </div>
</div>
