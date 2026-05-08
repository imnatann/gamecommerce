@props([
    'price' => '',
    'originalPrice' => null,
    'size' => 'compact',
    'currency' => 'Rp',
])

@php
    $sizeClass = match($size) {
        'compact' => 'gc-price-compact',
        'large' => 'gc-price-large',
        'display' => 'gc-price-display',
        default => 'gc-price-compact',
    };
@endphp

<div class="flex items-baseline gap-1.5">
    <span class="gc-price {{ $sizeClass }}">{{ $currency }} {{ $price }}</span>
    @if($originalPrice)
        <span class="gc-price-strike">{{ $currency }} {{ $originalPrice }}</span>
    @endif
</div>