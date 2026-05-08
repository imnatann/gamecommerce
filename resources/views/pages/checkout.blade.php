@extends('layouts.app', ['title' => 'Checkout — GameCommerce'])

@section('content')
<div class="gc-page-wrapper" x-data="{ selectedPayment: '', voucherCode: '', voucherApplied: false }">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Keranjang', 'url' => route('cart')],
        ['label' => 'Checkout']
    ]" />

    <h1 class="font-[var(--font-family-display)] text-2xl md:text-3xl font-bold text-[var(--color-gc-text)] mb-6">Checkout</h1>

    <div class="gc-checkout-layout">
        {{-- Main Content --}}
        <div class="gc-checkout-main">
            {{-- Order Summary --}}
            <div class="gc-card p-4 md:p-6 mb-4">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Ringkasan Pesanan</h2>

                <div class="space-y-4">
                    @foreach($items ?? [] as $item)
                        <div class="flex gap-3 p-3 rounded-xl bg-[var(--color-gc-bg)]">
                            <img src="{{ $item['image'] ?? '' }}" alt="{{ $item['name'] ?? '' }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0" loading="lazy">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-[var(--color-gc-text)] truncate">{{ $item['name'] ?? '' }}</h3>
                                @if($item['game_name'] ?? '')
                                    <p class="text-xs text-[var(--color-gc-text-tertiary)]">{{ $item['game_name'] }}</p>
                                @endif
                                @if($item['variant'] ?? '')
                                    <p class="text-xs text-[var(--color-gc-text-secondary)]">{{ $item['variant'] }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-[var(--color-gc-text-tertiary)]">x{{ $item['quantity'] ?? 1 }}</span>
                                    <span class="text-sm font-bold text-[var(--color-gc-accent)]">{{ $item['price'] ?? 'Rp 0' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Voucher / Coupon --}}
            <div class="gc-card p-4 md:p-6 mb-4">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-3">Kode Voucher</h2>
                <div class="flex gap-2">
                    <input type="text" x-model="voucherCode" class="gc-input flex-1" placeholder="Masukkan kode voucher" :disabled="voucherApplied">
                    <button @click="voucherApplied = true" x-show="!voucherApplied" class="gc-btn gc-btn-primary gc-btn-sm">Terapkan</button>
                    <button @click="voucherApplied = false; voucherCode = ''" x-show="voucherApplied" class="gc-btn gc-btn-ghost gc-btn-sm text-[var(--color-gc-error)]">Hapus</button>
                </div>
                <template x-if="voucherApplied">
                    <p class="text-sm text-[var(--color-gc-accent)] mt-2">✓ Voucher berhasil diterapkan!</p>
                </template>
            </div>

            {{-- Payment Method Selection --}}
            <div class="gc-card p-4 md:p-6 mb-4">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Metode Pembayaran</h2>

                {{-- E-Wallet --}}
                <div class="mb-5">
                    <h3 class="text-sm font-semibold text-[var(--color-gc-text-secondary)] mb-2">E-Wallet</h3>
                    <div class="space-y-2">
                        @foreach($ewallets ?? [['id' => 'gopay', 'name' => 'GoPay'], ['id' => 'ovo', 'name' => 'OVO'], ['id' => 'dana', 'name' => 'DANA'], ['id' => 'shopeepay', 'name' => 'ShopeePay'], ['id' => 'linkaja', 'name' => 'LinkAja']] as $method)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[var(--color-gc-border)] hover:border-[var(--color-gc-primary)] cursor-pointer transition-colors" :class="selectedPayment === '{{ $method['id'] }}' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/5' : ''">
                                <input type="radio" x-model="selectedPayment" value="{{ $method['id'] }}" class="gc-radio">
                                <span class="text-sm text-[var(--color-gc-text)]">{{ $method['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Bank Transfer --}}
                <div class="mb-5">
                    <h3 class="text-sm font-semibold text-[var(--color-gc-text-secondary)] mb-2">Transfer Bank</h3>
                    <div class="space-y-2">
                        @foreach($banks ?? [['id' => 'bca', 'name' => 'BCA'], ['id' => 'bni', 'name' => 'BNI'], ['id' => 'bri', 'name' => 'BRI'], ['id' => 'mandiri', 'name' => 'Mandiri']] as $method)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[var(--color-gc-border)] hover:border-[var(--color-gc-primary)] cursor-pointer transition-colors" :class="selectedPayment === '{{ $method['id'] }}' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/5' : ''">
                                <input type="radio" x-model="selectedPayment" value="{{ $method['id'] }}" class="gc-radio">
                                <span class="text-sm text-[var(--color-gc-text)]">{{ $method['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Convenience Store --}}
                <div class="mb-5">
                    <h3 class="text-sm font-semibold text-[var(--color-gc-text-secondary)] mb-2">Gerai / Minimarket</h3>
                    <div class="space-y-2">
                        @foreach($convenience ?? [['id' => 'alfamart', 'name' => 'Alfamart'], ['id' => 'indomaret', 'name' => 'Indomaret']] as $method)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[var(--color-gc-border)] hover:border-[var(--color-gc-primary)] cursor-pointer transition-colors" :class="selectedPayment === '{{ $method['id'] }}' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/5' : ''">
                                <input type="radio" x-model="selectedPayment" value="{{ $method['id'] }}" class="gc-radio">
                                <span class="text-sm text-[var(--color-gc-text)]">{{ $method['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- QRIS --}}
                <div>
                    <h3 class="text-sm font-semibold text-[var(--color-gc-text-secondary)] mb-2">QRIS</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[var(--color-gc-border)] hover:border-[var(--color-gc-primary)] cursor-pointer transition-colors" :class="selectedPayment === 'qris' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/5' : ''">
                            <input type="radio" x-model="selectedPayment" value="qris" class="gc-radio">
                            <span class="text-sm text-[var(--color-gc-text)]">QRIS (Scan QR)</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Order Total --}}
        <div class="gc-checkout-sidebar">
            <div class="gc-card p-4 md:p-6 sticky top-4">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Detail Pembayaran</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[var(--color-gc-text-secondary)]">Subtotal ({{ ($totalItems ?? 0) . ' item' }})</span>
                        <span class="text-[var(--color-gc-text)]">{{ $subtotal ?? 'Rp 0' }}</span>
                    </div>

                    @if($discount ?? null)
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-gc-accent)]">Diskon</span>
                            <span class="text-[var(--color-gc-accent)]">-{{ $discount }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <span class="text-[var(--color-gc-text-secondary)]">Biaya Layanan</span>
                        <span class="text-[var(--color-gc-text)]">{{ $serviceFee ?? 'Rp 0' }}</span>
                    </div>

                    <div class="pt-3 mt-3 border-t border-[var(--color-gc-border)]">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-[var(--color-gc-text)]">Total</span>
                            <span class="gc-price gc-price-large text-[var(--color-gc-accent)]">{{ $total ?? 'Rp 0' }}</span>
                        </div>
                    </div>
                </div>

                <button :disabled="!selectedPayment" class="gc-btn gc-btn-accent gc-btn-lg w-full mt-6 font-bold" :class="{ 'opacity-50 cursor-not-allowed': !selectedPayment }">
                    Bayar Sekarang
                </button>

                <div class="flex items-center justify-center gap-4 mt-4">
                    <x-trust-badge icon="shield" title="Transaksi Aman" description="" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
