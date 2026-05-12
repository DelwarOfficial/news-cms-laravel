@props([
    'title',
    'moreUrl' => null,
    'leftPosts' => [],
    'heroPost' => null,
    'rightPosts' => [],
])

<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <x-section-header :title="$title" :moreUrl="$moreUrl" />

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.5fr_1fr] gap-0 lg:divide-x divide-border bg-surface border border-border">
    <div class="flex flex-col p-4 pb-0">
      @foreach($leftPosts as $post)
        <x-home.entertainment-side-card :post="$post" />
      @endforeach
    </div>

    <div class="p-4 flex flex-col">
      @if($heroPost)
        <a href="{{ route('article.show', $heroPost['slug']) }}" class="group flex flex-col">
          <div class="w-full overflow-hidden mb-4 rounded-sm">
            <div class="aspect-[16/9] w-full">
              <img src="{{ $heroPost['image_url'] }}" alt="{{ $heroPost['title'] }}" loading="lazy"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]" />
            </div>
          </div>
          <h3 class="font-serif font-extrabold text-[24px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors mb-2.5">
            {{ $heroPost['title'] }}
          </h3>
          @if(!empty($heroPost['excerpt']))
            <p class="text-fg-secondary text-[15px] leading-relaxed line-clamp-3">
              {{ $heroPost['excerpt'] }}
            </p>
          @endif
        </a>
      @endif
    </div>

    <div class="flex flex-col p-4 pb-0">
      @foreach($rightPosts as $post)
        <x-home.entertainment-side-card :post="$post" />
      @endforeach
    </div>
  </div>
</section>
