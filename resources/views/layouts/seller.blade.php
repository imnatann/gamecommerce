<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — Seller Dashboard' : 'Seller Dashboard — GameCommerce' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-[var(--color-gc-bg)] text-[var(--color-gc-text)] antialiased" x-data="sellerSidebar">

    {{-- Top Bar --}}
    <header class="gc-navbar">
        <div class="gc-navbar-inner">
            <div class="flex items-center gap-3">
                <button @click="toggle()" class="gc-btn-icon gc-btn-ghost lg:hidden" aria-label="Toggle sidebar">
                    <svg class="gc-icon-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <svg class="w-7 h-7 text-[var(--color-gc-primary)]" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="currentColor"/><path d="M10 8l12 8-12 8V8z" fill="white"/></svg>
                    <span class="font-[var(--font-family-display)] text-lg font-bold gc-hide-mobile">Seller Center</span>
                </a>
            </div>
            <div class="flex items-center gap-2">
                <button x-data="themeToggle" @click="toggle()" class="gc-btn-icon gc-btn-ghost" :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'">
                    <svg x-show="dark" class="gc-icon-sm text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg x-show="!dark" class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <x-notification-bell />
                <div class="flex items-center gap-2 ml-2">
                    <div class="w-8 h-8 rounded-full bg-[var(--color-gc-primary)] flex items-center justify-center text-white text-sm font-semibold">
                        {{ strtoupper(Auth::user()->name[0] ?? 'S') }}
                    </div>
                    <div class="gc-hide-mobile">
                        <div class="text-sm font-medium text-[var(--color-gc-text)]">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-[var(--color-gc-text-tertiary)]">Seller</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="gc-sidebar hidden lg:block" :class="{ 'gc-sidebar-open block': mobileOpen }" @click.away="close()">
            <div class="p-4">
                <div class="gc-sidebar-section-title">Dashboard</div>
                <a href="{{ route('seller.dashboard') }}" class="gc-sidebar-link {{ request()->routeIs('seller.dashboard') ? 'gc-sidebar-link-active' : '' }}">
                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    Overview
                </a>

                <div class="gc-sidebar-section-title">Katalog</div>
                <a href="{{ route('seller.products.index') }}" class="gc-sidebar-link {{ request()->routeIs('seller.products.*') ? 'gc-sidebar-link-active' : '' }}">
                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" x2="7.01" y1="7" y2="7"/></svg>
                    Produk
                </a>
                <a href="{{ route('seller.products.create') }}" class="gc-sidebar-link">
                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah Produk
                </a>

                <div class="gc-sidebar-section-title">Penjualan</div>
                <a href="{{ route('seller.orders.index') }}" class="gc-sidebar-link {{ request()->routeIs('seller.orders.*') ? 'gc-sidebar-link-active' : '' }}">
                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    Pesanan
                </a>

                <div class="gc-sidebar-section-title">Keuangan</div>
                <a href="{{ route('seller.balance.index') }}" class="gc-sidebar-link {{ request()->routeIs('seller.balance.*') ? 'gc-sidebar-link-active' : '' }}">
                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a2 2 0 0 1 0 4H6a2 2 0 0 0 0 4h14a2 2 0 0 1 0 4H6a2 2 0 0 1-1-1v-3"/></svg>
                    Saldo
                </a>

                <div class="gc-sidebar-section-title">Pengaturan</div>
                <a href="{{ route('seller.settings.index') }}" class="gc-sidebar-link {{ request()->routeIs('seller.settings.*') ? 'gc-sidebar-link-active' : '' }}">
                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                    Pengaturan
                </a>
            </div>

            {{-- Back to Store --}}
            <div class="p-4 mt-auto border-t border-[var(--color-gc-border)]">
                <a href="{{ route('home') }}" class="gc-sidebar-link text-[var(--color-gc-text-tertiary)]">
                    <svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Kembali ke Toko
                </a>
            </div>
        </aside>

        {{-- Mobile backdrop --}}
        <div x-show="mobileOpen" @click="close()" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 lg:hidden"></div>

        {{-- Main Content --}}
        <main class="flex-1 min-h-[calc(100vh-4rem)] p-4 lg:p-8 overflow-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>