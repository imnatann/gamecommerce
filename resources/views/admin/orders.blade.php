@extends('layouts.admin', ['title' => 'Pesanan'])

@section('content')
<div x-data="adminOrders()">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Pesanan</h1>
        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola dan pantau semua pesanan platform</p>
    </div>

    {{-- Filters --}}
    <div class="gc-card p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" placeholder="Cari ID pesanan, pembeli, atau seller..." class="gc-search-input h-10">
            </div>
            <select x-model="filterStatus" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Status</option>
                <option value="pending">Menunggu</option>
                <option value="processing">Diproses</option>
                <option value="delivered">Dikirim</option>
                <option value="completed">Selesai</option>
                <option value="disputed">Dispute</option>
                <option value="cancelled">Dibatalkan</option>
            </select>
            <select x-model="filterGame" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Game</option>
                <option>Mobile Legends</option>
                <option>Free Fire</option>
                <option>Genshin Impact</option>
                <option>PUBG Mobile</option>
                <option>Valorant</option>
            </select>
            <input type="date" x-model="filterDateStart" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]" placeholder="Dari">
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="gc-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Order ID</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Pembeli</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Seller</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Produk</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Jumlah</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Tanggal</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 20; $i++)
                    @php
                        $statuses = ['pending', 'processing', 'delivered', 'completed', 'disputed', 'cancelled'];
                        $buyerNames = ['Ahmad', 'Siti', 'Budi', 'Rina', 'Joko', 'Maya', 'Rizky', 'Dewi', 'Fajar', 'Anisa', 'Tono', 'Lina', 'Eko', 'Fitri', 'Agus', 'Yuni', 'Hadi', 'Nana', 'Umar', 'Ratna'];
                        $sellerNames = ['GameStore Pro', 'JokiMaster', 'AkunGame', 'TopUp Murah', 'VGK Shop'];
                        $products = ['86 Diamond ML', '120 UC PUBG', 'Rising Star Package', 'Weekly Diamond Pass', 'Prime Plus ML', '280 UC PUBG', 'Genesis Crystal 60', 'SS Rank FF', 'Key Game Steam', 'Akun ML Sapphire'];
                        $status = $statuses[$i % 6];
                    @endphp
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors cursor-pointer" @click="showDetail = true">
                        <td class="p-4 font-mono text-[var(--color-gc-primary)]">#ORD{{ str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="p-4 text-[var(--color-gc-text)]">{{ $buyerNames[$i-1] }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ $sellerNames[$i % 5] }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text)]">{{ $products[$i % 10] }}</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-accent)]">Rp {{ number_format(rand(20000, 500000), 0, ',', '.') }}</td>
                        <td class="p-4"><x-order-status-badge :status="$status" /></td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ now()->subDays(rand(0, 14))->format('d M H:i') }}</td>
                        <td class="p-4 text-right">
                            <button @click.stop="showDetail = true" class="gc-btn gc-btn-ghost gc-btn-xs">Detail</button>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-gc-border)]">
            <x-pagination />
        </div>
    </div>

    {{-- Order Detail Modal --}}
    <div x-show="showDetail" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto" @keydown.escape.prevent="showDetail = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showDetail = false"></div>
        <div x-show="showDetail" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="gc-modal-panel max-w-2xl relative bg-[var(--color-gc-bg-elevated)] my-8">
            <div class="gc-modal-header">
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Detail Pesanan #ORD12345</h3>
                <button @click="showDetail = false" class="gc-btn-icon gc-btn-ghost" aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="gc-modal-body space-y-6">
                {{-- Order Info */}
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-xs text-[var(--color-gc-text-tertiary)]">Pembeli</span><div class="text-sm font-medium text-[var(--color-gc-text)] mt-0.5">Ahmad Fauzi</div></div>
                    <div><span class="text-xs text-[var(--color-gc-text-tertiary)]">Penjual</span><div class="text-sm font-medium text-[var(--color-gc-text)] mt-0.5">GameStore Pro</div></div>
                    <div><span class="text-xs text-[var(--color-gc-text-tertiary)]">Jumlah</span><div class="text-sm font-bold text-[var(--color-gc-accent)] font-[var(--font-family-display)] mt-0.5">Rp 150.000</div></div>
                    <div><span class="text-xs text-[var(--color-gc-text-tertiary)]">Tanggal</span><div class="text-sm text-[var(--color-gc-text)] mt-0.5">15 Jan 2025, 14:30</div></div>
                </div>

                {{-- Items --}}
                <div class="border border-[var(--color-gc-border)] rounded-xl overflow-hidden">
                    <div class="bg-[var(--color-gc-bg-subtle)] px-4 py-2 text-xs font-medium text-[var(--color-gc-text-secondary)]">ITEMS</div>
                    <div class="p-4 flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-[var(--color-gc-bg-subtle)] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" x2="7.01" y1="7" y2="7"/></svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-[var(--color-gc-text)]">86 Diamond Mobile Legends</div>
                            <div class="text-xs text-[var(--color-gc-text-tertiary)]">Mobile Legends • Top Up • Server: Indonesia</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-[var(--color-gc-accent)]">Rp 150.000</div>
                            <div class="text-xs text-[var(--color-gc-text-tertiary)]">x1</div>
                        </div>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="border border-[var(--color-gc-border)] rounded-xl overflow-hidden">
                    <div class="bg-[var(--color-gc-bg-subtle)] px-4 py-2 text-xs font-medium text-[var(--color-gc-text-secondary)]">PEMBAYARAN</div>
                    <div class="p-4 space-y-2">
                        <div class="flex justify-between text-sm"><span class="text-[var(--color-gc-text-secondary)]">Metode</span><span class="text-[var(--color-gc-text)]">QRIS</span></div>
                        <div class="flex justify-between text-sm"><span class="text-[var(--color-gc-text-secondary)]">Harga</span><span class="text-[var(--color-gc-text)]">Rp 150.000</span></div>
                        <div class="flex justify-between text-sm"><span class="text-[var(--color-gc-text-secondary)]">Biaya Layanan</span><span class="text-[var(--color-gc-text)]">Rp 3.000</span></div>
                        <div class="border-t border-[var(--color-gc-border)] pt-2 flex justify-between text-sm font-semibold"><span class="text-[var(--color-gc-text)]">Total</span><span class="text-[var(--color-gc-accent)]">Rp 153.000</span></div>
                    </div>
                </div>

                {{-- Delivery Data --}}
                <div class="border border-[var(--color-gc-border)] rounded-xl overflow-hidden">
                    <div class="bg-[var(--color-gc-bg-subtle)] px-4 py-2 text-xs font-medium text-[var(--color-gc-text-secondary)]">DATA DELIVERY</div>
                    <div class="p-4 space-y-2">
                        <div class="flex justify-between text-sm"><span class="text-[var(--color-gc-text-secondary)]">User ID</span><span class="font-mono text-[var(--color-gc-text)]">123456789</span></div>
                        <div class="flex justify-between text-sm"><span class="text-[var(--color-gc-text-secondary)]">Server</span><span class="text-[var(--color-gc-text)]">Indonesia (ID)</span></div>
                        <div class="flex justify-between text-sm"><span class="text-[var(--color-gc-text-secondary)]">Tipe</span><span class="text-[var(--color-gc-text)]">Instant Delivery</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('adminOrders', () => ({
    search: '',
    filterStatus: '',
    filterGame: '',
    filterDateStart: '',
    showDetail: false,
}));
</script>
@endpush
@endsection