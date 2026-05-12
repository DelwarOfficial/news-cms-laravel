<footer class="w-full bg-[#1d2640] text-white mt-16 border-t-[3px] border-[#e2231a] font-serif">
  <div class="w-full max-w-screen-xl mx-auto px-4 pt-12 pb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">

      <div class="col-span-1">
        <a href="{{ route('home') }}" class="block mb-6 w-[180px] -ml-1 md:-ml-2" aria-label="Dhaka Magazine">
          <img src="{{ asset('images/dhaka-magazine-white-logo.svg') }}" class="w-full h-auto opacity-90 hover:opacity-100 transition-opacity" alt="Dhaka Magazine" />
        </a>
        <div class="flex items-center gap-3 mb-6 -ml-1 md:-ml-2">
          <a href="#" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[#1877F2] transition-colors hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#1877F2]" aria-label="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
          <a href="#" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white transition-colors hover:bg-white hover:text-[#111111] hover:border-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white" aria-label="X">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
          </a>
          <a href="#" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[#E4405F] transition-colors hover:bg-[#E4405F] hover:text-white hover:border-[#E4405F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#E4405F]" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
          </a>
          <a href="#" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[#FF0000] transition-colors hover:bg-[#FF0000] hover:text-white hover:border-[#FF0000] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF0000]" aria-label="YouTube">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
          </a>
        </div>
      </div>

      <div>
        <h3 class="text-[14px] font-bold mb-4 text-white uppercase">বিভাগসমূহ</h3>
        <ul class="grid grid-cols-2 gap-y-2 gap-x-4 text-fg-muted text-sm">
          @if(isset($siteCategories))
            @foreach($siteCategories as $cat)
              @continue(!is_array($cat))
              @continue(empty($cat['slug']) || empty($cat['name_bn']))
              <li><a href="{{ \App\Support\CategoryRepository::route($cat) }}" class="hover:text-white transition-colors">{{ $cat['name_bn'] }}</a></li>
            @endforeach
          @endif
        </ul>
      </div>

      <div>
        <h3 class="text-[14px] font-bold mb-4 text-white uppercase">অন্যান্য</h3>
        <ul class="space-y-2 text-fg-muted text-sm">
          <li><a href="#" class="hover:text-white transition-colors">আমাদের সম্পর্কে</a></li>
          <li><a href="#" class="hover:text-white transition-colors">যোগাযোগ</a></li>
          <li><a href="#" class="hover:text-white transition-colors">বিজ্ঞাপন</a></li>
        </ul>
      </div>

      <div>
        <h3 class="text-[14px] font-bold mb-4 text-white uppercase">নীতিমালা</h3>
        <ul class="space-y-2 text-fg-muted text-sm">
          <li><a href="#" class="hover:text-white transition-colors">গোপনীয়তা নীতি</a></li>
          <li><a href="#" class="hover:text-white transition-colors">ব্যবহারের শর্তাবলী</a></li>
        </ul>
      </div>
    </div>

    <div class="border-t border-border pt-6 text-fg-muted text-[12px] flex flex-col md:flex-row justify-between items-center">
      <p>&copy; {{ date('Y') }} ঢাকা ম্যাগাজিন। সর্বস্বত্ব সংরক্ষিত।</p>
      <p class="mt-2 md:mt-0">সম্পাদক ও প্রকাশক: <span class="text-fg-muted">আহমেদ চৌধুরী</span></p>
    </div>
  </div>
</footer>
