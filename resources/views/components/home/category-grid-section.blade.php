@props([
    'title',
    'posts' => [],
    'moreUrl' => null,
    'columns' => 'grid-cols-2 md:grid-cols-4',
    'titleSize' => 15,
])

{{-- Category-fed section.
     Future CMS source: Category::posts() / post_category pivot, ordered by published_at. --}}
<section class="w-full max-w-screen-xl mx-auto px-4 py-5">
  <x-section-header :title="$title" :moreUrl="$moreUrl" />

  <div class="grid {{ $columns }} gap-5">
    @foreach($posts as $post)
      <x-cards.grid :article="$post" :titleSize="$titleSize" />
    @endforeach
  </div>
</section>
