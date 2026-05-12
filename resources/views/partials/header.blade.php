<header class="w-full bg-bg flex flex-col font-serif dark:bg-surface" id="site-header">
  <div class="w-full max-w-screen-xl mx-auto px-4 py-3">
    <div class="flex items-center justify-between">
      <a href="{{ route('home') }}" class="logo-link" aria-label="Dhaka Magazine">
        <img src="{{ asset('images/dhaka-magazine-color-logo.svg') }}" class="logo logo-light h-10 md:h-12" alt="Dhaka Magazine" />
        <img src="{{ asset('images/dhaka-magazine-white-logo.svg') }}" class="logo logo-dark h-10 md:h-12" alt="Dhaka Magazine" />
      </a>

      <div class="flex items-center gap-3 md:gap-4">
        <div class="flex flex-col items-end">
          <span class="text-fg-secondary text-[11px] md:text-[13px] font-bengali">{{ \App\Helpers\DateHelper::getBengaliDate() }}</span>
          <a href="#" class="hidden md:flex items-center gap-1 text-fg-secondary hover:text-fg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            <span class="text-[12px]">ই-পেপার</span>
          </a>
        </div>

        <button class="flex items-center gap-1 text-fg-secondary hover:text-fg transition-colors" aria-label="Search">
          <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>
      </div>
    </div>
  </div>

  <div id="site-nav" class="w-full bg-[#1d2640] text-white sticky top-0 z-40">
    <div class="w-full max-w-screen-xl mx-auto px-4">
      <nav class="hidden md:flex items-center h-[48px] the-sticky-nav justify-between">
        <div class="flex items-center">
          <a href="{{ route('home') }}" class="nav-mini-logo">
            <img src="{{ asset('images/dhaka-magazine-white-logo.svg') }}" class="h-0 transition-all duration-300" alt="Dhaka Magazine">
          </a>

          <a href="{{ route('news.latest') }}" class="nav-item {{ request()->routeIs('news.latest') ? 'is-active' : '' }}">সর্বশেষ</a>

          @foreach(($siteCategories ?? collect()) as $cat)
            @continue(!is_array($cat))
            @continue(empty($cat['slug']) || empty($cat['name_bn']))
            @if(!empty($cat['children']))
              <div class="nav-dropdown">
                <a href="{{ \App\Support\CategoryRepository::route($cat) }}" class="nav-item flex items-center gap-1">
                  {{ $cat['name_bn'] }}
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
                <div class="nav-dropdown-menu">
                  @foreach($cat['children'] as $child)
                    @continue(!is_array($child) || empty($child['slug']) || empty($child['name_bn']))
                    <a href="{{ \App\Support\CategoryRepository::route($child) }}" class="nav-dropdown-item">{{ $child['name_bn'] }}</a>
                  @endforeach
                </div>
              </div>
            @else
              <a href="{{ \App\Support\CategoryRepository::route($cat) }}" class="nav-item">{{ $cat['name_bn'] }}</a>
            @endif
          @endforeach
        </div>

        <a href="#" class="nav-search-link flex items-center gap-1 text-white hover:text-red-400 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          <span class="text-[13px]">খুঁজুন</span>
        </a>

        <a href="#" class="nav-item flex items-center gap-1 text-white hover:text-red-400">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          <span class="text-[13px]">Eng</span>
        </a>

        <button id="theme-toggle-nav" class="flex items-center gap-1 text-white hover:text-red-400 transition-colors" aria-label="Toggle theme">
          <svg id="theme-icon-sun-nav" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
          <svg id="theme-icon-moon-nav" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
      </nav>

      <div class="md:hidden flex items-center justify-between h-[48px] w-full gap-2 overflow-hidden">
        <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
          <img src="{{ asset('images/dhaka-magazine-white-logo.svg') }}" class="h-6" alt="Dhaka Magazine">
        </a>

        <div class="mobile-nav-scroll-wrapper flex-1" style="overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
          <div style="display:flex;align-items:center;gap:0;white-space:nowrap;padding-bottom:0px;">
            <a href="{{ route('news.latest') }}" class="mobile-scroll-nav-item {{ request()->routeIs('news.latest') ? 'is-active' : '' }}">সর্বশেষ</a>
            @foreach(($siteCategories ?? collect()) as $cat)
              @continue(!is_array($cat))
              @continue(empty($cat['slug']) || empty($cat['name_bn']))
              <a href="{{ \App\Support\CategoryRepository::route($cat) }}" class="mobile-scroll-nav-item">{{ $cat['name_bn'] }}</a>
            @endforeach
          </div>
        </div>

        <button id="hamburger-btn-sticky" class="flex-shrink-0 p-1 text-white hover:text-red-400 transition-colors" aria-label="Toggle menu">
          <svg id="icon-menu-sticky" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
          <svg id="icon-close-sticky" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="hidden"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
      </div>
    </div>
  </div>

  <div id="mobile-menu" class="hidden fixed inset-0 top-[48px] bg-bg dark:bg-surface z-50 md:hidden">
    <nav class="flex flex-col p-4 overflow-y-auto h-full">
      <div class="mb-4">
        <input type="text" placeholder="খুঁজুন..." class="w-full px-4 py-2 border border-border rounded-lg bg-bg text-fg">
      </div>

      <a href="{{ route('news.latest') }}" class="mobile-nav-item {{ request()->routeIs('news.latest') ? 'is-active' : '' }}">সর্বশেষ</a>

      @foreach(($siteCategories ?? collect()) as $cat)
        @continue(!is_array($cat))
        @continue(empty($cat['slug']) || empty($cat['name_bn']))
        @if(!empty($cat['children']))
          <div class="mobile-accordion">
            <button class="mobile-accordion-btn">
              <span>{{ $cat['name_bn'] }}</span>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="mobile-submenu hidden">
              <a href="{{ \App\Support\CategoryRepository::route($cat) }}" class="mobile-submenu-item">{{ $cat['name_bn'] }}</a>
              @foreach($cat['children'] as $child)
                @continue(!is_array($child) || empty($child['slug']) || empty($child['name_bn']))
                <a href="{{ \App\Support\CategoryRepository::route($child) }}" class="mobile-submenu-item">{{ $child['name_bn'] }}</a>
              @endforeach
            </div>
          </div>
        @else
          <a href="{{ \App\Support\CategoryRepository::route($cat) }}" class="mobile-nav-item">{{ $cat['name_bn'] }}</a>
        @endif
      @endforeach

      <div class="mt-4 pt-4 border-t border-border flex gap-2">
        <button onclick="toggleTheme()" class="flex-1 py-2 px-4 bg-primary text-white rounded-lg flex items-center justify-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
          Dark Mode
        </button>
        <button class="flex-1 py-2 px-4 border border-border rounded-lg flex items-center justify-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
          ই-পেপার
        </button>
      </div>
    </nav>
  </div>
</header>
