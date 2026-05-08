@props([
    'icon' => '',
    'title' => 'Tidak Ada Hasil',
    'description' => 'Tidak ditemukan item yang sesuai dengan pencarian Anda.',
    'actionText' => '',
    'actionUrl' => '#',
])

<div class="gc-empty-state">
    @if($icon)
        <div class="gc-empty-state-icon">
            {!! $icon !!}
        </div>
    @else
        <div class="gc-empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </div>
    @endif

    <h3 class="gc-empty-state-title">{{ $title }}</h3>
    <p class="gc-empty-state-desc">{{ $description }}</p>

    @if($actionText)
        <a href="{{ $actionUrl }}" class="gc-btn gc-btn-primary gc-btn-md">
            {{ $actionText }}
        </a>
    @endif
</div>