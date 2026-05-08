@extends('layouts.auth', ['title' => 'Masuk'])

@section('content')
<div x-data="{ email: '', password: '', showPassword: false, loading: false }">
    <h2 class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)] text-center mb-2">Masuk ke Akun</h2>
    <p class="text-sm text-[var(--color-gc-text-secondary)] text-center mb-6">Selamat datang kembali! Masukkan kredensial Anda.</p>

    @if(session('status'))
        <div class="gc-alert gc-alert-info mb-4">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    {{-- Social Login --}}
    <div class="space-y-3 mb-6">
        <a href="{{ route('auth.google') }}" class="gc-btn gc-btn-ghost gc-btn-md w-full flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            Masuk dengan Google
        </a>
    </div>

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-[var(--color-gc-border)]"></div></div>
        <div class="relative flex justify-center text-xs"><span class="bg-[var(--color-gc-card)] px-2 text-[var(--color-gc-text-tertiary)]">atau</span></div>
    </div>

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">Email</label>
            <input id="email" type="email" name="email" x-model="email" class="gc-input w-full" placeholder="nama@email.com" required autofocus autocomplete="email">
            @error('email')
                <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-[var(--color-gc-text-secondary)]">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] transition-colors">Lupa Password?</a>
            </div>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" class="gc-input w-full pr-10" placeholder="Masukkan password" required autocomplete="current-password">
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--color-gc-text-tertiary)] hover:text-[var(--color-gc-text)] transition-colors" aria-label="Toggle password visibility">
                    <svg x-show="!showPassword" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.91-1.57a3 3 0 1 1-4.24 4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    <svg x-show="showPassword" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input id="remember" type="checkbox" name="remember" class="gc-checkbox">
            <label for="remember" class="text-sm text-[var(--color-gc-text-secondary)] cursor-pointer">Ingat saya</label>
        </div>

        <button type="submit" class="gc-btn gc-btn-accent gc-btn-md w-full" :disabled="loading">
            <span x-show="!loading">Masuk</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Memproses...
            </span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-[var(--color-gc-text-secondary)]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] font-medium transition-colors">Daftar sekarang</a>
    </p>
</div>
@endsection
