@extends('layouts.app', ['title' => ($game['name'] ?? 'Game') . ' — GameCommerce'])

@section('content')
<div class="gc-page-wrapper">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => $game['name'] ?? 'Game']
    ]" />

    {{-- Game Header --}}
    <div class="gc-card p-4 md:p-6 mb-6">
        <div class="flex items-center gap-4">
            @if($game['icon'] ?? '')
                <img src="{{ $game['icon'] }}" alt="{{ $game['name'] ?? '' }}" class="w-16 h-16 md:w-20 md:h-20 rounded-2xl object-cover" loading="lazy">
            @else
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-[var(--color-gc-primary)]/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><path d="M6 12h4m4 0h4"/></svg>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <h1 class="font-[var(--font-family-display)] text-2xl md:text-3xl font-bold text-[var(--color-gc-text)]">{{ $game['name'] ?? 'Game' }}</h1>
                @if($game['region'] ?? '')
                    <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Region: {{ $game['region'] }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="gc-page-layout" x-data="{ activeTab: '{{ $activeTab ?? 'topup' }}', showFilters: false, sort: '{{ $sort ?? 'popular' }}', priceRange: [0, 10000000] }">
        {{-- Product Type Tabs --}}
        <div class="gc-tabs-scroll mb-6">
            <div class="gc-tabs">
                @foreach($productTypes ?? [] as $type)
                    <button @click="activeTab = '{{ $type['slug'] }}'" :class="activeTab === '{{ $type['slug'] }}' ? 'gc-tab-active' : 'gc-tab'" class="gc-tab whitespace-nowrap">{{ $type['name'] }}</button>
                @endforeach
            </div>
        </div>

        <div class="gc-content-with-sidebar">
            {{-- Sidebar Filters --}}
            <aside class="gc-sidebar" :class="{ 'gc-sidebar-open': showFilters }">
                <div class="gc-sidebar-header md:hidden flex items-center justify-between">
                    <h3 class="font-semibold text-[var(--color-gc-text)]">Filter</h3>
                    <button @click="showFilters = false" class="gc-btn-icon gc-btn-ghost" aria-label="Close filters">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                {{-- Server Filter --}}
                <div class="gc-filter-group">
                    <h4 class="gc-filter-title">Server</h4>
                    <div class="space-y-2">
                        @foreach($servers ?? [] as $server)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" value="{{ $server['id'] ?? '' }}" class="gc-checkbox">
                                <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors">{{ $server['name'] ?? '' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Region Filter --}}
                <div class="gc-filter-group">
                    <h4 class="gc-filter-title">Region</h4>
                    <div class="space-y-2">
                        @foreach($regions ?? [] as $region)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" value="{{ $region['id'] ?? '' }}" class="gc-checkbox">
                                <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors">{{ $region['name'] ?? '' }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Price Range Filter --}}
                <div class="gc-filter-group">
                    <h4 class="gc-filter-title">Harga</h4>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="priceRange[0]" class="gc-input gc-input-sm flex-1" placeholder="Min">
                        <span class="text-[var(--color-gc-text-tertiary)]">—</span>
                        <input type="number" x-model="priceRange[1]" class="gc-input gc-input-sm flex-1" placeholder="Max">
                    </div>
                </div>

                {{-- Delivery Type Filter --}}
                <div class="gc-filter-group">
                    <h4 class="gc-filter-title">Pengiriman</h4>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="delivery" value="instant" class="gc-checkbox">
                            <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors">Instan</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="delivery" value="manual" class="gc-checkbox">
                            <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors">Manual</span>
                        </label>
                    </div>
                </div>

                {{-- Rating Filter --}}
                <div class="gc-filter-group">
                    <h4 class="gc-filter-title">Rating</h4>
                    <div class="space-y-2">
                        @for($i = 5; $i >= 4; $i--)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" value="{{ $i }}" class="gc-checkbox">
                                <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors flex items-center gap-1">
                                    {{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }} ke atas
                                </span>
                            </label>
                        @endfor
                    </div>
                </div>

                <button class="gc-btn gc-btn-primary gc-btn-md w-full mt-4">Terapkan Filter</button>
            </aside>

            {{-- Main Content --}}
            <div class="gc-main-content">
                {{-- Sort Options --}}
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-2 flex-1">
                        <button @click="showFilters = !showFilters" class="gc-btn gc-btn-ghost gc-btn-sm gc-hide-desktop" aria-label="Toggle filters">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                            Filter
                        </button>
                        <span class="text-sm text-[var(--color-gc-text-secondary)]">{{ ($totalProducts ?? 0) . ' produk' }}</span>
                    </div>
                    <select x-model="sort" class="gc-select gc-select-sm w-auto">
                        <option value="popular">Terpopuler</option>
                        <option value="cheapest">Termurah</option>
                        <option value="newest">Terbaru</option>
                        <option value="rating">Rating Tertinggi</option>
                    </select>
                </div>

                {{-- Product Grid --}}
                @if(($products ?? [])->count() > 0)
                    <div class="gc-product-grid">
                        @foreach($products ?? [] as $product)
                            <x-product-card
                                :url="$product['url'] ?? '#'"
                                :image="$product['image'] ?? ''"
                                :name="$product['name'] ?? ''"
                                :gameName="$product['game_name'] ?? ''"
                                :price="$product['price'] ?? ''"
                                :originalPrice="$product['original_price'] ?? null"
                                :discount="$product['discount'] ?? null"
                                :rating="$product['rating'] ?? 0"
                                :ratingCount="$product['rating_count'] ?? null"
                                :soldCount="$product['sold_count'] ?? null"
                                :sellerName="$product['seller_name'] ?? ''"
                                :sellerVerified="$product['seller_verified'] ?? false"
                            />
                        @endforeach
                    </div>

                    <x-pagination :paginator="$products ?? null" />
                @else
                    <x-empty-state
                        icon='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-16 h-16"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>'
                        title="Belum Ada Produk"
                        description="Produk untuk kategori ini belum tersedia. Coba ubah filter atau kategori."
                        actionText="Lihat Game Lainnya"
                        :actionUrl="route('home')"
                    />
                @endif
            </div>
        </div>
    </div>

    {{-- Mobile Filter Overlay Backdrop --}}
    <div x-show="showFilters" @click="showFilters = false" class="fixed inset-0 bg-black/50 z-30 md:hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
</div>
@endsection