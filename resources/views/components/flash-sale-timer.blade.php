@props([
    'endTime' => '',
    'label' => 'Flash Sale',
])

<div x-data="countdownTimer('{{ $endTime }}')" class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
    @if($label)
        <div class="flex items-center gap-1.5">
            <svg class="w-5 h-5 text-[var(--color-gc-warning)]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span class="font-bold text-[var(--color-gc-warning)] text-sm sm:text-base">{{ $label }}</span>
        </div>
    @endif

    <div class="gc-countdown">
        @if($days > 0)
            <div class="gc-countdown-block">
                <span class="gc-countdown-value" x-text="String(days).padStart(2, '0')"></span>
                <span class="gc-countdown-label">Hari</span>
            </div>
            <span class="gc-countdown-separator">:</span>
        @endif
        <div class="gc-countdown-block">
            <span class="gc-countdown-value" x-text="String(hours).padStart(2, '0')"></span>
            <span class="gc-countdown-label">Jam</span>
        </div>
        <span class="gc-countdown-separator">:</span>
        <div class="gc-countdown-block">
            <span class="gc-countdown-value" x-text="String(minutes).padStart(2, '0')"></span>
            <span class="gc-countdown-label">Menit</span>
        </div>
        <span class="gc-countdown-separator">:</span>
        <div class="gc-countdown-block">
            <span class="gc-countdown-value" x-text="String(seconds).padStart(2, '0')"></span>
            <span class="gc-countdown-label">Detik</span>
        </div>
    </div>

    <template x-if="expired">
        <span class="text-sm font-semibold text-[var(--color-gc-error)]">Sale Berakhir</span>
    </template>
</div>