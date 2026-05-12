@props(['articles' => []])

<div class="border-t border-border pt-4">
  <h3 class="text-[20px] font-bold font-serif mb-4 text-fg">
    সর্বাধিক পঠিত
  </h3>

  <div class="flex flex-col">
    @foreach($articles as $index => $article)
      <a href="{{ route('article.show', $article['slug']) }}" class="group flex items-start space-x-3 py-3 border-b border-border last:border-0">
        <span class="text-[24px] font-serif font-bold text-[#e2231a] leading-none mt-1">
          {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
        </span>
        <div>
          <h4 class="font-bold text-[16px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors font-serif">
            {{ $article['title'] }}
          </h4>
        </div>
      </a>
    @endforeach
  </div>
</div>
