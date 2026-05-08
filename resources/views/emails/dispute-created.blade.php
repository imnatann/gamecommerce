@component('emails.layout', ['subject' => 'Dispute Baru #' . $dispute->id])
<p>Ada dispute baru yang memerlukan perhatian Anda.</p>

<table class="order-table">
    <thead>
        <tr>
            <th>Detail</th>
            <th class="text-right">Info</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>ID Dispute</td>
            <td class="text-right"><strong>#{{ $dispute->id }}</strong></td>
        </tr>
        <tr>
            <td>ID Pesanan</td>
            <td class="text-right"><strong>{{ $dispute->order?->order_id ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>Alasan</td>
            <td class="text-right">{{ $dispute->reason }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $dispute->created_at->format('d M Y, H:i') }}</td>
        </tr>
    </tbody>
</table>

@if($dispute->description)
<div class="divider"></div>

<h2 style="font-size: 16px; margin-bottom: 12px;">📝 Deskripsi</h2>
<p>{{ $dispute->description }}</p>
@endif

<div style="text-align: center; margin: 24px 0;">
    <a href="{{ route('orders.index') }}" class="btn">Lihat Detail Dispute</a>
</div>

<div class="warning-box">
    <p>⚠️ <strong>Penting:</strong> Silakan respons dispute ini dalam waktu 24 jam untuk menghindari penyelesaian otomatis.</p>
</div>
@endcomponent