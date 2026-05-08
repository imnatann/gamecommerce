@extends('layouts.app', ['title' => ($product['name'] ?? 'Produk') . ' — GameCommerce'])

@section('content')
<div class="gc-page-wrapper" x-data="{ quantity: 1, selectedVariant: null, activeTab: 'description', wishlistLoading: false }">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => $product['game_name'] ?? 'Game', 'url' => $product['game_url'] ?? '#'],
        ['label' => $product['name'] ?? 'Produk']
    ]" />

    {{-- Product Detail Card --}}
    <div class="gc-card overflow-hidden">
        <div class="gc-product-detail-layout">
            {{-- Image Gallery --}}
            <div class="gc-product-gallery">
                @if(($product['images'] ?? []) || $product['image'] ?? '')
                    <div class="gc-gallery-main">
                        <img id="main-product-image" src="{{ $product['images'][0] ?? $product['image'] ?? '' }}" alt="{{ $product['name'] ?? '' }}" class="w-full aspect-square object-cover rounded-xl" loading="eager">
                    </div>
                    @if(($product['images'] ?? []) && count($product['images']) > 1)
                        <div class="gc-gallery-thumbnails flex gap-2 mt-3 overflow-x-auto pb-2">
                            @foreach($product['images'] as $index => $image)
                                <button @click="document.getElementById('main-product-image').src = '{{ $image }}'" class="gc-gallery-thumb {{ $loop->first ? 'gc-gallery-thumb-active' : '' }} flex-shrink-0">
                                    <img src="{{ $image }}" alt="{{ $product['name'] ?? '' }} {{ $index + 1 }}" class="w-full h-full object-cover rounded-lg" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full aspect-square rounded-xl bg-[var(--color-gc-hover)] flex items-center justify-center">
                        <svg class="w-24 h-24 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="gc-product-info">
                {{-- Game Name --}}
                @if($product['game_name'] ?? '')
                    <a href="{{ $product['game_url'] ?? '#' }}" class="text-sm text-[var(--color-gc-primary)] hover:underline">{{ $product['game_name'] }}</a>
                @endif

                <h1 class="font-[var(--font-family-display)] text-2xl md:text-3xl font-bold text-[var(--color-gc-text)] mt-1">{{ $product['name'] ?? '' }}</h1>

                {{-- Server / Region --}}
                <div class="flex items-center gap-3 mt-2 text-sm text-[var(--color-gc-text-secondary)]">
                    @if($product['server'] ?? '')
                        <span class="gc-badge gc-badge-processing">{{ $product['server'] }}</span>
                    @endif
                    @if($product['region'] ?? '')
                        <span class="gc-badge gc-badge-processing">{{ $product['region'] }}</span>
                    @endif
                </div>

                {{-- Rating + Sold --}}
                <div class="flex items-center gap-3 mt-3">
                    <x-rating-stars :rating="$product['rating'] ?? 0" size="md" :showCount="true" :count="$product['rating_count'] ?? null" />
                    @if($product['sold_count'] ?? null)
                        <span class="text-sm text-[var(--color-gc-text-secondary)]">{{ number_format($product['sold_count']) }} terjual</span>
                    @endif
                </div>

                {{-- Price --}}
                <div class="mt-4 p-4 rounded-xl bg-[var(--color-gc-bg)]">
                    <div class="flex items-baseline gap-3">
                        <span class="gc-price gc-price-display text-[var(--color-gc-accent)]">{{ $product['currency'] ?? 'Rp' }} {{ $product['price'] ?? '0' }}</span>
                        @if($product['original_price'] ?? '')
                            <span class="gc-price-strike text-lg">{{ $product['currency'] ?? 'Rp' }} {{ $product['original_price'] }}</span>
                            @if($product['discount'] ?? '')
                                <span class="gc-badge gc-badge-discount">{{ $product['discount'] }}</span>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Server/Variant Selector --}}
                @if(($product['variants'] ?? []) && count($product['variants']) > 0)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-2">Pilih Varian</label>
                        <select x-model="selectedVariant" class="gc-select w-full">
                            <option value="" disabled selected>-- Pilih Varian --</option>
                            @foreach($product['variants'] as $variant)
                                <option value="{{ $variant['id'] ?? '' }}" {{ $loop->first ? 'selected' : '' }}>{{ $variant['name'] ?? '' }} {{ isset($variant['price']) ? '- Rp ' . $variant['price'] : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Quantity Selector --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-2">Jumlah</label>
                    <div class="inline-flex items-center border border-[var(--color-gc-border)] rounded-lg overflow-hidden">
                        <button @click="quantity = Math.max(1, quantity - 1)" class="gc-qty-btn" aria-label="Decrease quantity">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <input type="number" x-model="quantity" min="1" class="w-12 text-center bg-transparent text-[var(--color-gc-text)] text-sm border-x border-[var(--color-gc-border)] p-2 focus:outline-none" aria-label="Quantity">
                        <button @click="quantity++" class="gc-qty-btn" aria-label="Increase quantity">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <button class="gc-btn gc-btn-accent gc-btn-lg flex-1 font-bold text-lg" onclick="document.getElementById('checkout-form').submit()">
                        BELI SEKARANG
                    </button>
                    <button class="gc-btn gc-btn-primary gc-btn-lg flex-1">
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                        Tambah ke Keranjang
                    </button>
                </div>

                {{-- Wishlist Button --}}
                <button @click="wishlistLoading = true; setTimeout(() => wishlistLoading = false, 800)" :disabled="wishlistLoading" class="gc-btn gc-btn-ghost gc-btn-sm mt-3 w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    {{ $product['is_wishlisted'] ?? false ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}
                </button>

                {{-- Trust Badges Row --}}
                <div class="grid grid-cols-3 gap-3 mt-6 pt-6 border-t border-[var(--color-gc-border)]">
                    <div class="text-center">
                        <svg class="w-6 h-6 mx-auto text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                        <p class="text-xs text-[var(--color-gc-text-secondary)] mt-1">Transaksi Aman</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-6 h-6 mx-auto text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <p class="text-xs text-[var(--color-gc-text-secondary)] mt-1">Instant Delivery</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-6 h-6 mx-auto text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a2 2 0 0 1 0 4H6a2 2 0 0 0 0 4h14a2 2 0 0 1 0 4H6a2 2 0 0 1-1-1v-3"/><path d="M15 15v2a2 2 0 0 1-2 2H7"/></svg>
                        <p class="text-xs text-[var(--color-gc-text-secondary)] mt-1">Garansi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Seller Info Card --}}
    <div class="gc-card p-4 md:p-6 mt-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-[var(--color-gc-primary)]/20 flex items-center justify-center text-[var(--color-gc-primary)] font-bold text-lg overflow-hidden">
                @if($product['seller_avatar'] ?? '')
                    <img src="{{ $product['seller_avatar'] }}" alt="{{ $product['seller_name'] ?? '' }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(($product['seller_name'] ?? 'S')[0]) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-[var(--color-gc-text)] truncate">{{ $product['seller_name'] ?? 'Seller' }}</span>
                    @if($product['seller_verified'] ?? false)
                        <x-seller-badge />
                    @endif
                </div>
                <div class="flex items-center gap-3 text-sm text-[var(--color-gc-text-secondary)]">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        {{ number_format($product['seller_rating'] ?? 0, 1) }}
                    </span>
                    <span>{{ number_format($product['seller_total_sold'] ?? 0) }} terjual</span>
                </div>
            </div>
            <a href="{{ $product['seller_url'] ?? '#' }}" class="gc-btn gc-btn-ghost gc-btn-sm">Lihat Toko</a>
        </div>
    </div>

    {{-- Product Description Tabs --}}
    <div class="gc-card mt-4">
        <div class="gc-tabs border-b border-[var(--color-gc-border)]">
            <button @click="activeTab = 'description'" :class="activeTab === 'description' ? 'gc-tab-active' : 'gc-tab'" class="gc-tab">Deskripsi</button>
            <button @click="activeTab = 'delivery'" :class="activeTab === 'delivery' ? 'gc-tab-active' : 'gc-tab'" class="gc-tab">Cara Pengiriman</button>
        </div>

        <div class="p-4 md:p-6">
            {{-- Description Tab --}}
            <div x-show="activeTab === 'description'" x-transition>
                <div class="prose prose-invert max-w-none text-[var(--color-gc-text-secondary)] text-sm leading-relaxed">
                    {!! nl2br(e($product['description'] ?? 'Deskripsi produk belum tersedia.')) !!}
                </div>
            </div>

            {{-- Delivery Tab --}}
            <div x-show="activeTab === 'delivery'" x-transition>
                <div class="space-y-4">
                    @if(($product['delivery_type'] ?? '') === 'instant')
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-[var(--color-gc-accent)]/10 border border-[var(--color-gc-accent)]/20">
                            <svg class="w-6 h-6 text-[var(--color-gc-accent)] flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <div>
                                <h4 class="font-semibold text-[var(--color-gc-accent)]">Pengiriman Instan</h4>
                                <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Produk ini dikirim secara otomatis setelah pembayaran dikonfirmasi. Kode/game key akan langsung tersedia di halaman pesanan Anda.</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-[var(--color-gc-info)]/10 border border-[var(--color-gc-info)]/20">
                            <svg class="w-6 h-6 text-[var(--color-gc-info)] flex-shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <div>
                                <h4 class="font-semibold text-[var(--color-gc-info)]">Pengiriman Manual</h4>
                                <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Produk ini dikirim secara manual oleh penjual. Estimasi waktu pengiriman bervariasi tergantung kecepatan respons penjual.</p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3 text-sm text-[var(--color-gc-text-secondary)]">
                        <div class="flex items-start gap-2">
                            <span class="text-[var(--color-gc-primary)]">1.</span>
                            <span>Pilih varian dan jumlah yang diinginkan</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-[var(--color-gc-primary)]">2.</span>
                            <span>Lakukan pembayaran dengan metode yang tersedia</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-[var(--color-gc-primary)]">3.</span>
                            <span>Produk akan dikirim sesuai jenis pengiriman</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-[var(--color-gc-primary)]">4.</span>
                            <span>Konfirmasi pesanan setelah menerima produk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reviews Section --}}
    <div class="gc-card p-4 md:p-6 mt-4">
        <h2 class="font-[var(--font-family-display)] text-lg font-bold text-[var(--color-gc-text)] mb-4">Ulasan & Rating</h2>

        {{-- Rating Summary --}}
        <div class="flex items-center gap-6 mb-6 pb-6 border-b border-[var(--color-gc-border)]">
            <div class="text-center">
                <div class="text-4xl font-bold text-[var(--color-gc-text)]">{{ number_format($product['rating'] ?? 0, 1) }}</div>
                <x-rating-stars :rating="$product['rating'] ?? 0" size="md" />
                <div class="text-sm text-[var(--color-gc-text-tertiary)] mt-1">{{ number_format($product['rating_count'] ?? 0) }} ulasan</div>
            </div>
        </div>

        {{-- Individual Reviews --}}
        <div class="space-y-4">
            @forelse($product['reviews'] ?? [] as $review)
                <div class="p-4 rounded-xl bg-[var(--color-gc-bg)]">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-full bg-[var(--color-gc-primary)]/20 flex items-center justify-center text-xs font-bold text-[var(--color-gc-primary)]">
                            {{ strtoupper(($review['user_name'] ?? 'A')[0]) }}
                        </div>
                        <div>
                            <span class="text-sm font-medium text-[var(--color-gc-text)]">{{ $review['user_name'] ?? 'Anonim' }}</span>
                            <x-rating-stars :rating="$review['rating'] ?? 0" size="xs" />
                        </div>
                        <span class="text-xs text-[var(--color-gc-text-tertiary)] ml-auto">{{ $review['date'] ?? '' }}</span>
                    </div>
                    <p class="text-sm text-[var(--color-gc-text-secondary)]">{{ $review['comment'] ?? '' }}</p>
                </div>
            @empty
                <p class="text-sm text-[var(--color-gc-text-tertiary)] text-center py-4">Belum ada ulasan untuk produk ini.</p>
            @endforelse
        </div>
    </div>

    {{-- Related Products --}}
    @if(($relatedProducts ?? [])->count() > 0)
        <section class="mt-6">
            <x-section-header
                title="Produk Serupa"
                linkText="Lihat Semua"
            />
            <div class="gc-product-grid">
                @foreach($relatedProducts as $related)
                    <x-product-card
                        :url="$related['url'] ?? '#'"
                        :image="$related['image'] ?? ''"
                        :name="$related['name'] ?? ''"
                        :gameName="$related['game_name'] ?? ''"
                        :price="$related['price'] ?? ''"
                        :originalPrice="$related['original_price'] ?? null"
                        :discount="$related['discount'] ?? null"
                        :rating="$related['rating'] ?? 0"
                        :soldCount="$related['sold_count'] ?? null"
                        :sellerName="$related['seller_name'] ?? ''"
                        :sellerVerified="$related['seller_verified'] ?? false"
                    />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Hidden checkout form --}}
    <form id="checkout-form" action="{{ route('cart.add') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product['id'] ?? '' }}">
        <input type="hidden" name="quantity" x-model="quantity">
        <input type="hidden" name="variant_id" x-model="selectedVariant">
    </form>
</div>
@endsection
