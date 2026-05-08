@props([
    'status' => 'processing',
])

@php
    $statusConfig = [
        'pending'     => ['label' => 'Menunggu Pembayaran', 'class' => 'gc-badge-processing'],
        'processing' => ['label' => 'Diproses',            'class' => 'gc-badge-processing'],
        'shipped'     => ['label' => 'Dikirim',             'class' => 'gc-badge-shipped'],
        'delivered'   => ['label' => 'Diterima',            'class' => 'gc-badge-delivered'],
        'completed'   => ['label' => 'Selesai',             'class' => 'gc-badge-delivered'],
        'cancelled'   => ['label' => 'Dibatalkan',          'class' => 'gc-badge-cancelled'],
        'refunded'    => ['label' => 'Dikembalikan',        'class' => 'gc-badge-refunded'],
        'failed'      => ['label' => 'Gagal',               'class' => 'gc-badge-cancelled'],
    ];
    $config = $statusConfig[$status] ?? $statusConfig['processing'];
@endphp

<span class="gc-badge {{ $config['class'] }}">{{ $config['label'] }}</span>