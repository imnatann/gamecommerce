@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
<div x-data="adminDashboard()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-[var(--font-family-display)] text-2xl lg:text-3xl font-bold text-[var(--color-gc-text)]">Dashboard</h1>
            <p class="text-sm text-[var(--color-gc-text-secondary)] mt-1">Overview platform GameCommerce</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-[var(--color-gc-text-secondary)]">
            <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Total Revenue</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-accent)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-accent)]" x-text="formatRupiah(stats.revenue)">Rp 0</div>
            <div class="flex items-center gap-1 mt-2 text-xs text-[var(--color-gc-accent)]">
                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                <span x-text="stats.revenueGrowth + '%'"></span> dari bulan lalu
            </div>
        </div>

        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Total Pesanan</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-primary)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]" x-text="stats.orders.toLocaleString()">0</div>
            <div class="flex items-center gap-1 mt-2 text-xs" :class="stats.ordersGrowth >= 0 ? 'text-[var(--color-gc-accent)]' : 'text-[var(--color-gc-error)]'">
                <svg x-show="stats.ordersGrowth >= 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                <svg x-show="stats.ordersGrowth < 0" class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/></svg>
                <span x-text="Math.abs(stats.ordersGrowth) + '%'"></span> dari bulan lalu
            </div>
        </div>

        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Total Pengguna</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-info)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-info)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]" x-text="stats.users.toLocaleString()">0</div>
            <div class="flex items-center gap-1 mt-2 text-xs text-[var(--color-gc-info)]">
                <span x-text="stats.newUsersToday + ' baru hari ini'"></span>
            </div>
        </div>

        <div class="gc-card p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-[var(--color-gc-text-secondary)]">Total Produk</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-gc-warning)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" x2="7.01" y1="7" y2="7"/></svg>
                </div>
            </div>
            <div class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]" x-text="stats.products.toLocaleString()">0</div>
            <div class="flex items-center gap-1 mt-2 text-xs text-[var(--color-gc-text-tertiary)]">
                <span x-text="stats.activeProducts + ' aktif'"></span>
            </div>
        </div>
    </div>

    {{-- Charts + Quick Links --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Revenue Chart Placeholder --}}
        <div class="lg:col-span-2 gc-card p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Revenue Overview</h2>
                <div class="flex items-center gap-2" x-data="{ period: 'month' }">
                    <button @click="period = 'week'" :class="period === 'week' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">7 Hari</button>
                    <button @click="period = 'month'" :class="period === 'month' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">30 Hari</button>
                    <button @click="period = 'year'" :class="period === 'year' ? 'gc-btn-primary' : 'gc-btn-ghost'" class="gc-btn gc-btn-xs">12 Bulan</button>
                </div>
            </div>
            <div class="h-64">
                <canvas id="admin-revenue-chart"></canvas>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="gc-card p-6">
            <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)] mb-4">Quick Manage</h2>
            <div class="space-y-2">
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-[var(--color-gc-primary)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-primary)]/20">
                        <svg class="w-5 h-5 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Games</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Kelola game katalog</div>
                    </div>
                    <svg class="w-4 h-4 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-[var(--color-gc-accent)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-accent)]/20">
                        <svg class="w-5 h-5 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Users</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Manage pengguna</div>
                    </div>
                    <svg class="w-4 h-4 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-[var(--color-gc-warning)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-warning)]/20">
                        <svg class="w-5 h-5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Orders</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Semua pesanan</div>
                    </div>
                    <svg class="w-4 h-4 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-[var(--color-gc-error)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-error)]/20">
                        <svg class="w-5 h-5 text-[var(--color-gc-error)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Disputes</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Kelola dispute</div>
                    </div>
                    <span class="gc-badge bg-[var(--color-gc-error)]/10 text-[var(--color-gc-error)]" x-text="stats.pendingDisputes + ' open'">0 open</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-[var(--color-gc-info)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-info)]/20">
                        <svg class="w-5 h-5 text-[var(--color-gc-info)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Banners</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Kelola banner promo</div>
                    </div>
                    <svg class="w-4 h-4 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
                <a href="#" class="flex items-center gap-3 p-3 rounded-xl hover:bg-[var(--color-gc-hover)] transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-[var(--color-gc-primary)]/10 flex items-center justify-center group-hover:bg-[var(--color-gc-primary)]/20">
                        <svg class="w-5 h-5 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"/><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">Vouchers</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Kelola voucher promo</div>
                    </div>
                    <svg class="w-4 h-4 text-[var(--color-gc-text-tertiary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="gc-card mb-8">
        <div class="p-6 border-b border-[var(--color-gc-border)]">
            <div class="flex items-center justify-between">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Pesanan Terbaru</h2>
                <a href="#" class="text-sm text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] transition-colors">Lihat Semua →</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Order ID</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Pembeli</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Penjual</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Produk</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Jumlah</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Status</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 10; $i++)
                    @php
                        $statuses = ['pending', 'processing', 'delivered', 'completed'];
                        $status = $statuses[$i % 4];
                    @endphp
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4 font-mono text-[var(--color-gc-primary)]">#ORD{{ str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="p-4 text-[var(--color-gc-text)]">User {{ rand(1, 500) }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">Shop {{ rand(1, 50) }}</td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text)]">{{ ['86 Diamond ML', '120 UC PUBG', 'Weekly Pass ML', 'Key Game Steam', 'Akun ML Sapphire'][$i % 5] }}</td>
                        <td class="p-4 font-semibold text-[var(--color-gc-accent)]">Rp {{ number_format(rand(20000, 500000), 0, ',', '.') }}</td>
                        <td class="p-4"><x-order-status-badge :status="$status" /></td>
                        <td class="p-4 gc-hide-mobile text-[var(--color-gc-text-secondary)]">{{ now()->subDays(rand(0, 7))->format('d M H:i') }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Registrations --}}
    <div class="gc-card">
        <div class="p-6 border-b border-[var(--color-gc-border)]">
            <div class="flex items-center justify-between">
                <h2 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">Registrasi Baru</h2>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-gc-border)] bg-[var(--color-gc-bg-subtle)]">
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">User</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Role</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium gc-hide-mobile">KYC</th>
                        <th class="text-left p-4 text-[var(--color-gc-text-secondary)] font-medium">Tgl Daftar</th>
                        <th class="text-right p-4 text-[var(--color-gc-text-secondary)] font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 5; $i++)
                    <tr class="border-b border-[var(--color-gc-border)] hover:bg-[var(--color-gc-hover)] transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[var(--color-gc-primary)] flex items-center justify-center text-white text-xs font-semibold">{{ strtoupper(substr('User', 0, 1) . rand(1, 99)) }}</div>
                                <div>
                                    <div class="text-sm font-medium text-[var(--color-gc-text)]">User {{ rand(1, 200) }}</div>
                                    <div class="text-xs text-[var(--color-gc-text-tertiary)]">user{{ rand(1, 200) }}@email.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4"><span class="gc-badge bg-[var(--color-gc-primary)]/10 text-[var(--color-gc-primary)]">{{ $i <= 3 ? 'Buyer' : 'Seller' }}</span></td>
                        <td class="p-4 gc-hide-mobile">
                            @if($i <= 2)
                            <span class="gc-badge bg-[var(--color-gc-accent)]/10 text-[var(--color-gc-accent)]">Verified</span>
                            @else
                            <span class="gc-badge bg-[var(--color-gc-warning)]/10 text-[var(--color-gc-warning)]">Pending</span>
                            @endif
                        </td>
                        <td class="p-4 text-[var(--color-gc-text-secondary)]">{{ now()->subDays(rand(0, 5))->format('d M') }}</td>
                        <td class="p-4 text-right"><a href="#" class="text-sm text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] transition-colors">View</a></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
