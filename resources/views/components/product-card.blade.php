@props([
    'url' => '#',
    'image' => '',
    'name' => '',
    'gameName' => '',
    'price' => '',
    'originalPrice' => null,
    'discount' => null,
    'rating' => 0,
    'ratingCount' => null,
    'sellerName' => '',
    'sellerVerified' => false,
    'soldCount' => null,
    'isWishlisted' => false,
])

<a href="{{ $url }}" class="gc-card gc-card-product group">
    {{-- Discount Badge --}}
    @if($discount)
        <span class="gc-discount-badge gc-badge gc-badge-discount">{{ $discount }}</span>
    @endif

    {{-- Wishlist Button --}}
    <button class="gc-wishlist-btn {{ $isWishlisted ? 'text-[var(--color-gc-error)]' : '' }}" onclick="event.preventDefault(); event.stopPropagation();" aria-label="Add to wishlist">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
    </button>

    {{-- Image --}}
    <div class="gc-product-image gc-product-img-section">
        @if($image)
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-[var(--color-gc-text-tertiary)]">
                <svg class="w-12 h-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="gc-product-content">
        @if($gameName)
            <span class="gc-product-game-name">{{ $gameName }}</span>
        @endif

        <h3 class="gc-product-name">{{ $name }}</h3>

        @if($sellerName)
            <div class="gc-product-seller">
                <span class="gc-product-seller-name">{{ $sellerName }}</span>
                @if($sellerVerified)
                    <x-seller-badge />
                @endif
            </div>
        @endif

        <div class="gc-product-footer">
            <div>
                <span class="gc-price gc-price-compact">{{ $price }}</span>
                @if($originalPrice)
                    <span class="gc-price-strike ml-1">{{ $originalPrice }}</span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if($rating > 0)
                    <span class="gc-product-rating">
                        <svg class="w-3.5 h-3.5 text-[var(--color-gc-warning)] inline" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <span class="text-[var(--color-gc-text-secondary)]">{{ number_format($rating, 1) }}</span>
                    </span>
                @endif

                @if($soldCount !== null)
                    <span class="gc-product-sold">{{ $soldCount }} terjual</span>
                @endif
            </div>
        </div>
    </div>
</a>
