@props(['columns' => []])

{{-- Category-fed footer columns.
     Future CMS source: explicit category collections, not placement logic. --}}
<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
    @foreach($columns as $column)
      @php($posts = collect($column['posts'] ?? $column['articles'] ?? [])->values())

      <div class="flex flex-col">
        @if($posts->isNotEmpty())
          @php($heroPost = $posts->first())

          <a href="{{ route('article.show', $heroPost['slug']) }}" class="group flex flex-col mb-3">
            <div class="relative w-full aspect-[16/9] overflow-hidden mb-3">
              <img src="{{ $heroPost['image_url'] }}" alt="{{ $heroPost['title'] }}" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
              <div class="absolute bottom-0 left-0 right-0 flex items-end">
                <span class="bg-[#e2231a] text-white text-[11px] font-bold px-2 py-0.5 z-10">{{ $column['title'] }}</span>
                <div class="flex-1 h-[2px] bg-[#e2231a] mb-0.5"></div>
              </div>
            </div>
            <h3 class="font-serif font-extrabold text-[17px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3">
              {{ $heroPost['title'] }}
            </h3>
          </a>

          <div class="flex flex-col">
            @foreach($posts->slice(1, 3) as $post)
              <a href="{{ route('article.show', $post['slug']) }}" class="group py-3 border-t border-border">
                <h3 class="font-serif font-bold text-[15px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3">
                  {{ $post['title'] }}
                </h3>
              </a>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>
</section>
