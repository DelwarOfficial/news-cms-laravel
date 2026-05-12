@props([
    'title',
    'moreUrl' => null,
    'posts' => [],
])

<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <x-section-header :title="$title" :moreUrl="$moreUrl" />

  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-0 divide-x divide-border">
    @foreach($posts as $index => $post)
      <a href="{{ route('article.show', $post['slug']) }}"
        class="group flex flex-col {{ $index === 0 ? 'pr-4' : ($index === 4 ? 'pl-4' : 'px-4') }}">
        <div class="w-full aspect-[16/9] overflow-hidden mb-3 rounded-sm">
          <img src="{{ $post['image_url'] }}" alt="{{ $post['title'] }}" loading="lazy"
            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
        </div>
        <h3 class="font-serif font-bold text-[15px] text-fg leading-tight group-hover:text-[#e2231a] transition-colors line-clamp-3 mb-1.5">
          {{ $post['title'] }}
        </h3>
        @if(!empty($post['excerpt']))
          <p class="text-fg-secondary text-[13px] leading-relaxed line-clamp-3">
            {{ $post['excerpt'] }}
          </p>
        @endif
      </a>
    @endforeach
  </div>
</section>
