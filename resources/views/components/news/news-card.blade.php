@props(['article'])

<article class="group h-full min-w-0">
  <a href="{{ route('article.show', $article['slug']) }}" class="flex h-full min-w-0 flex-col overflow-hidden rounded-[8px] border border-border bg-bg shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#e2231a] focus-visible:ring-offset-2 focus-visible:ring-offset-bg">
    <div class="relative aspect-[16/10] overflow-hidden bg-surface">
      <img
        src="{{ $article['image_url'] ?? asset('images/news-1.jpg') }}"
        alt="{{ $article['title'] }}"
        loading="lazy"
        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.04]"
      >
      @if(!empty($article['category']))
        <span class="absolute left-3 top-3 bg-[#e2231a] px-2 py-1 text-[11px] font-bold leading-none text-white shadow-sm">
          {{ $article['category'] }}
        </span>
      @endif
    </div>

    <div class="flex min-w-0 flex-1 flex-col p-4">
      <h3 class="break-words font-serif text-[18px] font-bold leading-snug text-fg transition-colors group-hover:text-[#e2231a] line-clamp-2">
        {{ $article['title'] }}
      </h3>

      @if(!empty($article['excerpt']))
        <p class="mt-2 break-words text-[14px] leading-relaxed text-fg-secondary line-clamp-2">
          {{ $article['excerpt'] }}
        </p>
      @endif

      <div class="mt-auto flex flex-wrap items-center gap-x-2 gap-y-1 pt-3 text-[12px] text-fg-muted">
        <time>{{ $article['time_ago'] ?? $article['date'] ?? '' }}</time>
        @if(!empty($article['views']))
          <span>{{ number_format($article['views']) }} বার পড়া</span>
        @endif
      </div>
    </div>
  </a>
</article>
