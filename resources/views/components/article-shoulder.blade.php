@props(['article' => null, 'tone' => 'default'])

@php
    $shoulder = trim((string) data_get($article, 'shoulder', ''));
    $classes = $tone === 'light'
        ? 'text-[#fca5a5]'
        : 'text-[#b91c1c]';
@endphp

@if($shoulder !== '')
    <span {{ $attributes->merge(['class' => "font-extrabold {$classes}"]) }}>{{ $shoulder }}</span>
    <span class="{{ $tone === 'light' ? 'text-white/85' : 'text-fg' }} mx-1">&bull;</span>
@endif
