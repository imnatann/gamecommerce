@component('emails.layout', ['subject' => 'Selamat Datang di ' . config('app.name') . '!'])
<p>Halo <strong>{{ $user->name }}</strong>,</p>

<p>Selamat datang di <strong>{{ config('app.name') }}</strong>! 🎉 Kami sangat senang Anda bergabung.</p>

<p>Akun Anda telah berhasil dibuat. Sekarang Anda bisa menikmati semua fitur yang kami sediakan:</p>

<ul>
    <li>🎮 <strong>Top Up Game</strong> — Isi ulang game favorit Anda dengan harga termurah</li>
    <li>🔑 <strong>Game Key</strong> — Beli key game original dengan proses instan</li>
    <li>👤 <strong>Akun Game</strong> — Temukan akun game berkualitas dari penjual terpercaya</li>
    <li>🎟️ <strong>Voucher & Item</strong> — Dapatkan voucher dan item eksklusif</li>
</ul>

<div style="text-align: center; margin: 24px 0;">
    <a href="{{ route('home') }}" class="btn btn-accent">🎮 Jelajahi Game</a>
</div>

<div class="info-box">
    <p>💡 <strong>Tips:</strong> Lengkapi profil Anda untuk mendapatkan promo dan diskon eksklusif langsung ke inbox Anda!</p>
</div>

<p class="text-muted" style="font-size: 13px; margin-top: 16px;">Jika Anda memiliki pertanyaan, tim support kami siap membantu 24/7 melalui live chat.</p>
@endcomponent