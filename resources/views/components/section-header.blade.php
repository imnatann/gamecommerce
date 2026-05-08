@props([
    'title' => '',
    'url' => '#',
    'linkText' => 'Lihat Semua',
    'icon' => '',
])

<div class="gc-section-header">
    <div class="flex items-center gap-2">
        @if($icon)
            <span class="text-[var(--color-gc-primary)]">{!! $icon !!}</span>
        @endif
        <h2 class="gc-section-title">{{ $title }}</h2>
    </div>
    @if($url !== '#')
        <a href="{{ $url }}" class="gc-section-link">
            {{ $linkText }}
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
    @endif
</div>