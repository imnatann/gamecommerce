@extends('layouts.admin', ['title' => 'Manajemen Voucher'])

@section('content')
<div x-data="adminVouchers()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Voucher</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Kelola voucher promo dan diskon</p>
        </div>
        <button @click="showAddModal = true" class="gc-btn gc-btn-primary gc-btn-md">
            <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Voucher
        </button>
    </div>

    {{-- Search & Filter --}}
    <div class="gc-card p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" x-model="search" placeholder="Cari kode voucher..." class="gc-search-input h-10">
            </div>
            <select x-model="filterType" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Tipe</option>
                <option value="percentage">Persentase</option>
                <option value="fixed">Nominal Tetap</option>
                <option value="free_shipping">Gratis Ongkir</option>
            </select>
            <select x-model="filterStatus" class="h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
                <option value="expired">Kedaluwarsa</option>
            </select>
        </div>
    </div>

    {{-- Vouchers Table --}}
    <div class="gc-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Kode</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Tipe</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Diskon</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Min. Pembelian</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Penggunaan</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Periode</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        ['code' => 'GGDEAL2025', 'type' => 'percentage', 'discount' => '50%', 'min' => 'Rp 100.000', 'used' => 234, 'max' => 500, 'start' => '01 Jan', 'end' => '31 Jan', 'active' => true],
                        ['code' => 'NEWMEMBER', 'type' => 'fixed', 'discount' => 'Rp 25.000', 'min' => 'Rp 50.000', 'used' => 1023, 'max' => 0, 'start' => '01 Des', 'end' => '28 Feb', 'active' => true],
                        ['code' => 'MLBB50', 'type' => 'percentage', 'discount' => '15%', 'min' => 'Rp 0', 'used' => 56, 'max' => 100, 'start' => '10 Jan', 'end' => '20 Jan', 'active' => true],
                        ['code' => 'FREEONGKIR', 'type' => 'free_shipping', 'discount' => 'Gratis', 'min' => 'Rp 200.000', 'used' => 89, 'max' => 200, 'start' => '01 Jan', 'end' => '31 Mar', 'active' => true],
                        ['code' => 'WINTER24', 'type' => 'percentage', 'discount' => '30%', 'min' => 'Rp 75.000', 'used' => 500, 'max' => 500, 'start' => '01 Des', 'end' => '31 Des', 'active' => false],
                        ['code' => 'VALO10K', 'type' => 'fixed', 'discount' => 'Rp 10.000', 'min' => 'Rp 0', 'used' => 345, 'max' => 1000, 'start' => '15 Jan', 'end' => '15 Feb', 'active' => true],
                    ] as $voucher)
                    @php
                        $typeLabels = ['percentage' => 'Persentase', 'fixed' => 'Nominal', 'free_shipping' => 'Gratis Ongkir'];
                        $typeClasses = ['percentage' => 'bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]', 'fixed' => 'bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]', 'free_shipping' => 'bg-[var(--color-gc-warning)]/10 text-[var(--color-gc-warning)]'];
                    @endphp
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4 font-mono font-semibold text-[var(--color-gc-primary)]">{{ $voucher['code'] }}</td>
                        <td class="p-4"><span class="gc-badge {{ $typeClasses[$voucher['type']] }}">{{ $typeLabels[$voucher['type']] }}</span></td>
                        <td class="p-4 font-semibold text-[var(--color-gc-accent)]">{{ $voucher['discount'] }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text)]">{{ $voucher['min'] }}</td>
                        <td class="p-4 gc-hide-mobile">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-[var(--color-gc-text)]">{{ $voucher['used'] }}</span>
                                @if($voucher['max'] > 0)
                                <span class="text-xs text-[var(--color-gc-text-tertiary)]">/ {{ $voucher['max'] }}</span>
                                <div class="flex-1 h-1.5 bg-[var(--color-gc-bg-subtle)] rounded-full overflow-hidden">
                                    <div class="h-full bg-[var(--color-gc-primary)] rounded-full" style="width: {{ round(($voucher['used'] / $voucher['max']) * 100) }}%"></div>
                                </div>
                                @else
                                <span class="text-xs text-[var(--color-gc-text-tertiary)]">∞</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 gc-hide-mobile text-xs text-[var(--color-gc-text-secondary)]">{{ $voucher['start'] }} - {{ $voucher['end'] }}</td>
                        <td class="p-4">
                            <button @click="$data.active = !$data.active" x-data="{ active: {{ $voucher['active'] ? 'true' : 'false' }} }" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="active ? 'bg-[var(--color-gc-accent)]' : 'bg-[var(--color-gc-border)]'" role="switch" :aria-checked="active.toString()">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform" :class="active ? 'translate-x-6' : 'translate-x-0.5'"></span>
                            </button>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button class="gc-btn gc-btn-ghost gc-btn-icon" aria-label="Statistics" title="Statistik">
                                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                                </button>
                                <button class="gc-btn gc-btn-ghost gc-btn-icon" aria-label="Edit">
                                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 21l.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                                <button class="gc-btn gc-btn-ghost gc-btn-icon text-[var(--color-gc-error)]" aria-label="Delete">
                                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--color-gc-border)]">
            <x-pagination :paginator="isset($vouchers) ? $vouchers : null" />
        </div>
    </div>

    {{-- Add/Edit Voucher Modal --}}
    <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.prevent="showAddModal = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="gc-modal-panel max-w-lg relative bg-[var(--color-gc-bg-elevated)]">
            <div class="gc-modal-header">
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]" x-text="editingVoucher ? 'Edit Voucher' : 'Tambah Voucher'">Tambah Voucher</h3>
                <button @click="showAddModal = false; editingVoucher = null" class="gc-btn-icon gc-btn-ghost" aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="gc-modal-body space-y-4">
                <div>
                    <label for="voucher_code" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Kode Voucher <span class="text-[var(--color-gc-error)]">*</span></label>
                    <div class="flex gap-2">
                        <input type="text" id="voucher_code" x-model="form.code" class="flex-1 h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] font-mono uppercase focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" placeholder="GGDEAL2025" maxlength="20">
                        <button type="button" @click="form.code = generateCode()" class="gc-btn gc-btn-outline gc-btn-sm">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Tipe Diskon <span class="text-[var(--color-gc-error)]">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" @click="form.type = 'percentage'" :class="form.type === 'percentage' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]' : 'border-[var(--color-gc-border)] text-[var(--color-gc-text-secondary)]'" class="p-3 rounded-xl border text-center transition-all">
                            <div class="text-lg mb-1">%</div>
                            <div class="text-xs font-medium">Persentase</div>
                        </button>
                        <button type="button" @click="form.type = 'fixed'" :class="form.type === 'fixed' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]' : 'border-[var(--color-gc-border)] text-[var(--color-gc-text-secondary)]'" class="p-3 rounded-xl border text-center transition-all">
                            <div class="text-lg mb-1">Rp</div>
                            <div class="text-xs font-medium">Nominal</div>
                        </button>
                        <button type="button" @click="form.type = 'free_shipping'" :class="form.type === 'free_shipping' ? 'border-[var(--color-gc-primary)] bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]' : 'border-[var(--color-gc-border)] text-[var(--color-gc-text-secondary)]'" class="p-3 rounded-xl border text-center transition-all">
                            <div class="text-lg mb-1">🚚</div>
                            <div class="text-xs font-medium">Free Ongkir</div>
                        </button>
                    </div>
                </div>
                <div x-show="form.type !== 'free_shipping'">
                    <label for="discount_value" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Nilai Diskon <span class="text-[var(--color-gc-error)]">*</span></label>
                    <div class="relative">
                        <span x-show="form.type === 'fixed'" class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-[var(--color-gc-text-tertiary)]">Rp</span>
                        <input type="number" id="discount_value" x-model="form.discount_value" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" :class="form.type === 'fixed' ? 'pl-9' : ''" :placeholder="form.type === 'percentage' ? 'Contoh: 25' : 'Contoh: 25000'">
                        <span x-show="form.type === 'percentage'" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[var(--color-gc-text-tertiary)]">%</span>
                    </div>
                </div>
                <div>
                    <label for="min_purchase" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Minimum Pembelian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-[var(--color-gc-text-tertiary)]">Rp</span>
                        <input type="number" id="min_purchase" x-model="form.min_purchase" class="w-full h-10 pl-9 pr-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" placeholder="0 = tanpa minimum">
                    </div>
                </div>
                <div>
                    <label for="max_uses" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Maksimal Penggunaan</label>
                    <input type="number" id="max_uses" x-model="form.max_uses" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" placeholder="0 = tanpa batas">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Tanggal Mulai</label>
                        <input type="date" id="start_date" x-model="form.start_date" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Tanggal Selesai</label>
                        <input type="date" id="end_date" x-model="form.end_date" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                    </div>
                </div>
            </div>
            <div class="gc-modal-footer">
                <button @click="showAddModal = false; editingVoucher = null" class="gc-btn gc-btn-ghost gc-btn-md">Batal</button>
                <button @click="showAddModal = false" class="gc-btn gc-btn-primary gc-btn-md">Simpan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('adminVouchers', () => ({
    search: '',
    filterType: '',
    filterStatus: '',
    showAddModal: false,
    editingVoucher: null,
    form: {
        code: '',
        type: 'percentage',
        discount_value: '',
        min_purchase: '',
        max_uses: '',
        start_date: '',
        end_date: '',
    },
    generateCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 10; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return code;
    },
}));
</script>
@endpush
@endsection