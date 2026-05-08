@extends('layouts.auth', ['title' => 'Verifikasi Email'])

@section('content')
<div x-data="{ resent: false, countdown: 0 }">
    <div class="text-center mb-6">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[var(--color-gc-accent)]/10 flex items-center justify-center">
            <svg class="w-8 h-8 text-[var(--color-gc-accent)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a2 2 0 0 1-2.06 0L2 7"/></svg>
        </div>
        <h2 class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]">Verifikasi Email Anda</h2>
    </div>

    @if(session('resent'))
        <div class="gc-alert gc-alert-success mb-4">
            <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Link verifikasi telah dikirim ulang!</span>
        </div>
    @endif

    <div class="text-sm text-[var(--color-gc-text-secondary)] text-center space-y-3">
        <p>Sebelum melanjutkan, silakan verifikasi alamat email Anda dengan mengklik link yang kami kirim ke <strong class="text-[var(--color-gc-text)]">{{ auth()->user()->email }}</strong>.</p>
        <p>Jika Anda tidak menerima email, periksa folder spam atau klik tombol di bawah.</p>
    </div>

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
        @csrf
        <button type="submit" class="gc-btn gc-btn-accent gc-btn-md w-full" :disabled="countdown > 0" @click="if(!resent) { resent = true; countdown = 60; const interval = setInterval(() => { countdown--; if(countdown <= 0) { clearInterval(interval); resent = false; } }, 1000) }">
            <span x-show="countdown <= 0">Kirim Ulang Link Verifikasi</span>
            <span x-show="countdown > 0" x-text="`Kirim ulang dalam ${countdown}d`"></span>
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-[var(--color-gc-border)] text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-[var(--color-gc-text-tertiary)] hover:text-[var(--color-gc-text-secondary)] transition-colors">
                Keluar &bull; Masuk dengan akun lain
            </button>
        </form>
    </div>
</div>
@endsection