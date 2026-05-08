@extends('layouts.app', ['title' => 'Status Pesanan #' . ($order['id'] ?? '') . ' — GameCommerce'])

@section('content')
<div class="gc-page-wrapper" x-data="{ activeTab: 'detail' }">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Pesanan', 'url' => route('profile.orders')],
        ['label' => 'Pesanan #' . ($order['id'] ?? '')]
    ]" />

    <div class="gc-card p-4 md:p-6 mb-4">
        {{-- Order ID + Status --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-[var(--font-family-display)] text-xl md:text-2xl font-bold text-[var(--color-gc-text)]">Pesanan #{{ $order['id'] ?? '' }}</h1>
                <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">{{ $order['created_at'] ?? '' }}</p>
            </div>
            <x-order-status-badge :status="$order['status'] ?? 'pending'" />
        </div>

        {{-- Progress Timeline --}}
        <div class="gc-order-timeline mb-8">
            @php
                $steps = [
                    ['key' => 'pending', 'label' => 'Pending', 'icon' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'],
                    ['key' => 'paid', 'label' => 'Dibayar', 'icon' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a2 2 0 0 1 0 4H6a2 2 0 0 0 0 4h14a2 2 0 0 1 0 4H6a2 2 0 0 1-1-1v-3"/></svg>'],
                    ['key' => 'processing', 'label' => 'Diproses', 'icon' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4m0 12v4m-7.07-3.93l2.83-2.83m8.48-8.48l2.83-2.83M2 12h4m12 0h4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83"/></svg>'],
                    ['key' => 'delivered', 'label' => 'Dikirim', 'icon' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7 17 2 2 4-4"/><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>'],
                    ['key' => 'completed', 'label' => 'Selesai', 'icon' => '<svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'],
                ];
                $statusOrder = ['pending' => 0, 'paid' => 1, 'processing' => 2, 'delivered' => 3, 'completed' => 4];
                $currentStep = $statusOrder[$order['status'] ?? 'pending'] ?? 0;
            @endphp

            <div class="flex items-start justify-between relative">
                {{-- Connecting Line --}}
                <div class="absolute top-5 left-0 right-0 h-0.5 bg-[var(--color-gc-border)]">
                    <div class="h-full bg-[var(--color-gc-accent)] transition-all duration-500" style="width: {{ min($currentStep / max(count($steps) - 1, 1) * 100, 100) }}%"></div>
                </div>

                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center relative z-10 flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $index <= $currentStep ? 'bg-[var(--color-gc-accent)] text-white' : 'bg-[var(--color-gc-hover)] text-[var(--color-gc-text-tertiary)]' }}">
                            {!! $step['icon'] !!}
                        </div>
                        <span class="text-xs mt-2 text-center {{ $index <= $currentStep ? 'text-[var(--color-gc-text)] font-medium' : 'text-[var(--color-gc-text-tertiary)]' }}">{{ $step['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="gc-checkout-layout">
        <div class="gc-checkout-main">
            {{-- Order Items --}}
            <div class="gc-card p-4 md:p-6 mb-4">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Item Pesanan</h2>

                <div class="space-y-4">
                    @foreach($order['items'] ?? [] as $item)
                        <div class="flex gap-3 p-3 rounded-xl bg-[var(--color-gc-bg)]">
                            <img src="{{ $item['image'] ?? '' }}" alt="{{ $item['name'] ?? '' }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0" loading="lazy">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-[var(--color-gc-text)] truncate">{{ $item['name'] ?? '' }}</h3>
                                @if($item['game_name'] ?? '')
                                    <p class="text-xs text-[var(--color-gc-text-tertiary)]">{{ $item['game_name'] }}</p>
                                @endif
                                @if($item['variant'] ?? '')
                                    <p class="text-xs text-[var(--color-gc-text-secondary)]">{{ $item['variant'] }}</p>
                                @endif
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-[var(--color-gc-text-tertiary)]">x{{ $item['quantity'] ?? 1 }}</span>
                                    <span class="text-sm font-bold text-[var(--color-gc-accent)]">{{ $item['price'] ?? 'Rp 0' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Delivery Info --}}
            <div class="gc-card p-4 md:p-6 mb-4">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Informasi Pengiriman</h2>

                @if(($order['delivery_type'] ?? '') === 'instant' && ($order['status'] ?? '') === 'delivered')
                    {{-- Auto delivery with code/key visible --}}
                    <div class="p-4 rounded-xl bg-[var(--color-gc-accent)]/10 border border-[var(--color-gc-accent)]/30">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <span class="font-semibold text-[var(--color-gc-accent)]">Kode Produk</span>
                        </div>
                        @foreach($order['delivery_codes'] ?? [] as $code)
                            <div class="flex items-center gap-2 bg-[var(--color-gc-bg)] p-3 rounded-lg mt-2">
                                <code class="flex-1 text-sm font-mono text-[var(--color-gc-text)] select-all break-all">{{ $code['code'] ?? $code }}</code>
                                <button onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent)" class="gc-btn gc-btn-ghost gc-btn-sm flex-shrink-0">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @elseif(($order['delivery_type'] ?? '') === 'instant')
                    <div class="p-4 rounded-xl bg-[var(--color-gc-info)]/10 border border-[var(--color-gc-info)]/30">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[var(--color-gc-info)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span class="text-sm text-[var(--color-gc-info)]">Kode produk akan ditampilkan setelah pembayaran dikonfirmasi.</span>
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-[var(--color-gc-warning)]/10 border border-[var(--color-gc-warning)]/30">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span class="text-sm text-[var(--color-gc-warning)]">Menunggu pengiriman dari penjual. Harap tunggu.</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar: Order Summary --}}
        <div class="gc-checkout-sidebar">
            <div class="gc-card p-4 md:p-6 sticky top-4">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Rincian Pembayaran</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-[var(--color-gc-text-secondary)]">Subtotal</span>
                        <span class="text-[var(--color-gc-text)]">{{ $order['subtotal'] ?? 'Rp 0' }}</span>
                    </div>
                    @if($order['discount'] ?? '')
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-gc-accent)]">Diskon</span>
                            <span class="text-[var(--color-gc-accent)]">-{{ $order['discount'] }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-[var(--color-gc-text-secondary)]">Biaya Layanan</span>
                        <span class="text-[var(--color-gc-text)]">{{ $order['service_fee'] ?? 'Rp 0' }}</span>
                    </div>
                    <div class="pt-3 mt-3 border-t border-[var(--color-gc-border)]">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-[var(--color-gc-text)]">Total</span>
                            <span class="gc-price gc-price-large text-[var(--color-gc-accent)]">{{ $order['total'] ?? 'Rp 0' }}</span>
                        </div>
                    </div>
                    <div class="pt-2">
                        <span class="text-xs text-[var(--color-gc-text-tertiary)]">Metode: {{ $order['payment_method'] ?? '-' }}</span>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-6">
                    @if(($order['status'] ?? '') === 'completed')
                        <a href="{{ $order['product_url'] ?? '#' }}" class="gc-btn gc-btn-accent gc-btn-md w-full text-center">Beli Lagi</a>
                    @endif

                    @if(in_array($order['status'] ?? '', ['pending', 'paid', 'processing', 'delivered']))
                        <button class="gc-btn gc-btn-ghost gc-btn-md w-full text-[var(--color-gc-error)]">Ajukan Keluhan</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
