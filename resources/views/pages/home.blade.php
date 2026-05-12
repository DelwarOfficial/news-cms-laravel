@extends('layouts.app')

@section('title', 'ঢাকা ম্যাগাজিন - হোম')

@section('content')

  <x-home.hero-section
    :featured="$featured ?? null"
    :center-grid="$centerGrid ?? []"
    :left-col="$leftCol ?? []"
    :right-col="$rightCol ?? []"
  />


  {{-- ── ADVERTISEMENT BANNER ─────────────────────────────────── --}}
  <div class="w-full max-w-screen-xl mx-auto px-4 py-3">
    <x-home.ad-banner height="90px" />
  </div>

  {{-- 1. PHOTO NEWS (ফটো সংবাদ) --}}
  <x-photo-news-block 
    :carousel-articles="$photoNewsArticles" 
    :latest-articles="$photoNewsLatest" 
    :popular-articles="$photoNewsPopular" 
  />

  <div class="border-t-4 border-border"></div>

  <x-home.category-grid-section
    title="বাংলাদেশ"
    :posts="$bangladeshArticles ?? []"
    :moreUrl="route('category.parent', 'bangladesh')"
  />

  <div class="border-t-4 border-border"></div>

  <x-home.local-news-section
    :left-posts="$countryLeft ?? []"
    :hero-post="$countryHero ?? null"
    :right-posts="$countryRight ?? []"
    :divisions="$saradeshDivisions ?? []"
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ POLITICS / রাজনীতি ════════════════════════════════════════════ --}}
  <x-home.feature-list-section
    title="রাজনীতি"
    :posts="$opinionArticles ?? []"
    :moreUrl="route('category.child', ['bangladesh', 'politics'])"
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ INTERNATIONAL ════════════════════════════════════════ --}}
  <x-home.feature-list-section
    title="আন্তর্জাতিক"
    :posts="$internationalArticles ?? []"
    :moreUrl="route('category.parent', 'world')"
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ SPORTS (খেলাধুলা) — 3 panel ════════════════════════ --}}
  <x-sports-block 
    :sports-articles="$sportsArticles ?? []" 
    :primary-article="$sportsPrimary ?? null"
    :secondary-articles="$sportsSecondary ?? []"
    :sports-subcat-articles="$sportsSubcatArticles ?? []" 
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ মতামত ═════════════════════════════════════ --}}
  <x-home.opinion-section
    title="মতামত"
    :posts="$matamatArticles ?? []"
    :moreUrl="route('category.parent', 'opinion')"
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ VIDEO — dark bg, 1 large left + 3 small right ════════ --}}
  <x-video-block 
    :articles="$videoArticles ?? []"
    :video-featured="$videoFeatured ?? null" 
    :video-small="$videoSmall ?? null" 
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ ENTERTAINMENT (বিনোদন) ═══════════════════════════════ --}}
  <x-home.entertainment-section
    title="বিনোদন"
    :moreUrl="route('category.parent', 'entertainment')"
    :left-posts="$entertainmentLeft ?? []"
    :hero-post="$entertainmentHero ?? null"
    :right-posts="$entertainmentRight ?? []"
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ ECONOMY + LIFESTYLE + JOBS 3-Column ════════════════════════ --}}
  <x-home.compact-category-columns
    :columns="[
      ['title' => 'অর্থনীতি', 'moreUrl' => route('category.parent', 'economy'), 'posts' => $economyArticles ?? []],
      ['title' => 'লাইফস্টাইল', 'moreUrl' => route('category.parent', 'lifestyle'), 'posts' => $healthArticles ?? []],
      ['title' => 'চাকরি', 'moreUrl' => route('category.parent', 'jobs'), 'posts' => $jobArticles ?? []],
    ]"
  />

  <div class="border-t-4 border-border"></div>

  {{-- ══ ঢাকা ম্যাগাজিন স্পেশাল ════════════════════════════ --}}
  <x-home.special-strip-section
    title="ঢাকা ম্যাগাজিন স্পেশাল"
    :moreUrl="route('category.parent', 'dhaka-magazine-special')"
    :posts="$specialArticles ?? []"
  />
  <div class="border-t-4 border-border"></div>

  {{-- ══ BOTTOM 4 COLUMNS (ধর্ম, তথ্য-প্রযুক্তি, শিক্ষা, প্রবাস) ══════════════ --}}
  <x-home.bottom-category-columns
    :columns="[
      ['title' => 'ধর্ম', 'posts' => $religionArticles ?? []],
      ['title' => 'রাজধানী', 'posts' => $rajdhaniArticles ?? []],
      ['title' => 'শিক্ষা', 'posts' => $educationArticles ?? []],
      ['title' => 'প্রবাস', 'posts' => $probashArticles ?? []],
    ]"
  />
  {{-- ── FOOTER AD ────────────────────────────────────────────── --}}
  <div class="w-full max-w-screen-xl mx-auto px-4 pb-4">
    <x-home.ad-banner height="80px" :rounded="false" />
  </div>

@endsection
