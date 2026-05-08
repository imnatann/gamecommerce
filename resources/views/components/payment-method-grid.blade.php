@props([
    'methods' => [],
])

<div class="gc-payment-grid">
    @forelse($methods as $method)
        <div class="gc-payment-item">
            @if(isset($method['icon']))
                <img src="{{ $method['icon'] }}" alt="{{ $method['name'] ?? '' }}" class="h-6 max-w-[4rem] object-contain" loading="lazy">
            @elseif(isset($method['name']))
                <span class="text-xs text-[var(--color-gc-text-secondary)] font-medium">{{ $method['name'] }}</span>
            @endif
        </div>
    @empty
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">QRIS</span></div>
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">BCA</span></div>
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">BNI</span></div>
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">BRI</span></div>
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">GoPay</span></div>
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">OVO</span></div>
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">DANA</span></div>
        <div class="gc-payment-item"><span class="text-xs text-[var(--color-gc-text-secondary)]">ShopeePay</span></div>
    @endforelse
</div>