@extends('layouts.app', ['title' => 'Top Up Game Termurah — Beli Game Key, Akun, Voucher & Item'])

@section('content')
<div class="gc-page-wrapper">
    {{-- Hero Banner Carousel --}}
    <section class="gc-section">
        <x-hero-banner :slides="$banners ?? []" />
    </section>

    {{-- Quick Category Icons --}}
    <section class="gc-section">
        <div class="gc-category-scroll-wrapper">
            <div class="gc-category-scroll">
                @php
                    $quickCategories = [
                        ['label' => 'Top Up', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>', 'url' => route('search', ['type' => 'topup'])],
                        ['label' => 'Game Key', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>', 'url' => route('search', ['type' => 'game_key'])],
                        ['label' => 'Akun', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>', 'url' => route('search', ['type' => 'account'])],
                        ['label' => 'Voucher', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="8" x="2" y="8" rx="2" ry="2"/><path d="M12 8v8"/><path d="M2 12h4"/><path d="M18 12h4"/></svg>', 'url' => route('search', ['type' => 'voucher'])],
                        ['label' => 'Item', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>', 'url' => route('search', ['type' => 'item'])],
                        ['label' => 'Joki', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 12h4m4 0h4"/><rect width="20" height="12" x="2" y="6" rx="2"/><path d="M12 18v4"/><path d="M8 22h8"/></svg>', 'url' => route('search', ['type' => 'joki'])],
                        ['label' => 'Koin Game', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/></svg>', 'url' => route('search', ['type' => 'coin'])],
                        ['label' => 'RPG Games', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>', 'url' => route('search', ['genre' => 'rpg'])],
                        ['label' => 'Random Steam', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h10v10H7z"/><path d="M3 3l4 4"/><path d="M21 3l-4 4"/><path d="M3 21l4-4"/><path d="M21 21l-4-4"/></svg>', 'url' => route('search', ['q' => 'random steam key'])],
                        ['label' => 'Simulation', 'icon' => '<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/></svg>', 'url' => route('search', ['genre' => 'simulation'])],
                    ];
                @endphp
                @foreach($quickCategories as $cat)
                    <a href="{{ $cat['url'] }}" class="gc-quick-category">
                        <span class="gc-quick-category-icon text-[var(--color-gc-primary)]">{!! $cat['icon'] !!}</span>
                        <span class="gc-quick-category-label">{{ $cat['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Flash Sale (if active) --}}
    @isset($flashSale)
        <section class="gc-section">
            <div class="gc-card p-4 md:p-6 border border-[var(--color-gc-warning)]/30">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <h2 class="font-[var(--font-family-display)] text-xl font-bold text-[var(--color-gc-warning)]">Flash Sale!</h2>
                    </div>
                    <x-flash-sale-timer :endTime="$flashSale['ends_at'] ?? ''" label="Berakhir dalam" />
                </div>
                <div class="gc-product-grid">
                    @foreach($flashSale['products'] ?? [] as $product)
                        <x-product-card
                            :url="$product['url'] ?? '#'"
                            :image="$product['image'] ?? ''"
                            :name="$product['name'] ?? ''"
                            :gameName="$product['game_name'] ?? ''"
                            :price="$product['price'] ?? ''"
                            :originalPrice="$product['original_price'] ?? null"
                            :discount="$product['discount'] ?? null"
                            :rating="$product['rating'] ?? 0"
                            :soldCount="$product['sold_count'] ?? null"
                            :sellerName="$product['seller_name'] ?? ''"
                            :sellerVerified="$product['seller_verified'] ?? false"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endisset

    {{-- Top Up Game Section --}}
    <section class="gc-section">
        <x-section-header
            title="Top Up Game"
            url="{{ route('search', ['type' => 'topup']) }}"
            linkText="Lihat Semua"
            icon='<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'
        />
        <div class="gc-game-grid">
            @foreach($topUpGames ?? [] as $game)
                <x-game-card
                    :name="$game['name'] ?? ''"
                    :slug="$game['slug'] ?? ''"
                    :icon="$game['icon'] ?? ''"
                    :url="$game['url'] ?? '#'"
                />
            @endforeach
        </div>
    </section>

    {{-- Voucher Section --}}
    <section class="gc-section">
        <x-section-header
            title="Voucher Game"
            url="{{ route('search', ['type' => 'voucher']) }}"
            linkText="Lihat Semua"
            icon='<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="8" x="2" y="8" rx="2" ry="2"/><path d="M12 8v8"/><path d="M2 12h4"/><path d="M18 12h4"/></svg>'
        />
        <div class="gc-game-grid">
            @foreach($voucherGames ?? [] as $game)
                <x-game-card
                    :name="$game['name'] ?? ''"
                    :slug="$game['slug'] ?? ''"
                    :icon="$game['icon'] ?? ''"
                    :url="$game['url'] ?? '#'"
                />
            @endforeach
        </div>
    </section>

    {{-- Game Key Section --}}
    <section class="gc-section">
        <x-section-header
            title="Game Key Resmi, Instan Klaim!"
            url="{{ route('search', ['type' => 'game_key']) }}"
            linkText="Lihat Semua"
            icon='<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>'
        />
        <div class="gc-product-grid">
            @foreach($gameKeyProducts ?? [] as $product)
                <x-product-card
                    :url="$product['url'] ?? '#'"
                    :image="$product['image'] ?? ''"
                    :name="$product['name'] ?? ''"
                    :gameName="$product['game_name'] ?? ''"
                    :price="$product['price'] ?? ''"
                    :originalPrice="$product['original_price'] ?? null"
                    :discount="$product['discount'] ?? null"
                    :rating="$product['rating'] ?? 0"
                    :soldCount="$product['sold_count'] ?? null"
                    :sellerName="$product['seller_name'] ?? ''"
                    :sellerVerified="$product['seller_verified'] ?? false"
                />
            @endforeach
        </div>
    </section>

    {{-- Roblox Games Section --}}
    <section class="gc-section">
        <x-section-header
            title="Roblox Games"
            url="{{ route('search', ['q' => 'roblox']) }}"
            linkText="Lihat Semua"
            icon='<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 3 6 18"/><path d="m3 9 18 6"/></svg>'
        />
        <div class="gc-product-grid">
            @foreach($robloxProducts ?? [] as $product)
                <x-product-card
                    :url="$product['url'] ?? '#'"
                    :image="$product['image'] ?? ''"
                    :name="$product['name'] ?? ''"
                    :gameName="$product['game_name'] ?? ''"
                    :price="$product['price'] ?? ''"
                    :originalPrice="$product['original_price'] ?? null"
                    :discount="$product['discount'] ?? null"
                    :rating="$product['rating'] ?? 0"
                    :soldCount="$product['sold_count'] ?? null"
                    :sellerName="$product['seller_name'] ?? ''"
                    :sellerVerified="$product['seller_verified'] ?? false"
                />
            @endforeach
        </div>
    </section>

    {{-- Akun Section --}}
    <section class="gc-section">
        <x-section-header
            title="Akun Game Premium"
            url="{{ route('search', ['type' => 'account']) }}"
            linkText="Lihat Semua"
            icon='<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'
        />
        <div class="gc-product-grid">
            @foreach($accountProducts ?? [] as $product)
                <x-product-card
                    :url="$product['url'] ?? '#'"
                    :image="$product['image'] ?? ''"
                    :name="$product['name'] ?? ''"
                    :gameName="$product['game_name'] ?? ''"
                    :price="$product['price'] ?? ''"
                    :originalPrice="$product['original_price'] ?? null"
                    :discount="$product['discount'] ?? null"
                    :rating="$product['rating'] ?? 0"
                    :soldCount="$product['sold_count'] ?? null"
                    :sellerName="$product['seller_name'] ?? ''"
                    :sellerVerified="$product['seller_verified'] ?? false"
                />
            @endforeach
        </div>
    </section>

    {{-- Trust Section --}}
    <section class="gc-section">
        <div class="gc-trust-grid">
            <x-trust-badge icon="shield" title="Transaksi Aman" description="Semua transaksi dilindungi enkripsi dan sistem keamanan terbaik." />
            <x-trust-badge icon="wallet" title="Garansi Uang Kembali" description="Uang kembali 100% jika produk tidak sesuai deskripsi." />
            <x-trust-badge icon="clock" title="Bantuan 24/7" description="Tim customer service siap membantu kapan saja." />
        </div>
    </section>

    {{-- Payment Methods Section --}}
    <section class="gc-section">
        <x-section-header title="Metode Pembayaran" />
        <x-payment-method-grid :methods="$paymentMethods ?? []" />
    </section>
</div>
@endsection
