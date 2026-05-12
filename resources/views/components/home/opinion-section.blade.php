@props([
    'title',
    'posts' => [],
    'moreUrl' => null,
])

<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <x-section-header :title="$title" :moreUrl="$moreUrl" />

  <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
    @foreach($posts as $post)
      <x-cards.grid :article="$post" :titleSize="15" />
    @endforeach
  </div>
</section>
