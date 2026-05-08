@component('emails.layout', ['subject' => 'Produk Dikirim #' . $order->order_id])
<p>Halo <strong>{{ $order->user_name }}</strong>,</p>

<div style="text-align: center; margin: 20px 0;">
    <span class="status-badge status-badge-delivered" style="font-size: 14px; padding: 8px 20px;">✓ Produk Dikirim</span>
</div>

<p>Pesanan Anda telah dikirim! Berikut detail produk Anda:</p>

@foreach($order->items as $item)
<div class="divider"></div>

<div style="margin-bottom: 8px;">
    <strong>{{ $item->product_name }}</strong>
    <span class="text-muted" style="margin-left: 8px;">{{ $item->game_name }}</span>
</div>

@if($item->delivery_type === 'instant')
    {{-- Instant delivery: show code --}}
    @foreach($item->codes as $code)
    <div class="code-box">
        <code>{{ $code->value }}</code>
        @if($code->label)
            <small>{{ $code->label }}</small>
        @endif
    </div>
    @endforeach

    <div class="info-box">
        <p>📋 <strong>Salin kode</strong> dan masukkan ke game/aplikasi Anda. Kode ini hanya berlaku untuk satu kali penggunaan.</p>
    </div>
@elseif($item->delivery_type === 'account')
    {{-- Account delivery: show credentials --}}
    <div class="code-box">
        <code style="font-size: 14px;">{{ $item->account_email ?? $item->account_username }}</code>
        <small>Username/Email</small>
    </div>
    @if($item->account_password)
    <div class="code-box">
        <code style="font-size: 14px;">{{ $item->account_password }}</code>
        <small>Password</small>
    </div>
    @endif

    <div class="warning-box">
        <p>⚠️ <strong>Penting:</strong> Segera ganti password akun setelah login. Jangan bagikan data akun ini kepada siapapun.</p>
    </div>
@else
    {{-- Manual delivery: show delivery info --}}
    <div class="info-box">
        <p>⏳ <strong>Pengiriman manual:</strong> Penjual sedang memproses pesanan Anda. Estimasi pengiriman: {{ $item->estimated_delivery ?? '1-24 jam' }}.</p>
    </div>
@endif
@endforeach

<div class="divider"></div>

<table class="order-table">
    <thead>
        <tr>
            <th>Ringkasan</th>
            <th class="text-right">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>ID Pesanan</td>
            <td class="text-right">{{ $order->order_id }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td class="text-right"><span class="total-price">Rp {{ number_format($order->total, 0, ',', '.') }}</span></td>
        </tr>
    </tbody>
</table>

<div style="text-align: center; margin: 24px 0;">
    <a href="{{ route('order.status', $order->order_id) }}" class="btn">Lihat Detail Pesanan</a>
</div>
@endcomponent