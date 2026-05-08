@component('emails.layout', ['subject' => 'Pembayaran Dikonfirmasi #' . $order->order_id])
<p>Halo <strong>{{ $order->user_name }}</strong>,</p>

<div style="text-align: center; margin: 20px 0;">
    <span class="status-badge status-badge-paid" style="font-size: 14px; padding: 8px 20px;">✓ Pembayaran Dikonfirmasi</span>
</div>

<p>Pembayaran Anda telah berhasil dikonfirmasi. Kami sedang memproses pesanan Anda.</p>

<table class="order-table">
    <thead>
        <tr>
            <th>Detail</th>
            <th class="text-right">Info</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>ID Pesanan</td>
            <td class="text-right"><strong>{{ $order->order_id }}</strong></td>
        </tr>
        <tr>
            <td>Metode Pembayaran</td>
            <td class="text-right">{{ $order->payment_method }}</td>
        </tr>
        <tr>
            <td>Tanggal Pembayaran</td>
            <td class="text-right">{{ $order->paid_at ?? now()->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <td>Total Dibayar</td>
            <td class="text-right"><span class="text-accent">Rp {{ number_format($order->total, 0, ',', '.') }}</span></td>
        </tr>
    </tbody>
</table>

<div class="divider"></div>

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
            </td>
            <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="text-align: center; margin: 24px 0;">
    <a href="{{ route('order.status', $order->order_id) }}" class="btn">Lihat Status Pesanan</a>
</div>

<div class="info-box">
    <p>🎮 <strong>Produk digital</strong> akan dikirim secara otomatis setelah pembayaran dikonfirmasi. Produk yang memerlukan proses manual akan dikirim dalam waktu yang ditentukan.</p>
</div>

<p class="text-muted">Anda akan menerima email berikutnya ketika produk telah dikirim.</p>
@endcomponent