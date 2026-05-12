@props([
    'height' => '90px',
    'rounded' => true,
])

<div class="w-full bg-surface border border-border flex items-center justify-center {{ $rounded ? 'rounded-lg' : '' }}" style="height: {{ $height }};">
  <span class="text-fg-muted text-[12px] tracking-widest uppercase">বিজ্ঞাপন</span>
</div>
