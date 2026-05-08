@props([
    'rating' => 0,
    'maxStars' => 5,
    'showCount' => false,
    'count' => null,
    'size' => 'sm',
])

@php
    $sizeMap = [
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
    ];
    $iconSize = $sizeMap[$size] ?? $sizeMap['sm'];
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.25 && ($rating - $fullStars) < 0.75;
    $emptyStars = $maxStars - $fullStars - ($hasHalfStar ? 1 : 0);
@endphp

<div class="gc-rating-stars inline-flex items-center" aria-label="Rating {{ number_format($rating, 1) }} out of {{ $maxStars }}">
    @for($i = 0; $i < $fullStars; $i++)
        <svg class="{{ $iconSize }} gc-star-filled" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    @endfor

    @if($hasHalfStar)
        <svg class="{{ $iconSize }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <defs>
                <linearGradient id="half-star-{{ $rating }}">
                    <stop offset="50%" stop-color="var(--color-gc-warning)"/>
                    <stop offset="50%" stop-color="var(--color-gc-border)"/>
                </linearGradient>
            </defs>
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="url(#half-star-{{ $rating }})" stroke="var(--color-gc-border)" stroke-width="1"/>
        </svg>
    @endif

    @for($i = 0; $i < $emptyStars; $i++)
        <svg class="{{ $iconSize }} gc-star-empty" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
    @endfor

    @if($showCount && $count !== null)
        <span class="ml-1 text-[var(--font-size-xs)] text-[var(--color-gc-text-secondary)]">({{ number_format($count) }})</span>
    @endif
</div>