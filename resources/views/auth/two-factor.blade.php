@extends('layouts.auth', ['title' => 'Verifikasi 2FA'])

@section('content')
<div x-data="{ code: ['', '', '', '', '', ''], recovery: false }">
    <div class="text-center mb-6">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[var(--color-gc-primary)]/10 flex items-center justify-center">
            <svg class="w-8 h-8 text-[var(--color-gc-primary)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="3" height="11" x="1" y="6" rx="1"/><path d="M7 6h2l3.5-3.5A1 1 0 0 1 14 3.5V6h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2"/><path d="M22 12h-2a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h2"/></svg>
        </div>
        <h2 class="font-[var(--font-family-display)] text-2xl font-bold text-[var(--color-gc-text)]">Verifikasi Dua Langkah</h2>
        <p class="text-sm text-[var(--color-gc-text-secondary)] mt-2">Masukkan kode dari aplikasi authenticator Anda.</p>
    </div>

    {{-- Regular 2FA Code Input --}}
    <div x-show="!recovery">
        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-3 text-center">Kode Autentikasi</label>
                <div class="flex justify-center gap-2" x-ref="codeInputs">
                    <template x-for="(digit, index) in code" :key="index">
                        <input
                            type="text"
                            maxlength="1"
                            class="w-12 h-14 text-center text-xl font-bold bg-[var(--color-gc-bg)] border border-[var(--color-gc-border)] rounded-lg text-[var(--color-gc-text)] focus:border-[var(--color-gc-primary)] focus:ring-1 focus:ring-[var(--color-gc-primary)] outline-none transition-colors"
                            x-model="code[index]"
                            @input="if($event.target.value && index < 5) { $refs.codeInputs.children[index+1]?.focus() }"
                            @keydown.backspace="if(!$event.target.value && index > 0) { $refs.codeInputs.children[index-1]?.focus() }"
                            @paste="let paste = $event.clipboardData.getData('text').replace(/\D/g,''); paste.split('').slice(0,6).forEach((ch,i) => { code[i] = ch })"
                            autocomplete="one-time-code"
                            inputmode="numeric"
                            pattern="[0-9]"
                        >
                    </template>
                </div>
                <input type="hidden" name="code" :value="code.join('')">
                @error('code')
                    <p class="text-xs text-[var(--color-gc-error)] mt-2 text-center">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="gc-btn gc-btn-accent gc-btn-md w-full">Verifikasi</button>
        </form>

        <div class="mt-4 text-center">
            <button @click="recovery = true" class="text-sm text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] transition-colors">Masuk dengan kode pemulihan</button>
        </div>
    </div>

    {{-- Recovery Code Input --}}
    <div x-show="recovery" x-cloak>
        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="recovery" value="1">

            <div>
                <label for="recovery_code" class="block text-sm font-medium text-[var(--color-gc-text-secondary)] mb-1">Kode Pemulihan</label>
                <input id="recovery_code" type="text" name="recovery_code" class="gc-input w-full text-center font-mono tracking-widest" placeholder="xxxx-xxxx" autocomplete="one-time-code" autofocus>
                @error('recovery_code')
                    <p class="text-xs text-[var(--color-gc-error)] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="gc-btn gc-btn-accent gc-btn-md w-full">Verifikasi</button>
        </form>

        <div class="mt-4 text-center">
            <button @click="recovery = false" class="text-sm text-[var(--color-gc-primary)] hover:text-[var(--color-gc-primary-hover)] transition-colors">Masuk dengan kode autentikasi</button>
        </div>
    </div>

    <div class="mt-6 pt-4 border-t border-[var(--color-gc-border)] text-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-[var(--color-gc-text-tertiary)] hover:text-[var(--color-gc-text-secondary)] transition-colors">Keluar</button>
        </form>
    </div>
</div>
@endsection