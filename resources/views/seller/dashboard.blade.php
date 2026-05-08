@extends('layouts.seller', ['title' => 'Dashboard'])

@section('content')
<div x-data="sellerDashboard()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Dashboard</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Selamat datang kembali, {{ Auth::user()->name }}!</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.products.create') }}" class="gc-btn gc-btn-primary gc-btn-md">
                <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Produk
            </a>
        </div>
    </div>

    {{-- Revenue Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Today --}}
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Pendapatan Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-primary)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]" x-text="formatRupiah(stats.today)">Rp 0</div>
            <div class="flex items-center gap-1 mt-2 text-xs" :class="stats.todayChange >= 0 ? 'text-[var(--color-gc-accent)]' : 'text-[var(--color-gc-error)]'">
                <svg x-show="stats.todayChange >= 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <svg x-show="stats.todayChange < 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                <span x-text="Math.abs(stats.todayChange) + '%'"></span> dari kemarin
            </div>
        </div>

        {{-- This Week --}}
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Pendapatan Minggu Ini</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-accent)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]" x-text="formatRupiah(stats.week)">Rp 0</div>
            <div class="flex items-center gap-1 mt-2 text-xs" :class="stats.weekChange >= 0 ? 'text-[var(--color-gc-accent)]' : 'text-[var(--color-gc-error)]'">
                <svg x-show="stats.weekChange >= 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <svg x-show="stats.weekChange < 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                <span x-text="Math.abs(stats.weekChange) + '%'"></span> dari minggu lalu
            </div>
        </div>

        {{-- This Month --}}
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Pendapatan Bulan Ini</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-warning)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]" x-text="formatRupiah(stats.month)">Rp 0</div>
            <div class="flex items-center gap-1 mt-2 text-xs" :class="stats.monthChange >= 0 ? 'text-[var(--color-gc-accent)]' : 'text-[var(--color-gc-error)]'">
                <svg x-show="stats.monthChange >= 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                <svg x-show="stats.monthChange < 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                <span x-text="Math.abs(stats.monthChange) + '%'"></span> dari bulan lalu
            </div>
        </div>

        {{-- Total Earnings --}}
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Total Pendapatan</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-primary)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a2 2 0 0 1 0 4H6a2 2 0 0 0 0 4h14a2 2 0 0 1 0 4H6a2 2 0 0 1-1-1v-3"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-accent)]" x-text="formatRupiah(stats.total)">Rp 0</div>
            <div class="text-xs text-[var(--color-gc-text-tertiary)] mt-2">Sejak bergabung</div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="gc-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[var(--color-gc-primary)]/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" x2="7.01" y1="7" y2="7"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold font-[var(--font-family-display)]" x-text="stats.activeProducts">0</div>
                <div class="text-sm text-[var(--color-gc-text-secondary)]">Produk Aktif</div>
            </div>
        </div>
        <div class="gc-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[var(--color-gc-warning)]/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold font-[var(--font-family-display)]" x-text="stats.pendingOrders">0</div>
                <div class="text-sm text-[var(--color-gc-text-secondary)]">Pesanan Tertunda</div>
            </div>
        </div>
        <div class="gc-card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-[var(--color-gc-accent)]/10 flex items-center justify-center">
                <svg class="w-6 h-6 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold font-[var(--font-family-display)]" x-text="stats.totalOrders">0</div>
                <div class="text-sm text-[var(--color-gc-text-secondary)]">Total Pesanan</div>
            </div>
        </div>
    </div>

    {{-- Earnings Chart + Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Chart Area --}}
        <div class="lg:col-span-2 gc-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Pendapatan</h2>
                <div class="flex items-center gap-2" x-data="{ period: 'week' }">
                    <button @click="period = 'week'" :class="period === 'week' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">7 Hari</button>
                    <button @click="period = 'month'" :class="period === 'month' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">30 Hari</button>
                    <button @click="period = 'year'" :class="period === 'year' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">12 Bulan</button>
                </div>
            </div>
            <div class="h-64">
                <canvas id="seller-revenue-chart"></canvas>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="gc-card p-6">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Aksi Cepat</h2>
            <div class="space-y-3">
                <a href="{{ route('seller.products.create') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[var(--color-gc-bg-subtle)] hover:bg-[var(--color-gc-hover)] border border-[var(--color-gc-border)] transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-[var(--color-gc-primary)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-primary)]/20 transition-colors">
                        <svg class="w-5 h-5 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Tambah Produk</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Tambah produk baru</div>
                    </div>
                </a>
                <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[var(--color-gc-bg-subtle)] hover:bg-[var(--color-gc-hover)] border border-[var(--color-gc-border)] transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-[var(--color-gc-accent)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-accent)]/20 transition-colors">
                        <svg class="w-5 h-5 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Lihat Pesanan</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">{{ $pendingOrdersCount ?? 0 }} pesanan tertunda</div>
                    </div>
                </a>
                <a href="{{ route('seller.balance.index') }}" class="flex items-center gap-3 p-3 rounded-xl bg-[var(--color-gc-bg-subtle)] hover:bg-[var(--color-gc-hover)] border border-[var(--color-gc-border)] transition-all group">
                    <div class="w-10 h-10 rounded-lg bg-[var(--color-gc-warning)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-warning)]/20 transition-colors">
                        <svg class="w-5 h-5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a2 2 0 0 1 0 4H6a2 2 0 0 0 0 4h14a2 2 0 0 1 0 4H6a2 2 0 0 1-1-1v-3"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Tarik Saldo</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Pencairan dana</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="gc-card">
        <div class="p-6 border-b border-[var(--color-gc-border)]">
            <div class="flex items-center justify-between">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Pesanan Terbaru</h2>
                <a href="{{ route('seller.orders.index') }}" class="text-sm text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] transition-colors">Lihat Semua →</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($recentOrders ?? null && count($recentOrders) > 0)
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Order ID</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Pembeli</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Produk</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Jumlah</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Tanggal</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4 font-mono text-[var(--color-gc-primary)]">#{{ $order['id'] ?? $loop->index + 1 }}</td>
                        <td class="p-4 text-[var(--color-gc-text)]">{{ $order['buyer'] ?? 'User' }}</td>
                        <td class="p-4 text-[var(--color-gc-text)]">{{ $order['product'] ?? 'Product' }}</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-accent)]">{{ $order['amount'] ?? 'Rp 0' }}</td>
                        <td class="p-4"><x-order-status-badge :status="$order['status'] ?? 'processing'" /></td>
                        <td class="p-4 text-[var(--color-gc-text-secondary)]">{{ $order['date'] ?? '-' }}</td>
                        <td class="p-4 text-right">
                            <a href="#" class="text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] text-sm">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-12 text-center text-[var(--color-gc-text-tertiary)]">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                <p>Belum ada pesanan</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('sellerDashboard', () => ({
    stats: {
        today: 1250000,
        todayChange: 12.5,
        week: 8750000,
        weekChange: 8.3,
        month: 35000000,
        monthChange: 15.2,
        total: 150000000,
        activeProducts: 24,
        pendingOrders: 5,
        totalOrders: 312,
    },
    formatRupiah(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
    }
}));
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('seller-revenue-chart');
    if (!ctx) return;

    const revenueData = @json($revenueByMonth ?? collect());

    const labels = revenueData.keys().all().length > 0
        ? revenueData.keys().all()
        : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    const data = revenueData.values().all().length > 0
        ? revenueData.pluck('revenue').all()
        : [0,0,0,0,0,0,0,0,0,0,0,0];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: data.map(v => v / 100),
                borderColor: 'rgb(139, 92, 244)',
                backgroundColor: 'rgba(139, 92, 244, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(139, 92, 244)',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 15, 30, 0.95)',
                    titleColor: '#e2e8f0',
                    bodyColor: '#e2e8f0',
                    borderColor: 'rgba(139, 92, 244, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y * 100);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.06)' },
                    ticks: { color: 'rgba(255,255,255,0.5)', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.06)' },
                    ticks: {
                        color: 'rgba(255,255,255,0.5)',
                        font: { size: 11 },
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value * 100);
                        }
                    }
                }
            },
            interaction: { intersect: false, mode: 'index' }
        }
    });
});
</script>
@endpush
@endsection