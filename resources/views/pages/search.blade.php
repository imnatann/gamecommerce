@extends('layouts.app', ['title' => 'Hasil Pencarian — GameCommerce'])

@section('content')
<div class="gc-page-wrapper" x-data="{ filterOpen: false }">
    {{-- Search Bar */}
    <div class="gc-card p-4 mb-6">
        <form action="{{ route('search') }}" method="GET" class="max-w-2xl mx-auto">
            <x-search-bar
                placeholder="Cari game, top up, akun..."
                :action="route('search')"
                id="search-page-bar"
                value="{{ $query ?? '' }}"
            />
        </form>
    </div>

    <div class="gc-content-with-sidebar">
        {{-- Filter Sidebar --}}
        <aside class="gc-sidebar" :class="{ 'gc-sidebar-open': filterOpen }">
            <div class="gc-sidebar-header md:hidden flex items-center justify-between">
                <h3 class="font-semibold text-[var(--color-gc-text)]">Filter</h3>
                <button @click="filterOpen = false" class="gc-btn-icon gc-btn-ghost" aria-label="Close filters">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Category Filter --}}
            <div class="gc-filter-group">
                <h4 class="gc-filter-title">Kategori</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories ?? [] as $cat)
                        <x-category-chip :label="$cat['name'] ?? ''" :url="$cat['url'] ?? '#'" :active="$cat['active'] ?? false" />
                    @endforeach
                </div>
            </div>

            {{-- Price Range Filter --}}
            <div class="gc-filter-group">
                <h4 class="gc-filter-title">Harga</h4>
                <div class="flex items-center gap-2">
                    <input type="number" name="min_price" class="gc-input gc-input-sm flex-1" placeholder="Min" value="{{ request('min_price') }}">
                    <span class="text-[var(--color-gc-text-tertiary)]">—</span>
                    <input type="number" name="max_price" class="gc-input gc-input-sm flex-1" placeholder="Max" value="{{ request('max_price') }}">
                </div>
            </div>

            {{-- Delivery Type --}}
            <div class="gc-filter-group">
                <h4 class="gc-filter-title">Pengiriman</h4>
                <div class="space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="delivery[]" value="instant" class="gc-checkbox">
                        <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors">Instan</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="delivery[]" value="manual" class="gc-checkbox">
                        <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors">Manual</span>
                    </label>
                </div>
            </div>

            {{-- Rating Filter --}}
            <div class="gc-filter-group">
                <h4 class="gc-filter-title">Rating Minimum</h4>
                <div class="space-y-2">
                    @for($i = 5; $i >= 3; $i--)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="min_rating" value="{{ $i }}" class="gc-radio">
                            <span class="gc-filter-label group-hover:text-[var(--color-gc-primary)] transition-colors">{{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }}</span>
                        </label>
                    @endfor
                </div>
            </div>

            <button class="gc-btn gc-btn-primary gc-btn-md w-full mt-4">Terapkan</button>
        </aside>

        {{-- Main Content --}}
        <div class="gc-main-content">
            {{-- Sort and Filter Controls --}}
            <div class="flex items-center justify-between gap-4 mb-4">
                <div class="flex items-center gap-2">
                    <button @click="filterOpen = !filterOpen" class="gc-btn gc-btn-ghost gc-btn-sm gc-hide-desktop" aria-label="Toggle filters">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Filter
                    </button>
                    <span class="text-sm text-[var(--color-gc-text-secondary)]">
                        @if($query)
                            Hasil pencarian untuk "<strong class="text-[var(--color-gc-text)]">{{ $query }}</strong>"
                        @endif
                        — {{ ($totalResults ?? 0) . ' produk' }}
                    </span>
                </div>
                <select name="sort" class="gc-select gc-select-sm w-auto">
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                    <option value="cheapest" {{ request('sort') === 'cheapest' ? 'selected' : '' }}>Termurah</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                </select>
            </div>

            {{-- Product Grid --}}
            @if(($products ?? [])->count() > 0)
                <div class="gc-product-grid">
                    @foreach($products as $product)
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
                    icon='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-16 h-16"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>'
                    title="Tidak Ditemukan"
                    description="Maaf, tidak ada produk yang sesuai dengan pencarian Anda. Coba kata kunci lain atau ubah filter."
                    actionText="Kembali ke Beranda"
                    :actionUrl="route('home')"
                />
            @endif

            {{-- Popular Searches --}}
            @if($popularSearches ?? [])
                <section class="mt-8 pt-6 border-t border-[var(--color-gc-border)]">
                    <h3 class="font-[var(--font-family-display)] text-base font-semibold text-[var(--color-gc-text)] mb-3">Pencarian Populer</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($popularSearches as $search)
                            <a href="{{ route('search', ['q' => $search]) }}" class="gc-chip">
                                {{ $search }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>

    {{-- Mobile Filter Overlay Backdrop --}}
    <div x-show="filterOpen" @click="filterOpen = false" class="fixed inset-0 bg-black/50 z-30 md:hidden" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
</div>
@endsection
