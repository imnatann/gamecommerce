@props(['name' => '', 'slug' => '', 'icon' => '', 'url' => '#'])

<a href="{{ $url }}" class="gc-card gc-card-game gc-hover-lift">
    @if($icon)
        <img src="{{ $icon }}" alt="{{ $name }}" class="gc-game-icon-img" loading="lazy">
    @else
        <div class="gc-game-icon-img flex items-center justify-center text-[var(--color-gc-text-tertiary)]">
            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 12h4m4 0h4"/><rect width="20" height="12" x="2" y="6" rx="2"/><path d="M12 18v4"/><path d="M8 22h8"/></svg>
        </div>
    @endif
    <span class="gc-game-icon-name">{{ $name }}</span>
</a>