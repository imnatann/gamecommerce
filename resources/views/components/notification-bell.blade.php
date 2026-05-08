@props([
    'count' => 0,
    'url' => '#',
])

<div class="relative">
    <a href="{{ $url }}" class="gc-btn-icon gc-btn-ghost" aria-label="Notifications" aria-live="polite">
        <svg class="gc-icon-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
        @if($count > 0)
            <span class="gc-notification-badge" x-data="{{ '{ count: ' . $count . ' }' }}" x-text="count">{{ $count }}</span>
        @endif
    </a>
</div>