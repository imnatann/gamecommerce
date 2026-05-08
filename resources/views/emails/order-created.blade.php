@component('emails.layout', ['subject' => 'Pesanan Dibuat #' . $order->order_id])
<p>Halo <strong>{{ $order->user_name }}</strong>,</p>
<p>Pesanan Anda telah berhasil dibuat! Berikut detail pesanan Anda:</p>

<table class="order-table">
    <thead>
        <tr>
            <th>Produk</th>
            <th class="text-right">Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>
                <strong>{{ $item->product_name }}</strong><br>
                <span class="text-muted">{{ $item->game_name }}</span>
                @if($item->variant)
                    <br><span class="text-muted">{{ $item->variant }}</span>
                @endif
            </td>
            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        @if($order->discount > 0)
        <tr>
            <td class="text-muted">Voucher</td>
            <td class="text-right text-accent">-Rp {{ number_format($order->discount, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-right"><span class="total-price">Rp {{ number_format($order->total, 0, ',', '.') }}</span></td>
        </tr>
    </tbody>
</table>

<div class="divider"></div>

<p><strong>Status:</strong> <span class="status-badge status-badge-pending">Menunggu Pembayaran</span></p>

<p>Silakan selesaikan pembayaran dalam <strong>{{ $order->payment_timeout ?? '30 menit' }}</strong> agar pesanan tidak dibatalkan.</p>

<div style="text-align: center; margin: 24px 0;">
    <a href="{{ $order->payment_url ?? route('order.status', $order->order_id) }}" class="btn">Bayar Sekarang</a>
</div>

<div class="info-box">
    <p>💡 <strong>Pesan cepat:</strong> Pesanan dengan pembayaran instan (QRIS, e-wallet) akan diproses lebih cepat!</p>
</div>

<p>ID Pesanan: <strong>{{ $order->order_id }}</strong></p>
<p class="text-muted" style="font-size: 12px; margin-top: 4px;">Simpan ID ini untuk melacak status pesanan Anda.</p>
@endcomponent