@extends('layouts.app')

@section('title', $metaTitle ?? ($categoryName . ' সংবাদ | Dhaka Magazine'))
@section('meta_description', $metaDescription ?? '')

@section('content')
  @php
    // UI adapter for future normalized Category -> posts relationship.
    // Controllers can later pass $posts directly without changing this layout.
    $sectionPosts = collect($posts ?? $categoryArticles ?? [])->values();
  @endphp

  <main class="w-full max-w-screen-xl mx-auto px-4 py-5 md:py-6">
    @if(!empty($breadcrumbs))
      <nav class="mb-4 text-[13px] text-fg-secondary" aria-label="Breadcrumb">
        @foreach($breadcrumbs as $index => $crumb)
          @if($index > 0)
            <span class="mx-2">/</span>
          @endif
          @if($index < count($breadcrumbs) - 1)
            <a href="{{ $crumb['url'] }}" class="hover:text-[#e2231a] transition-colors">{{ $crumb['title'] }}</a>
          @else
            <span class="text-fg">{{ $crumb['title'] }}</span>
          @endif
        @endforeach
      </nav>
    @endif

    <header class="mb-6 border-b border-border pb-4 md:mb-8">
      <p class="mb-1 text-[13px] font-bold text-[#e2231a]">বিভাগ</p>
      <h1 class="font-serif text-[30px] font-extrabold leading-tight text-fg md:text-[42px]">
        {{ $categoryName }}
      </h1>
    </header>

    @if($category['slug'] === 'country-news')
      <div class="mb-6 md:mb-8">
        <x-location-news-filter
          :divisions="$divisions ?? []"
          :selected-division="$division ?? ''"
          :selected-district="$district ?? ''"
          :selected-upazila="$upazila ?? ''"
        />
      </div>
    @endif

    <x-ads.ad-slot name="category-top" size="970x90" class="mb-6 md:mb-8" />

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-8">
      <section class="min-w-0 lg:col-span-8">
        @if($sectionPosts->isNotEmpty())
          @php
            $heroArticle = $sectionPosts->first();
            $gridArticles = $sectionPosts->slice(1)->values();
          @endphp

          <x-news.hero-card :article="$heroArticle" />

          @if($gridArticles->isNotEmpty())
            <div class="mt-6 grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
              @foreach($gridArticles as $article)
                <x-news.news-card :article="$article" />
                @if($loop->iteration % 6 === 0 && ! $loop->last)
                  <x-ads.ad-slot name="category-in-feed" size="336x280" class="md:col-span-2 xl:col-span-3" />
                @endif
              @endforeach
            </div>
          @else
            <div class="mt-6 bg-surface px-4 py-10 text-center text-[15px] text-fg-secondary">
              এই বিভাগে আর কোনো সংবাদ নেই।
            </div>
          @endif
        @else
          <div class="bg-surface border border-border px-4 py-14 text-center">
            <h2 class="text-[20px] font-bold text-fg">এই বিভাগে এখনো কোনো সংবাদ প্রকাশিত হয়নি।</h2>
          </div>
        @endif

        <x-ads.ad-slot name="category-bottom" size="728x90" class="mt-8" />
      </section>

      <aside class="min-w-0 lg:col-span-4">
        <div class="space-y-6 lg:sticky lg:top-20">
          <x-widgets.most-read :articles="$popularNews ?? []" />
          <x-ads.ad-slot name="sidebar-rectangle-1" size="300x250" />
          <x-ads.ad-slot name="sidebar-half-page" size="300x600" />
          <x-ads.ad-slot name="sidebar-rectangle-2" size="300x250" />
        </div>
      </aside>
    </div>
  </main>
@endsection
