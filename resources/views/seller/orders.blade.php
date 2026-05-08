@extends('layouts.seller', ['title' => 'Pesanan'])

@section('content')
<div x-data="sellerOrders()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Pesanan</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola pesanan masuk Anda</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="gc-card mb-6">
        <div class="flex items-center gap-1 p-2 overflow-x-auto">
            <template x-for="tab in tabs" :key="tab.value">
                <button @click="activeTab = tab.value" :class="activeTab === tab.value ? 'bg-[var(--color-gc-primary)] text-white shadow-[var(--shadow-gc-glow)]' : 'text-[var(--color-gc-text-secondary)] hover:bg-[var(--color-gc-hover)]'" class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition-all" x-text="tab.label + (tab.count ? ' (' + tab.count + ')' : '')"></button>
            </template>
        </div>
        <div class="p-4 border-t border-[var(--color-gc-border)]">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" x-model="search" placeholder="Cari ID pesanan atau nama pembeli..." class="gc-search-input h-10">
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="gc-card overflow-hidden">
        <div class="overflow-x-auto">
            @if(isset($orders) && count($orders) > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Order ID</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Pembeli</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Produk</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Jumlah</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Tanggal</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4 font-mono text-[var(--color-gc-primary)]">#{{ $order->id ?? $loop->index + 1 }}</td>
                        <td class="p-4 text-[var(--color-gc-text)]">{{ $order->buyer_name ?? 'User' }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ $order->product_name ?? 'Product' }}</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-accent)]">{{ $order->amount_formatted ?? 'Rp 0' }}</td>
                        <td class="p-4"><x-order-status-badge :status="$order->status ?? 'processing'" /></td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ $order->created_at ?? '-' }}</td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @if(($order->status ?? 'pending') === 'pending')
                                <button class="gc-btn gc-btn-xs gc-btn-accent">Proses</button>
                                @elseif(($order->status ?? '') === 'processing')
                                <button class="gc-btn gc-btn-xs gc-btn-primary">Kirim</button>
                                @endif
                                <a href="#" class="gc-btn gc-btn-ghost gc-btn-xs">Detail</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-16 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[var(--color-gc-bg-subtle)] flex items-center justify-center">
                    <svg class="w-8 h-8 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-1">Belum Ada Pesanan</h3>
                <p class="text-sm text-[var(--color-gc-text-secondary)]">Pesanan yang masuk akan muncul di sini.</p>
            </div>
            @endif
        </div>
        @if(isset($orders) && method_exists($orders, 'hasPages') && $orders->hasPages())
        <div class="p-4 border-t border-[var(--color-gc-border)]">
            <x-pagination :paginator="$orders" />
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
Alpine.data('sellerOrders', () => ({
    activeTab: 'all',
    search: '',
    tabs: [
        { label: 'Semua', value: 'all', count: null },
        { label: 'Menunggu', value: 'pending', count: null },
        { label: 'Diproses', value: 'processing', count: null },
        { label: 'Dikirim', value: 'delivered', count: null },
        { label: 'Selesai', value: 'completed', count: null },
        { label: 'Dispute', value: 'disputed', count: null },
    ],
}));
</script>
@endpush
@endsection