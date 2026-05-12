@props([
  'name',
  'size' => null,
  'class' => '',
  'image' => null,
  'link' => null,
  'htmlCode' => null,
  'isActive' => null,
])

@php
  $slot = \App\Support\AdvertisementSlotResolver::resolve($name);
  $active = $isActive ?? ($slot['is_active'] ?? config('ads.defaults.is_active', true));
  $label = $slot['label'] ?? config('ads.defaults.label', 'বিজ্ঞাপন');
  $desktopSize = $size ?: ($slot['desktop_size'] ?? '300x250');
  $mobileSize = $slot['mobile_size'] ?? $desktopSize;
  $imageUrl = $image ?: ($slot['image_url'] ?? null);
  $targetUrl = $link ?: ($slot['target_url'] ?? null);
  $code = $htmlCode ?: ($slot['html_code'] ?? null);

  [$desktopWidth, $desktopHeight] = array_pad(explode('x', strtolower($desktopSize)), 2, null);
  [$mobileWidth, $mobileHeight] = array_pad(explode('x', strtolower($mobileSize)), 2, null);
  $desktopWidth = (int) $desktopWidth ?: 300;
  $desktopHeight = (int) $desktopHeight ?: 250;
  $mobileWidth = (int) $mobileWidth ?: $desktopWidth;
  $mobileHeight = (int) $mobileHeight ?: $desktopHeight;
@endphp

@if($active)
  <div
    class="ad-slot {{ $class }}"
    style="--ad-desktop-width: {{ $desktopWidth }}px; --ad-desktop-height: {{ $desktopHeight }}px; --ad-mobile-width: {{ $mobileWidth }}px; --ad-mobile-height: {{ $mobileHeight }}px;"
    data-ad-slot="{{ $name }}"
    aria-label="{{ $label }}"
  >
    <span class="ad-slot__label">{{ $label }}</span>

    <div class="ad-slot__box">
      @if($code)
        {!! $code !!}
      @elseif($imageUrl)
        @if($targetUrl)
          <a href="{{ $targetUrl }}" target="_blank" rel="noopener sponsored" class="ad-slot__link">
            <img src="{{ $imageUrl }}" width="{{ $desktopWidth }}" height="{{ $desktopHeight }}" alt="{{ $label }}" loading="lazy">
          </a>
        @else
          <img src="{{ $imageUrl }}" width="{{ $desktopWidth }}" height="{{ $desktopHeight }}" alt="{{ $label }}" loading="lazy">
        @endif
      @else
        <span class="ad-slot__placeholder">{{ $desktopSize }}</span>
      @endif
    </div>
  </div>
@endif
