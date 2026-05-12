{{-- Section Header: title with optional "more" link --}}
{{-- Usage: <x-section-header title="বাংলাদেশ" :more-url="route(...)" /> --}}

@props(['title', 'moreUrl' => null, 'moreText' => 'আরও পড়ুন', 'showIcon' => true, 'class' => ''])

<div class="flex items-center justify-between gap-3 pb-2 mb-4 border-b border-border {{ $class }}">
    <h2 class="font-serif font-extrabold text-[20px] text-fg leading-none flex items-center gap-3">
        @if($showIcon)
            <span class="section-icon"></span>
        @endif
        {{ $title }}
    </h2>
    @if($moreUrl)
        <a href="{{ $moreUrl }}" class="text-fg-secondary text-[13px] hover:text-primary transition-colors flex items-center gap-0.5">
            {{ $moreText }}
            <span class="text-[15px] leading-none ml-0.5">&rsaquo;</span>
        </a>
    @endif
</div>
