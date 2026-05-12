@props(['article'])

<article class="min-w-0 border-b border-border pb-6 md:pb-8">
  <a href="{{ route('article.show', $article['slug']) }}" class="group block min-w-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#e2231a] focus-visible:ring-offset-2 focus-visible:ring-offset-bg">
    <div class="overflow-hidden bg-surface aspect-[16/9] mb-4 md:mb-5">
      <img
        src="{{ $article['image_url'] ?? asset('images/news-1.jpg') }}"
        alt="{{ $article['title'] }}"
        loading="eager"
        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
      >
    </div>

    @if(!empty($article['category']))
      <span class="mb-2 inline-flex bg-[#e2231a] px-2.5 py-1 text-[12px] font-bold leading-none text-white">
        {{ $article['category'] }}
      </span>
    @endif

    <h2 class="break-words font-serif text-[26px] md:text-[34px] font-extrabold leading-tight text-fg group-hover:text-[#e2231a] transition-colors">
      {{ $article['title'] }}
    </h2>

    @if(!empty($article['excerpt']))
      <p class="mt-3 max-w-3xl break-words text-[15px] md:text-[16px] leading-relaxed text-fg-secondary line-clamp-2">
        {{ $article['excerpt'] }}
      </p>
    @endif

    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-[12px] text-fg-muted">
      @if(!empty($article['author']))
        <span>{{ $article['author'] }}</span>
      @endif
      <time>{{ $article['time_ago'] ?? $article['date'] ?? '' }}</time>
      @if(!empty($article['views']))
        <span>{{ number_format($article['views']) }} বার পড়া</span>
      @endif
    </div>
  </a>
</article>
