<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GameCommerce — Top up game termurah, beli game key, akun game, voucher & item game. Transaksi aman, proses instan.">

    <title>{{ isset($title) ? $title . ' — GameCommerce' : 'GameCommerce — Top Up Game Termurah' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-[var(--color-gc-bg)] text-[var(--color-gc-text)] antialiased" x-data="{ ...$data() }">
    @php
        $loginUrl = \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/auth/login');
        $registerUrl = \Illuminate\Support\Facades\Route::has('register') ? route('register') : url('/auth/register');
        $logoutUrl = \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/auth/logout');
        $cartUrl = auth()->check() ? route('cart') : $loginUrl;
        $favoritesUrl = auth()->check() ? route('profile.favorites') : $loginUrl;
    @endphp

    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[100] focus:px-4 focus:py-2 focus:bg-[var(--color-gc-primary)] focus:text-white focus:rounded-lg">
        Skip to content
    </a>

    {{-- Navbar --}}
    <nav class="gc-navbar">
        <div class="gc-navbar-inner">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <svg class="w-8 h-8 text-[var(--color-gc-primary)]" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="currentColor"/>
                    <path d="M10 8l12 8-12 8V8z" fill="white"/>
                </svg>
                <span class="font-[var(--font-family-display)] text-xl font-bold text-[var(--color-gc-text)] gc-hide-mobile">GameCommerce</span>
            </a>

            {{-- Search Bar --}}
            <div class="flex-1 max-w-2xl mx-4 gc-hide-mobile" x-data="searchAutocomplete">
                <div class="gc-search">
                    <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text"
                           class="gc-search-input"
                           placeholder="Cari game, top up, akun..."
                           x-model="query"
                           @input="search()"
                           @keydown.down.prevent="navigateDown()"
                           @keydown.up.prevent="navigateUp()"
                           @keydown.enter="enterSelected()"
                           @blur="close()"
                           aria-label="Search games and products"
                           autocomplete="off">
                    <div class="gc-search-dropdown" x-show="isOpen && results.length > 0" x-transition @mousedown.prevent>
                        <template x-for="(item, index) in results" :key="item.id ?? index">
                            <a :href="item.url" class="gc-search-result-item" :class="{ 'bg-[var(--color-gc-hover)]': selectedIndex === index }">
                                <img :src="item.image ?? ''" :alt="item.name" class="w-10 h-10 rounded-lg object-cover" loading="lazy">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-medium text-[var(--color-gc-text)] truncate" x-text="item.name"></div>
                                    <div class="text-xs text-[var(--color-gc-text-tertiary)] truncate" x-text="item.category ?? ''"></div>
                                </div>
                                <span class="text-sm font-bold text-[var(--color-gc-accent)]" x-text="item.price ?? ''"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Right Side Nav --}}
            <div class="flex items-center gap-2">
                {{-- Cart --}}
                <a href="{{ $cartUrl }}" class="gc-btn-icon gc-btn-ghost relative" aria-label="Shopping cart">
                    <svg class="gc-icon-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span x-data="{ count: 0 }" x-show="count > 0" class="gc-notification-badge" x-text="count" role="status" aria-label="Cart items"></span>
                </a>

                {{-- Theme Toggle --}}
                <button x-data="themeToggle" @click="toggle()" class="gc-btn-icon gc-btn-ghost" :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'">
                    <svg x-show="dark" class="gc-icon-md text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg x-show="!dark" class="gc-icon-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>

                {{-- Auth Buttons --}}
                @guest
                    <a href="{{ $loginUrl }}" class="gc-btn gc-btn-ghost gc-btn-sm gc-hide-mobile">Masuk</a>
                    <a href="{{ $registerUrl }}" class="gc-btn gc-btn-primary gc-btn-sm gc-hide-mobile">Daftar</a>
                @endguest

                @auth
                    <x-notification-bell class="gc-hide-mobile" />
                    <div class="relative gc-hide-mobile" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-[var(--color-gc-hover)]" aria-label="User menu">
                            <div class="w-8 h-8 rounded-full bg-[var(--color-gc-primary)] flex items-center justify-center text-white text-sm font-semibold">
                                {{ strtoupper(Auth::user()->name[0] ?? 'U') }}
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-56 bg-[var(--color-gc-elevated)] border border-[var(--color-gc-border)] rounded-xl shadow-2xl z-50">
                            <div class="p-3 border-b border-[var(--color-gc-border)]">
                                <div class="text-sm font-medium text-[var(--color-gc-text)]">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-[var(--color-gc-text-tertiary)]">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="p-1">
                                <a href="{{ route('profile.index') }}" class="gc-nav-link block rounded-lg">Profil Saya</a>
                                <a href="{{ route('profile.orders') }}" class="gc-nav-link block rounded-lg">Pesanan</a>
                                <a href="{{ route('profile.favorites') }}" class="gc-nav-link block rounded-lg">Wishlist</a>
                            </div>
                            <div class="p-1 border-t border-[var(--color-gc-border)]">
                                <form method="POST" action="{{ $logoutUrl }}">
                                    @csrf
                                    <button type="submit" class="gc-nav-link block w-full text-left text-[var(--color-gc-error)] rounded-lg">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth

                {{-- Mobile Menu Toggle --}}
                <button x-data="mobileMenu" @click="toggle()" class="gc-btn-icon gc-btn-ghost gc-hide-desktop" aria-label="Menu">
                    <svg class="gc-icon-lg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile Search (visible only on mobile) --}}
    <div class="gc-hide-desktop px-4 py-2 bg-[var(--color-gc-bg)] border-b border-[var(--color-gc-border)]" x-data="searchAutocomplete">
        <div class="gc-search">
            <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text"
                   class="gc-search-input h-10 text-sm"
                   placeholder="Cari game, top up..."
                   x-model="query"
                   @input="search()"
                   @keydown.down.prevent="navigateDown()"
                   @keydown.up.prevent="navigateUp()"
                   @keydown.enter="enterSelected()"
                   @blur="close()"
                   aria-label="Search games and products"
                   autocomplete="off">
            <div class="gc-search-dropdown" x-show="isOpen && results.length > 0" x-transition>
                <template x-for="(item, index) in results" :key="item.id ?? index">
                    <a :href="item.url" class="gc-search-result-item">
                        <div class="text-sm font-medium truncate" x-text="item.name"></div>
                    </a>
                </template>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <main id="main-content" class="min-h-[calc(100vh-4rem)]">
        @yield('content')
    </main>

    {{-- Mobile Bottom Navigation --}}
    <nav class="gc-bottom-nav" aria-label="Mobile navigation">
        <div class="flex items-center justify-around">
            <a href="{{ route('home') }}" class="gc-bottom-nav-item {{ request()->routeIs('home') ? 'gc-bottom-nav-item-active' : '' }}">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <span>Home</span>
            </a>
            <a href="{{ route('search') }}" class="gc-bottom-nav-item">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <span>Cari</span>
            </a>
            <a href="{{ $cartUrl }}" class="gc-bottom-nav-item relative">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                <span>Keranjang</span>
            </a>
            <a href="{{ $favoritesUrl }}" class="gc-bottom-nav-item">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                <span>Favorit</span>
            </a>
            @auth
                <a href="{{ route('profile.index') }}" class="gc-bottom-nav-item">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Profil</span>
                </a>
            @else
                <a href="{{ $loginUrl }}" class="gc-bottom-nav-item">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Akun</span>
                </a>
            @endauth
        </div>
    </nav>

    {{-- Footer --}}
    <footer class="bg-[var(--color-gc-card)] border-t border-[var(--color-gc-border)] mt-16 pb-20 md:pb-0">
        <div class="max-w-[var(--gc-container-2xl)] mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- About --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-7 h-7 text-[var(--color-gc-primary)]" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="currentColor"/><path d="M10 8l12 8-12 8V8z" fill="white"/></svg>
                        <span class="font-[var(--font-family-display)] text-lg font-bold text-[var(--color-gc-text)]">GameCommerce</span>
                    </div>
                    <p class="text-sm text-[var(--color-gc-text-secondary)] mb-4">Platform top up game termurah dan terpercaya. Beli game key, akun game, voucher & item game dengan proses instan.</p>
                    <div class="flex items-center gap-3">
                        <a href="#" class="gc-btn-icon gc-btn-ghost" aria-label="Facebook"><svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                        <a href="#" class="gc-btn-icon gc-btn-ghost" aria-label="Twitter"><svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg></a>
                        <a href="#" class="gc-btn-icon gc-btn-ghost" aria-label="Instagram"><svg class="gc-icon-sm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg></a>
                    </div>
                </div>

                {{-- About Links --}}
                <div>
                    <h3 class="font-semibold text-[var(--color-gc-text)] mb-4">Tentang Kami</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Tentang GameCommerce</a></li>
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Cara Kerja</a></li>
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Blog</a></li>
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Karir</a></li>
                    </ul>
                </div>

                {{-- Help --}}
                <div>
                    <h3 class="font-semibold text-[var(--color-gc-text)] mb-4">Bantuan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Pusat Bantuan</a></li>
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-sm text-[var(--color-gc-text-secondary)] hover:text-[var(--color-gc-primary)] transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>

                {{-- Payment Methods --}}
                <div>
                    <h3 class="font-semibold text-[var(--color-gc-text)] mb-4">Metode Pembayaran</h3>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">QRIS</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">BCA</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">BNI</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">BRI</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">GoPay</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">OVO</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">DANA</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">ShopeePay</div>
                        <div class="flex items-center justify-center p-2 rounded-lg bg-[var(--color-gc-hover)] text-xs text-[var(--color-gc-text-secondary)]">LinkAja</div>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-[var(--color-gc-border)] flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-xs text-[var(--color-gc-text-tertiary)]">&copy; {{ date('Y') }} GameCommerce. All rights reserved.</p>
                <div class="flex items-center gap-4 text-xs text-[var(--color-gc-text-tertiary)]">
                    <a href="#" class="hover:text-[var(--color-gc-primary)] transition-colors">Syarat Layanan</a>
                    <a href="#" class="hover:text-[var(--color-gc-primary)] transition-colors">Privasi</a>
                    <a href="#" class="hover:text-[var(--color-gc-primary)] transition-colors">Cookie</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
