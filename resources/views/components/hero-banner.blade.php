@props([
    'slides' => [],
    'autoPlay' => true,
    'interval' => 5000,
])

@php
    $slideCount = count($slides);
@endphp

@if($slideCount > 0)
<div x-data="{
    currentSlide: 0,
    slideCount: {{ $slideCount }},
    autoplayInterval: null,
    next() { if (this.slideCount < 1) return; this.currentSlide = (this.currentSlide + 1) % this.slideCount; },
    prev() { if (this.slideCount < 1) return; this.currentSlide = (this.currentSlide - 1 + this.slideCount) % this.slideCount; },
    goTo(index) { this.currentSlide = index; },
    startAutoplay() { if (this.slideCount > 1 && !this.autoplayInterval) this.autoplayInterval = setInterval(() => this.next(), {{ (int) $interval }}); },
    stopAutoplay() { clearInterval(this.autoplayInterval); this.autoplayInterval = null; },
    init() { this.startAutoplay(); }
}" class="gc-hero-banner" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
    <div class="relative overflow-hidden">
        @foreach($slides as $index => $slide)
            <div x-show="currentSlide === {{ $index }}" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="gc-hero-slide">
                <img src="{{ $slide['image'] ?? '' }}" alt="{{ $slide['title'] ?? '' }}" class="w-full h-full object-cover" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                <div class="gc-hero-overlay">
                    <h2 class="gc-hero-title">{{ $slide['title'] ?? '' }}</h2>
                    @isset($slide['subtitle'])
                        <p class="gc-hero-subtitle">{{ $slide['subtitle'] }}</p>
                    @endif
                    @isset($slide['cta_text'])
                        <a href="{{ $slide['cta_url'] ?? '#' }}" class="gc-btn gc-btn-accent gc-btn-md inline-flex">
                            {{ $slide['cta_text'] }}
                        </a>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Navigation Arrows --}}
        @if($slideCount > 1)
            <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-[var(--color-gc-bg-card)]/80 backdrop-blur-sm text-[var(--color-gc-text)] hover:bg-[var(--color-gc-bg-card)] transition-all z-10" aria-label="Previous slide">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-[var(--color-gc-bg-card)]/80 backdrop-blur-sm text-[var(--color-gc-text)] hover:bg-[var(--color-gc-bg-card)] transition-all z-10" aria-label="Next slide">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        @endif
    </div>

    {{-- Dots --}}
    @if($slideCount > 1)
        <div class="gc-hero-dots">
            @foreach($slides as $index => $slide)
                <button @click="goTo({{ $index }})" class="gc-hero-dot {{ $loop->first ? 'gc-hero-dot-active' : '' }}" :class="{ 'gc-hero-dot-active': currentSlide === {{ $index }} }" aria-label="Go to slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</div>
@else
<div class="gc-hero-banner">
    <div class="relative overflow-hidden">
        <div class="gc-hero-slide bg-[var(--color-gc-card)]">
            <div class="gc-hero-overlay">
                <h2 class="gc-hero-title">GameCommerce</h2>
                <p class="gc-hero-subtitle">Top up, game key, voucher, akun, item, dan jasa game dalam satu marketplace.</p>
                <a href="{{ route('search') }}" class="gc-btn gc-btn-accent gc-btn-md inline-flex">Jelajahi Produk</a>
            </div>
        </div>
    </div>
</div>
@endif
