<?php extract((new \Illuminate\Support\Collection($attributes->getAttributes()))->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>
@props(['id','name','value','class'])
<x-rich-text::trix :id="$id" :name="$name" :value="$value" :class="$class" >

{{ $slot ?? "" }}
</x-rich-text::trix>