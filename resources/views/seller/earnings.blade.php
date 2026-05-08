@extends('layouts.seller', ['title' => 'Pendapatan'])

@section('content')
<div x-data="sellerEarnings()">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Pendapatan</h1>
        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Pantau saldo dan riwayat pendapatan Anda</p>
    </div>

    {{-- Earnings Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Saldo Tersedia</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-accent)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a2 2 0 0 1 0 4H6a2 2 0 0 0 0 4h14a2 2 0 0 1 0 4H6a2 2 0 0 1-1-1v-3"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-accent)]" x-text="formatRupiah(earnings.available)">Rp 0</div>
            <div class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Siap ditarik</div>
        </div>
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Tertahan</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-warning)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-warning)]" x-text="formatRupiah(earnings.pending)">Rp 0</div>
            <div class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Menunggu konfirmasi</div>
        </div>
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Ditarik</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-info)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-info)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-info)]" x-text="formatRupiah(earnings.withdrawn)">Rp 0</div>
            <div class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Total pencairan</div>
        </div>
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Total Pendapatan</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-primary)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]" x-text="formatRupiah(earnings.total)">Rp 0</div>
            <div class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Sejak bergabung</div>
        </div>
    </div>

    {{-- Withdrawal Section --}}
    <div class="gc-card p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Tarik Saldo</h2>
                <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Minimum pencairan Rp 50.000. Proses 1-3 hari kerja.</p>
            </div>
            <button @click="showWithdrawModal = true" class="gc-btn gc-btn-accent gc-btn-md" :disabled="earnings.available < 50000">
                <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Tarik Saldo
            </button>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="flex items-center justify-between gap-4 mb-4">
        <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Riwayat Pendapatan</h2>
        <div class="flex items-center gap-2" x-data="{ period: 'month' }">
            <button @click="period = 'today'; filterEarnings()" :class="period === 'today' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">Hari Ini</button>
            <button @click="period = 'week'; filterEarnings()" :class="period === 'week' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">Minggu Ini</button>
            <button @click="period = 'month'; filterEarnings()" :class="period === 'month' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">Bulan Ini</button>
            <button @click="period = 'custom'; showDatePicker = true" :class="period === 'custom' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">Kustom</button>
        </div>
    </div>

    {{-- Earnings History Table --}}
    <div class="gc-card overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Tanggal</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Order ID</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Produk</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Jumlah</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Komisi</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 8; $i++)
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4 text-[var(--color-gc-text-secondary)]">{{ now()->subDays($i)->format('d M Y') }}</td>
                        <td class="p-4 font-mono text-[var(--color-gc-primary)]">#ORD{{ str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text)]">{{ ['86 Diamond ML', '120 UC PUBG', 'Weekly Pass ML', 'Rising Star Package', '500 Diamond ML', 'SS Badge FF', 'Prime Plus ML', '390 UC PUBG'][$i - 1] }}</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-text)]">{{ 'Rp ' . number_format(rand(20000, 500000), 0, ',', '.') }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-warning)]">{{ 'Rp ' . number_format(rand(2000, 50000), 0, ',', '.') }}</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-accent)]">{{ 'Rp ' . number_format(rand(18000, 450000), 0, ',', '.') }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- Withdrawal History --}}
    <div class="gc-card overflow-hidden">
        <div class="p-6 border-b border-[var(--color-gc-border)]">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Riwayat Pencairan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Tanggal</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Jumlah</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Metode</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4 text-[var(--color-gc-text-secondary)]">15 Jan 2025</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-text)]">Rp 5.000.000</td>
                        <td class="p-4 text-[var(--color-gc-text)]">Bank BCA</td>
                        <td class="p-4"><span class="gc-badge bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]">Selesai</span></td>
                    </tr>
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4 text-[var(--color-gc-text-secondary)]">01 Jan 2025</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-text)]">Rp 3.200.000</td>
                        <td class="p-4 text-[var(--color-gc-text)]">Bank BCA</td>
                        <td class="p-4"><span class="gc-badge bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]">Selesai</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Withdraw Modal --}}
    <div x-show="showWithdrawModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.prevent="showWithdrawModal = false">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showWithdrawModal = false"></div>
        <div x-show="showWithdrawModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="gc-modal-panel max-w-md relative bg-[var(--color-gc-bg-elevated)]">
            <div class="gc-modal-header">
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Tarik Saldo</h3>
                <button @click="showWithdrawModal = false" class="gc-btn-icon gc-btn-ghost" aria-label="Close">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="gc-modal-body space-y-4">
                <div class="p-4 rounded-xl bg-[var(--color-gc-accent)]/10 border border-[var(--color-gc-accent)]/20">
                    <div class="text-sm text-[var(--color-gc-text-secondary)]">Saldo tersedia</div>
                    <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-accent)]" x-text="formatRupiah(earnings.available)">Rp 0</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Jumlah Pencairan</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-[var(--color-gc-text-tertiary)]">Rp</span>
                        <input type="number" x-model="withdrawAmount" placeholder="Minimum Rp 50.000" class="w-full h-10 pl-9 pr-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)] focus:ring-2 focus:ring-[var(--color-gc-primary)]/20" min="50000">
                    </div>
                    <p class="text-xs text-[var(--color-gc-text-tertiary)] mt-1">Minimum pencairan Rp 50.000</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1.5">Rekening Tujuan</label>
                    <select x-model="withdrawBank" class="w-full h-10 px-3 bg-[var(--color-gc-bg-card)] border border-[var(--color-gc-border)] rounded-xl text-sm text-[var(--color-gc-text)] focus:outline-none focus:border-[var(--color-gc-primary)]">
                        <option value="">Pilih rekening</option>
                        <option value="bca">BCA - **** 1234</option>
                        <option value="mandiri">Mandiri - **** 5678</option>
                    </select>
                </div>
            </div>
            <div class="gc-modal-footer">
                <button @click="showWithdrawModal = false" class="gc-btn gc-btn-ghost gc-btn-md">Batal</button>
                <button @click="submitWithdraw()" class="gc-btn gc-btn-accent gc-btn-md" :disabled="!withdrawAmount || withdrawAmount < 50000">Ajukan Pencairan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('sellerEarnings', () => ({
    earnings: {
        available: 27500000,
        pending: 3200000,
        withdrawn: 120000000,
        total: 150700000,
    },
    showWithdrawModal: false,
    showDatePicker: false,
    withdrawAmount: '',
    withdrawBank: '',
    formatRupiah(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
    },
    filterEarnings() {},
    submitWithdraw() {
        this.showWithdrawModal = false;
    },
}));
</script>
@endpush
@endsection