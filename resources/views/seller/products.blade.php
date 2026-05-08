@extends('layouts.seller', ['title' => 'Produk'])

@section('content')
<div x-data="sellerProducts()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Produk</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola semua produk toko Anda</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-icon" aria-label="Grid view">
                <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            </button>
            <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-icon" aria-label="Table view">
                <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <a href="{{ route('seller.products.create') }}" class="gc-btn gc-btn-primary gc-btn-md">
                <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Produk
            </a>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="gc-card p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" @input="filterProducts()" placeholder="Cari produk..." class="gc-search-input h-10">
            </div>
            <select x-model="filterType" @change="filterProducts()" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Tipe</option>
                <option value="top_up">Top Up</option>
                <option value="game_key">Game Key</option>
                <option value="item">Item</option>
                <option value="akun">Akun</option>
                <option value="voucher">Voucher</option>
                <option value="joki">Joki</option>
                <option value="koin">Koin</option>
            </select>
            <select x-model="filterStatus" @change="filterProducts()" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        {{-- Bulk Actions --}}
        <div x-show="selectedProducts.length > 0" x-transition class="mt-3 flex items-center gap-3 pt-3 border-t border-[var(--color-gc-border)]">
            <span class="text-sm text-[var(--color-gc-text-secondary)]" x-text="selectedProducts.length + ' produk dipilih'"></span>
            <div class="flex items-center gap-2" x-data="{ bulkOpen: false }">
                <button @click="bulkOpen = !bulkOpen" class="gc-btn gc-btn-outline gc-btn-sm">
                    Aksi Massal
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div x-show="bulkOpen" @click.away="bulkOpen = false" class="absolute mt-1 bg-[var(--color-gc-bg-elevated)] border border-[var(--color-gc-border)] rounded-xl shadow-2xl z-10 py-1 min-w-[160px]">
                    <button @click="bulkAction('activate'); bulkOpen = false" class="w-full px-4 py-2 text-left text-sm text-[var(--color-gc-text)] hover:bg-[var(--color-gc-hover)] transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Aktifkan
                    </button>
                    <button @click="bulkAction('deactivate'); bulkOpen = false" class="w-full px-4 py-2 text-left text-sm text-[var(--color-gc-text)] hover:bg-[var(--color-gc-hover)] transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                        Nonaktifkan
                    </button>
                    <div class="border-t border-[var(--color-gc-border)] my-1"></div>
                    <button @click="bulkAction('delete'); bulkOpen = false" class="w-full px-4 py-2 text-left text-sm text-[var(--color-gc-error)] hover:bg-[var(--color-gc-error)]/10 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Products Table View --}}
    <div x-show="viewMode === 'table'" class="gc-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="p-4 w-10"><input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-[var(--color-gc-border)] bg-[var(--color-gc-bg-card)]"></th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Produk</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Game</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Tipe</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Harga</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Stok</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Terjual</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 10; $i++)
                    @php
                        $statuses = ['active', 'inactive', 'draft'];
                        $types = ['top_up', 'game_key', 'item', 'akun', 'voucher'];
                        $typeLabels = ['top_up' => 'Top Up', 'game_key' => 'Game Key', 'item' => 'Item', 'akun' => 'Akun', 'voucher' => 'Voucher'];
                        $status = $statuses[$i % 3];
                        $type = $types[$i % 5];
                    @endphp
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4"><input type="checkbox" x-model="selectedProducts" value="{{ $i }}" class="rounded border-[var(--color-gc-border)] bg-[var(--color-gc-bg-card)]"></td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[var(--color-gc-bg-subtle)] flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                                <div>
                                    <div class="font-medium text-[var(--color-gc-text)]">Product {{ $i }}</div>
                                    <div class="text-xs text-[var(--color-gc-text-tertiary)]">SKU-{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">Mobile Legends</td>
                        <td class="p-4 gc-hide-mobile"><span class="gc-badge bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]">{{ $typeLabels[$type] }}</span></td>
                        <td class="p-4 font-semibold text-[var(--color-gc-accent)]">Rp {{ number_format(rand(50000, 500000), 0, ',', '.') }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text)]">{{ rand(0, 999) }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ rand(10, 500) }}</td>
                        <td class="p-4">
                            @if($status === 'active')
                            <span class="gc-badge bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]">Aktif</span>
                            @elseif($status === 'inactive')
                            <span class="gc-badge bg-[var(--color-gc-error)]/10 text-[var(--color-gc-error)]">Nonaktif</span>
                            @else
                            <span class="gc-badge bg-[var(--color-gc-text-tertiary)]/10 text-[var(--color-gc-text-tertiary)]">Draft</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('seller.products.edit', $i) }}" class="gc-btn gc-btn-ghost gc-btn-icon" aria-label="Edit">
                                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 21l.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </a>
                                <button class="gc-btn gc-btn-ghost gc-btn-icon text-[var(--color-gc-error)]" aria-label="Delete">
                                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-gc-border)]">
            <x-pagination :paginator="isset($products) ? $products : null" />
        </div>
    </div>

    {{-- Products Grid View --}}
    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @for($i = 1; $i <= 10; $i++)
        <div class="gc-card gc-card-hover group cursor-pointer">
            <div class="aspect-[3/4] bg-[var(--color-gc-bg-subtle)] relative overflow-hidden">
                <div class="flex items-center justify-center h-full text-[var(--color-gc-text-tertiary)]">
                    <svg class="w-12 h-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </div>
                <div class="absolute top-2 left-2"><span class="gc-badge bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]">Aktif</span></div>
            </div>
            <div class="p-3">
                <div class="text-xs text-[var(--color-gc-text-tertiary)] truncate">Mobile Legends</div>
                <div class="text-sm font-medium text-[var(--color-gc-text)] line-clamp-2">Product {{ $i }}</div>
                <div class="mt-2 flex items-end justify-between">
                    <div class="font-bold text-[var(--color-gc-accent)]">Rp {{ number_format(rand(50000, 500000), 0, ',', '.') }}</div>
                    <div class="text-xs text-[var(--color-gc-text-tertiary)]">{{ rand(10, 500) }} terjual</div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>

@push('scripts')
<script>
Alpine.data('sellerProducts', () => ({
    viewMode: 'table',
    search: '',
    filterType: '',
    filterStatus: '',
    selectAll: false,
    selectedProducts: [],
    filterProducts() {},
    toggleSelectAll() {
        this.selectedProducts = this.selectAll ? [1,2,3,4,5,6,7,8,9,10] : [];
    },
    bulkAction(action) {
        if (confirm(`Yakin ingin ${action === 'delete' ? 'menghapus' : action === 'activate' ? 'mengaktifkan' : 'menonaktifkan'} ${this.selectedProducts.length} produk?`)) {
            this.selectedProducts = [];
            this.selectAll = false;
        }
    }
}));
</script>
@endpush
@endsection