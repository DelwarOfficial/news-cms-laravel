{{-- Numbered Card: large Bengali number + title + time --}}
{{-- Usage: <x-cards.numbered :article="$a" :index="0" /> --}}

@props(['article', 'index' => 0, 'class' => ''])

@php
    $bengaliNumbers = ['১','২','৩','৪','৫','৬','৭','৮','৯','১০'];
    $number = $bengaliNumbers[$index] ?? ($index + 1);
@endphp

<a href="{{ route('article.show', $article['slug']) }}" class="group flex items-start gap-3 py-3 border-b border-border last:border-b-0 {{ $class }}">
    <span class="font-serif font-bold text-[34px] text-fg-muted shrink-0 w-8 text-center leading-none mt-0.5">
        {{ $number }}
    </span>
    <div class="flex-1 pt-0.5">
        <h3 class="font-serif font-extrabold text-[14px] text-fg leading-snug group-hover:text-[#e2231a] transition-colors line-clamp-3">
            {{ $article['title'] }}
        </h3>
        @if(!empty($article['time_ago']))
            <div class="text-[11px] text-fg-muted mt-1">{{ $article['time_ago'] }}</div>
        @endif
    </div>
</a>