Alpine.data('adminDashboard', () => ({
    stats: {
        revenue: 1250000000,
        revenueGrowth: 23.5,
        orders: 8432,
        ordersGrowth: 12.1,
        users: 15230,
        newUsersToday: 47,
        products: 3241,
        activeProducts: 2890,
        pendingDisputes: 12,
    },
    formatRupiah(val) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
    },
}));
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('admin-revenue-chart');
    if (!ctx) return;

    const orderData = @json($ordersByMonth ?? collect());

    const labels = orderData.keys().all().length > 0
        ? orderData.keys().all()
        : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    const revenueValues = orderData.pluck('revenue').all().length > 0
        ? orderData.pluck('revenue').all()
        : [0,0,0,0,0,0,0,0,0,0,0,0];

    const totalValues = orderData.pluck('total').all().length > 0
        ? orderData.pluck('total').all()
        : [0,0,0,0,0,0,0,0,0,0,0,0];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Orders',
                    data: totalValues,
                    backgroundColor: 'rgba(34, 211, 238, 0.6)',
                    borderColor: 'rgb(34, 211, 238)',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: 'Revenue',
                    data: revenueValues.map(v => v / 100),
                    type: 'line',
                    borderColor: 'rgb(139, 92, 244)',
                    backgroundColor: 'rgba(139, 92, 244, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(139, 92, 244)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    order: 1,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: { color: 'rgba(255,255,255,0.7)', font: { size: 11 } }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 15, 30, 0.95)',
                    titleColor: '#e2e8f0',
                    bodyColor: '#e2e8f0',
                    borderColor: 'rgba(139, 92, 244, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Revenue') {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y * 100);
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
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