@extends('layouts.app')

@section('title', $metaTitle)
@section('meta_description', $metaDescription)

@section('content')
  <main class="w-full max-w-screen-xl mx-auto px-4 py-5 md:py-6">
    <nav class="mb-4 text-[13px] text-fg-secondary" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-[#e2231a] transition-colors">হোম</a>
      <span class="mx-2">/</span>
      <span class="text-fg">সর্বশেষ</span>
    </nav>

    <header class="mb-6 border-b border-border pb-4 md:mb-8">
      <p class="mb-1 text-[13px] font-bold text-[#e2231a]">সর্বশেষ আপডেট</p>
      <h1 class="font-serif text-[30px] font-extrabold leading-tight text-fg md:text-[42px]">সর্বশেষ সংবাদ</h1>
      <p class="mt-2 max-w-2xl text-[15px] leading-relaxed text-fg-secondary md:text-[16px]">
        বাংলাদেশ ও বিশ্বের গুরুত্বপূর্ণ খবর এক জায়গায়, সময়ের ক্রমানুসারে সাজানো।
      </p>
    </header>

    <x-ads.ad-slot name="category-top" size="970x90" class="mb-6 md:mb-8" />

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-8">
      <section class="min-w-0 lg:col-span-8">
        @if($posts->count() > 0)
          @if($topStory)
            <x-news.hero-card :article="$topStory" />
          @endif

          <div class="mt-6 grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @php $displayedNewsCount = 0; @endphp
            @foreach($posts as $article)
              @if(! $topStory || $loop->index > 0 || $posts->currentPage() > 1)
                @php $displayedNewsCount++; @endphp
                <x-news.news-card :article="$article" />
                @if($displayedNewsCount % 6 === 0 && ! $loop->last)
                  <x-ads.ad-slot name="category-in-feed" size="336x280" class="md:col-span-2 xl:col-span-3" />
                @endif
              @endif
            @endforeach
          </div>

          <x-ads.ad-slot name="category-bottom" size="728x90" class="mt-8" />

          <div class="mt-8 border-t border-border pt-6">
            {{ $posts->links() }}
          </div>
        @else
          <div class="bg-surface border border-border px-4 py-14 text-center">
            <h2 class="text-[20px] font-bold text-fg">এখনও কোনো সংবাদ প্রকাশিত হয়নি।</h2>
            <p class="mt-2 text-[14px] text-fg-secondary">নতুন সংবাদ প্রকাশিত হলে এখানে দেখা যাবে।</p>
          </div>
        @endif
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
