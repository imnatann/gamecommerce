@component('emails.layout', ['subject' => 'Verifikasi Email - ' . config('app.name')])
<p>Halo <strong>{{ $user->name }}</strong>,</p>

<p>Terima kasih telah mendaftar di <strong>{{ config('app.name') }}</strong>! Untuk mengaktifkan akun Anda, silakan verifikasi email Anda dengan klik tombol di bawah ini:</p>

<div style="text-align: center; margin: 24px 0;">
    <a href="{{ $url }}" class="btn">Verifikasi Email Saya</a>
</div>

<p class="text-muted" style="font-size: 13px;">Jika tombol di atas tidak berfungsi, salin tautan berikut dan tempelkan ke browser Anda:</p>

<p style="font-size: 12px; word-break: break-all; color: #6C3FE8;">{{ $url }}</p>

<div class="divider"></div>

<div class="warning-box">
    <p>⚠️ <strong>Penting:</strong> Tautan verifikasi ini berlaku selama 24 jam. Jika Anda tidak merasa mendaftar di {{ config('app.name') }}, mohon abaikan email ini.</p>
</div>

<p>Setelah verifikasi, Anda bisa langsung mulai belanja game, top up, dan voucher favorit Anda!</p>
@endcomponent