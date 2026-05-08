@extends('layouts.auth', ['title' => 'Reset Password'])

@section('content')
<div x-data="{ showPassword: false, loading: false }">
    <h2 class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)] text-center mb-2">Reset Password</h2>
    <p class="text-sm text-[var(--color-gc-text-secondary)] text-center mb-6">Masukkan password baru Anda.</p>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? request('token') }}">

        <div>
            <label for="email" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email ?? '') }}" class="gc-input w-full" placeholder="nama@email.com" required autofocus autocomplete="email">
            @error('email')
                <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">Password Baru</label>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" class="gc-input w-full pr-10" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--color-gc-text-tertiary)] hover:text-[var(--color-gc-text)] transition-colors" aria-label="Toggle password visibility">
                    <svg x-show="!showPassword" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.91-1.57a3 3 0 1 1-4.24 4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    <svg x-show="showPassword" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">Konfirmasi Password Baru</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="gc-input w-full" placeholder="Ulangi password baru" required autocomplete="new-password">
            @error('password_confirmation')
                <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="gc-btn gc-btn-accent gc-btn-md w-full" :disabled="loading">
            <span x-show="!loading">Reset Password</span>
            <span x-show="loading" class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                Memproses...
            </span>
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-[var(--color-gc-text-secondary)]">
        Ingat password Anda?
        <a href="{{ route('login') }}" class="text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] font-medium transition-colors">Masuk</a>
    </p>
</div>
@endsection