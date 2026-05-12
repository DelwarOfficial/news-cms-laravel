@props(['columns' => []])

{{-- Category-fed compact columns.
     Future CMS source: each item should be a named Category with a posts collection. --}}
<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-0 lg:divide-x divide-border">
    @foreach($columns as $index => $column)
      @php
        $posts = collect($column['posts'] ?? $column['articles'] ?? [])->values();
        $padding = $index === 0 ? 'lg:pr-6' : ($index === 1 ? 'lg:px-6' : 'lg:pl-6 md:col-span-2 lg:col-span-1 mt-2 md:mt-0');
      @endphp

      <div class="{{ $padding }}">
        <x-section-header :title="$column['title']" :moreUrl="$column['moreUrl'] ?? null" />

        <div class="{{ $index === 2 ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-0 md:gap-6 lg:gap-0' : '' }}">
          @foreach($posts as $post)
            <a href="{{ route('article.show', $post['slug']) }}"
              class="group flex items-start gap-3 py-3 border-b border-border last:border-b-0">
              <div class="w-[130px] h-[73px] shrink-0 overflow-hidden rounded-sm">
                <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] }}" loading="lazy"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
              </div>
              <div class="flex-1 min-w-0">
                @if(!empty($post['category']))
                  <span class="text-[#e2231a] font-bold text-[12px]">{{ $post['category'] }} &bull;</span>
                @endif
                <h3 class="font-serif font-bold text-[16px] text-fg leading-tight group-hover:text-[#e2231a] transition-colors line-clamp-2 mt-0.5">
                  {{ $post['title'] }}
                </h3>
                @if(!empty($post['excerpt']))
                  <p class="text-fg-secondary text-[13px] line-clamp-2 mt-1 leading-relaxed">{{ $post['excerpt'] }}</p>
                @endif
                @if(!empty($post['time_ago']))
                  <div class="text-[11px] text-fg-muted mt-1">{{ $post['time_ago'] }}</div>
                @endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</section>
