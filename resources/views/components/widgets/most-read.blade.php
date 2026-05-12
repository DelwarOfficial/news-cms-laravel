@props(['articles' => []])

<aside class="w-full min-w-0">
  <div class="min-w-0 border-t-[3px] border-[#e2231a] bg-surface p-4 md:p-5">
    <h2 class="mb-4 font-serif text-[22px] font-extrabold leading-tight text-fg">সর্বাধিক পঠিত</h2>

    <div class="space-y-1">
      @forelse($articles as $index => $article)
        <a href="{{ route('article.show', $article['slug']) }}" class="group grid min-w-0 grid-cols-[28px_64px_minmax(0,1fr)] gap-3 border-b border-border py-3 last:border-0 sm:grid-cols-[34px_72px_minmax(0,1fr)] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#e2231a]">
          <span class="pt-1 font-serif text-[20px] font-extrabold leading-none text-[#e2231a] sm:text-[22px]">
            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
          </span>
          <div class="aspect-[4/3] overflow-hidden bg-bg">
            <img
              src="{{ $article['image_url'] ?? asset('images/news-1.jpg') }}"
              alt="{{ $article['title'] }}"
              loading="lazy"
              class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-[1.04]"
            >
          </div>
          <div class="min-w-0">
            <h3 class="break-words font-serif text-[14px] font-bold leading-snug text-fg transition-colors group-hover:text-[#e2231a] line-clamp-2">
              {{ $article['title'] }}
            </h3>
            <time class="mt-1 block text-[11px] text-fg-muted">{{ $article['time_ago'] ?? $article['date'] ?? '' }}</time>
          </div>
        </a>
      @empty
        <p class="py-4 text-[14px] text-fg-secondary">কোনো সংবাদ পাওয়া যায়নি।</p>
      @endforelse
    </div>
  </div>
</aside>
