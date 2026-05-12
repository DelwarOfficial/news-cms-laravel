<aside class="w-full space-y-8">

  <x-widgets.sidebar-most-read :articles="$popularNews ?? []" />

  <div class="bg-surface p-6 border-t border-[#e2231a] pt-6 border-t-[3px]">
    <h3 class="text-lg font-bold text-center mb-2 font-serif text-fg">নিউজলেটার</h3>
    <p class="text-[13px] text-center text-fg-secondary mb-4">
      প্রতিদিনের বাছাই করা খবর পেতে সাবস্ক্রাইব করুন
    </p>
    <form class="flex flex-col space-y-3" action="#" method="POST">
      @csrf
      <input
        type="email"
        name="email"
        placeholder="আপনার ইমেইল"
        class="px-3 py-2 border border-border focus:outline-none focus:border-border w-full text-sm rounded-none"
      />
      <button
        type="submit"
        class="bg-[#e2231a] text-white font-bold py-2 px-4 hover:bg-[#e2231a]/90 transition-colors text-sm rounded-none"
      >
        সাবস্ক্রাইব
      </button>
    </form>
  </div>

  <div class="bg-surface w-full h-[250px] flex items-center justify-center border border-border">
    <span class="text-gray-400 text-xs uppercase tracking-widest">বিজ্ঞাপন</span>
  </div>

</aside>
