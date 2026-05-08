@props([
    'placeholder' => 'Cari game, top up, akun...',
    'action' => '/search',
    'id' => 'search-bar',
])

<div class="gc-search" x-data="searchAutocomplete">
    <form action="{{ $action }}" method="GET" class="relative" role="search">
        <svg class="gc-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input
            type="text"
            name="q"
            id="{{ $id }}"
            class="gc-search-input"
            placeholder="{{ $placeholder }}"
            x-model="query"
            @input="search()"
            @keydown.down.prevent="navigateDown()"
            @keydown.up.prevent="navigateUp()"
            @keydown.enter="enterSelected()"
            @focus="if(query.length >= 2 && results.length > 0) isOpen = true"
            @blur="close()"
            aria-label="Search games and products"
            autocomplete="off"
            {{ $attributes }}
        >
        {{-- Clear button --}}
        <button type="button" x-show="query.length > 0" @click="query = ''; isOpen = false" class="absolute right-4 top-1/2 -translate-y-1/2 text-[var(--color-gc-text-tertiary)] hover:text-[var(--color-gc-text)] transition-colors" aria-label="Clear search">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>

        {{-- Autocomplete Dropdown --}}
        <div class="gc-search-dropdown" x-show="isOpen && results.length > 0" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" @mousedown.prevent>
            @foreach($results ?? [] as $index => $item)
                <a href="{{ $item['url'] ?? '#' }}" class="gc-search-result-item" :class="{ 'bg-[var(--color-gc-hover)]': selectedIndex === {{ $index }} }">
                    @isset($item['image'])
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] ?? '' }}" class="w-10 h-10 rounded-lg object-cover" loading="lazy">
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-[var(--color-gc-text)] truncate">{{ $item['name'] ?? '' }}</div>
                        @isset($item['category'])
                            <div class="text-xs text-[var(--color-gc-text-tertiary)] truncate">{{ $item['category'] }}</div>
                        @endif
                    </div>
                    @isset($item['price'])
                        <span class="text-sm font-bold text-[var(--color-gc-accent)]">{{ $item['price'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </form>
</div>