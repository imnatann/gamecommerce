@component('emails.layout', ['subject' => 'Pesanan Selesai #' . $order->order_id])
<p>Halo <strong>{{ $order->user_name }}</strong>,</p>

<div style="text-align: center; margin: 20px 0;">
    <span class="status-badge status-badge-completed" style="font-size: 14px; padding: 8px 20px;">✓ Pesanan Selesai</span>
</div>

<p>Pesanan Anda telah selesai! Terima kasih sudah berbelanja di GameCommerce.</p>

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
        <tr class="total-row">
            <td><strong>Total</strong></td>
            <td class="text-right"><span class="total-price">Rp {{ number_format($order->total, 0, ',', '.') }}</span></td>
        </tr>
    </tbody>
</table>

<div class="divider"></div>

<h2 style="font-size: 16px; margin-bottom: 12px;">⭐ Bagaimana pengalaman Anda?</h2>

<p>Ulasan Anda membantu pembeli lain membuat keputusan yang tepat!</p>

@foreach($order->items as $item)
<div style="margin: 12px 0; padding: 12px; background-color: #0f0f1a; border-radius: 8px;">
    <p style="margin-bottom: 8px;"><strong>{{ $item->product_name }}</strong></p>
    <a href="{{ route('product.review', $item->product_slug ?? '#') }}" class="btn btn-accent" style="font-size: 12px; padding: 8px 20px;">Beri Ulasan</a>
</div>
@endforeach

<div class="divider"></div>

<h2 style="font-size: 16px; margin-bottom: 12px;">🎮 Beli Lagi?</h2>

<p>Temukan penawaran terbaik untuk game favorit Anda!</p>

<div style="text-align: center; margin: 20px 0;">
    <a href="{{ route('home') }}" class="btn">Jelajahi GameCommerce</a>
</div>

<div class="info-box">
    <p>💬 <strong>Butuh bantuan?</strong> Hubungi customer service kami via live chat atau WhatsApp untuk bantuan terkait pesanan.</p>
</div>
@endcomponent