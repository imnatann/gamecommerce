@extends('layouts.auth', ['title' => 'Lupa Password'])

@section('content')
<div x-data="{ email: '', loading: false, sent: false }">
    @if(session('status'))
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[var(--color-gc-accent)]/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a2 2 0 0 1-2.06 0L2 7"/></svg>
            </div>
            <h2 class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)] mb-2">Cek Email Anda</h2>
            <p class="text-sm text-[var(--color-gc-text-secondary)]">Kami telah mengirim link reset password ke alamat email Anda. Silakan cek inbox dan folder spam.</p>
            <a href="{{ route('login') }}" class="gc-btn gc-btn-primary gc-btn-md w-full mt-6">Kembali ke Login</a>
        </div>
    @else
        <h2 class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)] text-center mb-2">Lupa Password?</h2>
        <p class="text-sm text-[var(--color-gc-text-secondary)] text-center mb-6">Masukkan email Anda dan kami akan mengirimkan link untuk mereset password.</p>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">Email</label>
                <input id="email" type="email" name="email" x-model="email" class="gc-input w-full" placeholder="nama@email.com" required autofocus autocomplete="email">
                @error('email')
                    <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="gc-btn gc-btn-accent gc-btn-md w-full" :disabled="loading">
                <span x-show="!loading">Kirim Link Reset</span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    Mengirim...
                </span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-[var(--color-gc-text-secondary)]">
            Ingat password Anda?
            <a href="{{ route('login') }}" class="text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] font-medium transition-colors">Masuk</a>
        </p>
    @endif
</div>
@endsection