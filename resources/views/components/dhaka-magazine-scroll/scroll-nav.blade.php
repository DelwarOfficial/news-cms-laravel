{{--
    Scroll Nav — Dhaka Magazine Laravel
    Usage: @include('components.dhaka-magazine-scroll.scroll-nav')
    Data:  $tickerHeadlines (auto-injected by View Composer)
--}}

@if( isset($tickerHeadlines) && count($tickerHeadlines) > 0 )
{{-- Inline styles on critical elements guarantee height/overflow even if Tailwind layers override --}}
<div id="dms-scroll-nav"
     style="width:100%;font-family:'NotoSerifBengali',serif;background:var(--color-surface,#f9fafb);border-bottom:1px solid var(--color-border,#e2e8f0);">

    {{-- Bar: strict 38px, overflow hidden — inline style is uncascadable --}}
    <div class="dms-ticker-bar"
         style="display:flex;flex-direction:row;align-items:stretch;height:38px;max-height:38px;overflow:hidden;max-width:80rem;margin:4px auto;padding:0 1rem;border-radius:6px;border:1px solid var(--color-border,#e2e8f0);background:var(--color-bg,#fff);box-shadow:0 1px 3px rgba(0,0,0,.04);">

        {{-- Red "সর্বশেষ" label --}}
        <div style="flex-shrink:0;display:inline-flex;align-items:center;gap:6px;padding:0 14px;background:var(--color-primary,#e2231a);color:#fff;font-size:16px;font-weight:700;white-space:nowrap;letter-spacing:.02em;">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                 stroke-linejoin="round" style="flex-shrink:0;opacity:.9;">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            সর্বশেষ
        </div>

        {{-- Track: overflow hidden, single row --}}
        <div id="dmsTickerTrack"
             style="flex:1 1 0%;min-width:0;overflow:hidden;position:relative;-webkit-mask-image:linear-gradient(to right,transparent 0%,#000 4%,#000 96%,transparent 100%);mask-image:linear-gradient(to right,transparent 0%,#000 4%,#000 96%,transparent 100%);">

            <div id="dmsTickerContent"
                 style="display:inline-flex;flex-shrink:0;align-items:center;white-space:nowrap;height:38px;padding:0;will-change:transform;">
                @foreach( $tickerHeadlines as $headline )
                    <a href="{{ route('article.show', $headline['slug']) }}"
                       class="dms-ticker-link"
                       style="color:var(--color-fg,#1f2a44);font-size:16px;font-weight:500;text-decoration:none;white-space:nowrap;padding:0 4px;transition:color .2s;">{{ $headline['title'] }}</a>
                    <span style="color:var(--color-primary,#e2231a);font-size:20px;margin:0 18px;flex-shrink:0;opacity:.7;">●</span>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endif

{{-- Dark mode overrides + hover effects (inline styles can't do :hover or .dark) --}}
@push('styles')
<style>
.dark #dms-scroll-nav {
    background: var(--color-surface-alt, #1e293b) !important;
    border-color: rgba(255,255,255,0.06) !important;
}
.dark #dms-scroll-nav .dms-ticker-bar {
    background: var(--color-surface, #111827) !important;
    border-color: rgba(255,255,255,.06) !important;
}
.dark .dms-ticker-link {
    color: var(--color-fg, #f8fafc) !important;
}
.dms-ticker-link:hover,
.dark .dms-ticker-link:hover {
    color: var(--color-primary, #e2231a) !important;
}
@media (max-width: 768px) {
    #dms-scroll-nav .dms-ticker-bar {
        height: 34px !important;
        max-height: 34px !important;
        border-radius: 4px !important;
    }
    #dms-scroll-nav #dmsTickerContent {
        height: 34px !important;
    }
    #dms-scroll-nav .dms-ticker-link {
        font-size: 14px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';
    var content = document.getElementById('dmsTickerContent');
    var track   = document.getElementById('dmsTickerTrack');
    if (!content || !track) return;

    var clone = content.cloneNode(true);
    clone.removeAttribute('id');
    clone.setAttribute('aria-hidden', 'true');
    track.appendChild(clone);

    var speed    = 0.8;
    var pos      = 0;
    var paused   = false;
    var contentW = content.offsetWidth;

    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function () { contentW = content.offsetWidth; });
    }

    function tick() {
        if (!paused) {
            pos -= speed;
            if (Math.abs(pos) >= contentW) pos = 0;
            var t = 'translateX(' + pos + 'px)';
            content.style.transform = t;
            clone.style.transform   = t;
        }
        requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);

    track.addEventListener('mouseenter', function () { paused = true;  });
    track.addEventListener('mouseleave', function () { paused = false; });
    track.addEventListener('focusin',   function () { paused = true;  });
    track.addEventListener('focusout',  function () { paused = false; });
})();
</script>
@endpush
