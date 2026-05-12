@props(['post'])

<a href="{{ route('article.show', $post['slug']) }}"
  class="group flex items-start gap-3 py-4 border-b border-border first:pt-0 last:border-b-0 last:pb-4">
  <div class="flex-1 min-w-0">
    <h3 class="font-serif font-bold text-[15px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-2">
      {{ $post['title'] }}
    </h3>
    @if(!empty($post['excerpt']))
      <p class="text-fg-secondary text-[13px] line-clamp-2 mt-1.5 leading-relaxed">{{ $post['excerpt'] }}</p>
    @endif
  </div>
  <div class="w-[90px] aspect-square shrink-0 overflow-hidden rounded-sm">
    <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] }}" loading="lazy"
      class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
  </div>
</a>
