@extends('layouts.admin', ['title' => 'Dispute'])

@section('content')
<div x-data="adminDisputes()">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Dispute</h1>
        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Tinjau dan selesaikan dispute antara pembeli dan penjual</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="gc-card p-4 text-center cursor-pointer" :class="activeTab === 'open' ? 'border-[var(--color-gc-error)]' : ''" @click="activeTab = 'open'">
            <div class="text-2xl font-bold font-[var(--font-family-display)] text-[var(--color-gc-error)]">7</div>
            <div class="text-xs text-[var(--color-gc-text-secondary)]">Terbuka</div>
        </div>
        <div class="gc-card p-4 text-center cursor-pointer" :class="activeTab === 'review' ? 'border-[var(--color-gc-warning)]' : ''" @click="activeTab = 'review'">
            <div class="text-2xl font-bold font-[var(--font-family-display)] text-[var(--color-gc-warning)]">3</div>
            <div class="text-xs text-[var(--color-gc-text-secondary)]">Ditinjau</div>
        </div>
        <div class="gc-card p-4 text-center cursor-pointer" :class="activeTab === 'resolved' ? 'border-[var(--color-gc-accent)]' : ''" @click="activeTab = 'resolved'">
            <div class="text-2xl font-bold font-[var(--font-family-display)] text-[var(--color-gc-accent)]">45</div>
            <div class="text-xs text-[var(--color-gc-text-secondary)]">Diselesaikan</div>
        </div>
        <div class="gc-card p-4 text-center cursor-pointer" :class="activeTab === 'closed' ? 'border-[var(--color-gc-text-tertiary)]' : ''" @click="activeTab = 'closed'">
            <div class="text-2xl font-bold font-[var(--font-family-display)] text-[var(--color-gc-text-secondary)]">12</div>
            <div class="text-xs text-[var(--color-gc-text-secondary)]">Ditutup</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="gc-card p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" placeholder="Cari dispute ID, pembeli, atau penjual..." class="gc-search-input h-10">
            </div>
        </div>
    </div>

    {{-- Disputes List --}}
    <div class="space-y-4">
        @for($i = 1; $i <= 5; $i++)
        @php
            $disputeStatuses = ['open', 'review', 'resolved'];
            $disputeStatus = $disputeStatuses[$i % 3];
            $statusLabels = ['open' => 'Terbuka', 'review' => 'Ditinjau', 'resolved' => 'Diselesaikan'];
            $statusClasses = ['open' => 'bg-[var(--color-gc-error)]/10 text-[var(--color-gc-error)]', 'review' => 'bg-[var(--color-gc-warning)]/10 text-[var(--color-gc-warning)]', 'resolved' => 'bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]'];
            $reasons = ['Produk tidak sesuai deskripsi', 'Pengiriman terlalu lama', 'Akun tidak bisa diakses', 'Voucher sudah digunakan', 'Item tidak diterima'];
            $buyerNames = ['Ahmad Rizki', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Kartika', 'Eko Prasetyo'];
            $sellerNames = ['GameShop Pro', 'ML TopUp Store', 'FF Diamond Shop', 'Genshin Mart', 'GameKey Store'];
        @endphp
        <div class="gc-card p-5 cursor-pointer hover:border-[var(--color-gc-primary)]/50 transition-colors" @click="showDetail = true">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-mono text-sm text-[var(--color-gc-primary)]">#DSP{{ str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="gc-badge {{ $statusClasses[$disputeStatus] }}">{{ $statusLabels[$disputeStatus] }}</span>
                    </div>
                    <h3 class="text-sm font-medium text-[var(--color-gc-text)] mb-1">{{ $reasons[$i - 1] }}</h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-[var(--color-gc-text-secondary)]">
                        <span>Pembeli: <strong class="text-[var(--color-gc-text)]">{{ $buyerNames[$i - 1] }}</strong></span>
                        <span>Penjual: <strong class="text-[var(--color-gc-text)]">{{ $sellerNames[$i - 1] }}</strong></span>
                        <span>Order: <strong class="text-[var(--color-gc-primary)]">#ORD{{ rand(10000, 99999) }}</strong></span>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <div class="text-sm font-semibold text-[var(--color-gc-accent)]">Rp {{ number_format(rand(50000, 500000), 0, ',', '.') }}</div>
                    <div class="text-xs text-[var(--color-gc-text-tertiary)]">{{ now()->subDays($i)->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        @endfor
    </div>

    {{-- Dispute Detail Modal --}}
    <div x-show="showDetail" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto" @keydown.escape.prevent="showDetail = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showDetail = false"></div>
        <div x-show="showDetail" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="gc-modal-panel max-w-3xl relative bg-[var(--color-gc-bg-elevated)] my-8">
            <div class="gc-modal-header">
                <div class="flex items-center gap-2">
                    <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Detail Dispute #DSP0001</h3>
                    <span class="gc-badge bg-[var(--color-gc-error)]/10 text-[var(--color-gc-error)]">Terbuka</span>
                </div>
                <button @click="showDetail = false" class="gc-btn-icon gc-btn-ghost" aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="gc-modal-body space-y-6 max-h-[70vh] overflow-y-auto">
                {{-- Order Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="gc-card p-3">
                        <div class="text-xs text-[var(--color-gc-text-tertiary)] mb-1">Info Order</div>
                        <div class="font-mono text-sm text-[var(--color-gc-primary)]">#ORD12345</div>
                        <div class="text-sm text-[var(--color-gc-text)] mt-1">86 Diamond ML</div>
                        <div class="text-sm font-semibold text-[var(--color-gc-accent)]">Rp 250.000</div>
                    </div>
                    <div class="gc-card p-3">
                        <div class="text-xs text-[var(--color-gc-text-tertiary)] mb-1">Pembeli</div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Ahmad Rizki</div>
                        <div class="text-xs text-[var(--color-gc-text-secondary)]">ahmad@email.com</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Dispute: 2</div>
                    </div>
                    <div class="gc-card p-3">
                        <div class="text-xs text-[var(--color-gc-text-tertiary)] mb-1">Penjual</div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">GameShop Pro</div>
                        <div class="text-xs text-[var(--color-gc-text-secondary)]">shop@email.com</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Rating: 4.8 ★</div>
                    </div>
                </div>

                {{-- Dispute Reason --}}
                <div>
                    <h4 class="text-sm font-medium text-[var(--color-gc-text-secondary)] mb-2">Alasan Dispute</h4>
                    <div class="gc-card p-4">
                        <div class="text-[var(--color-gc-text)] text-sm">Produk tidak sesuai deskripsi. Saya membeli 86 diamond tapi yang masuk hanya 50 diamond. Sudah cek berulang kali dan screenshot bukti sudah dikirim.</div>
                    </div>
                </div>

                {{-- Evidence / Images --}}
                <div>
                    <h4 class="text-sm font-medium text-[var(--color-gc-text-secondary)] mb-2">Bukti</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="aspect-square rounded-xl bg-[var(--color-gc-bg-subtle)] border border-[var(--color-gc-border)] flex items-center justify-center cursor-pointer hover:border-[var(--color-gc-primary)] transition-colors">
                            <svg class="w-8 h-8 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        </div>
                        <div class="aspect-square rounded-xl bg-[var(--color-gc-bg-subtle)] border border-[var(--color-gc-border)] flex items-center justify-center cursor-pointer hover:border-[var(--color-gc-primary)] transition-colors">
                            <svg class="w-8 h-8 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        </div>
                        <div class="aspect-square rounded-xl bg-[var(--color-gc-bg-subtle)] border border-dashed border-[var(--color-gc-border)] flex items-center justify-center cursor-pointer hover:border-[var(--color-gc-primary)] transition-colors">
                            <svg class="w-6 h-6 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Message Thread --}}
                <div>
                    <h4 class="text-sm font-medium text-[var(--color-gc-text-secondary)] mb-3">Percakapan</h4>
                    <div class="space-y-4">
                        {{-- Buyer Message --}}
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-[var(--color-gc-primary)]/10 flex items-center justify-center text-xs font-semibold text-[var(--color-gc-primary)] flex-shrink-0">A</div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-[var(--color-gc-text)]">Ahmad Rizki</span>
                                    <span class="text-xs text-[var(--color-gc-text-tertiary)]">Pembeli</span>
                                    <span class="text-xs text-[var(--color-gc-text-tertiary)]">• 15 Jan, 14:35</span>
                                </div>
                                <div class="gc-card p-3">
                                    <p class="text-sm text-[var(--color-gc-text)]">Saya sudah menunggu 2 hari dan diamond belum masuk. Mohon dicek dan dikirim segera. Terima kasih.</p>
                                </div>
                            </div>
                        </div>
                        {{-- Seller Message --}}
                        <div class="flex gap-3 flex-row-reverse">
                            <div class="w-8 h-8 rounded-full bg-[var(--color-gc-accent)]/10 flex items-center justify-center text-xs font-semibold text-[var(--color-gc-accent)] flex-shrink-0">G</div>
                            <div class="flex-1 text-right">
                                <div class="flex items-center gap-2 mb-1 justify-end">
                                    <span class="text-xs text-[var(--color-gc-text-tertiary)]">16 Jan, 09:12 •</span>
                                    <span class="text-xs text-[var(--color-gc-text-secondary)]">Penjual</span>
                                    <span class="text-sm font-medium text-[var(--color-gc-text)]">GameShop Pro</span>
                                </div>
                                <div class="gc-card p-3 text-left">
                                    <p class="text-sm text-[var(--color-gc-text)]">Mohon maaf atas ketidaknyamanan. Kami sudah cek dan ada kendala teknis. Akan segera diproses dalam 1x24 jam.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Admin Reply Input --}}
                <div class="border-t border-[var(--color-gc-border)] pt-4">
                    <h4 class="text-sm font-medium text-[var(--color-gc-text-secondary)] mb-2">Balasan Admin</h4>
                    <textarea rows="3" placeholder="Tulis balasan sebagai admin..." class="w-full px-3 py-2 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20 resize-none"></textarea>
                </div>
            </div>

            {{-- Resolution Actions --}}
            <div class="gc-modal-footer border-t border-[var(--color-gc-border)]">
                <div class="flex flex-col sm:flex-row gap-2 w-full">
                    <button @click="showDetail = false" class="gc-btn gc-btn-ghost gc-btn-md flex-1">Batal</button>
                    <button @click="showDetail = false" class="gc-btn gc-btn-md flex-1 border border-[var(--color-gc-error)] text-[var(--color-gc-error)] hover:bg-[var(--color-gc-error)]/10 transition-colors">
                        Refund ke Pembeli
                    </button>
                    <button @click="showDetail = false" class="gc-btn gc-btn-md flex-1 border border-[var(--color-gc-accent)] text-[var(--color-gc-accent)] hover:bg-[var(--color-gc-accent)]/10 transition-colors">
                        Lepas ke Penjual
                    </button>
                    <button @click="showDetail = false" class="gc-btn gc-btn-primary gc-btn-md flex-1">
                        Refund Sebagian
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('adminDisputes', () => ({
    activeTab: 'open',
    search: '',
    showDetail: false,
}));
</script>
@endpush
@endsection