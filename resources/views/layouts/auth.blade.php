<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — GameCommerce' : 'GameCommerce' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-[var(--color-gc-bg)] text-[var(--color-gc-text)] antialiased flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Minimal Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <svg class="w-10 h-10 text-[var(--color-gc-primary)]" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="currentColor"/>
                    <path d="M10 8l12 8-12 8V8z" fill="white"/>
                </svg>
                <span class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]">GameCommerce</span>
            </a>
        </div>

        {{-- Auth Card --}}
        <div class="gc-card p-6 md:p-8">
            @yield('content')
        </div>

        {{-- Footer Links --}}
        <div class="mt-6 text-center text-xs text-[var(--color-gc-text-tertiary)]">
            <a href="{{ route('home') }}" class="hover:text-[var(--color-gc-primary)] transition-colors">Kembali ke Beranda</a>
            <span class="mx-2">·</span>
            <a href="#" class="hover:text-[var(--color-gc-primary)] transition-colors">Bantuan</a>
        </div>
    </div>

    @stack('scripts')
</body>
</html>