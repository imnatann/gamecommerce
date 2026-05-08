@props([
    'name' => 'modal',
    'maxWidth' => 'md',
])

@php
    $maxWidthClass = match($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '4xl' => 'max-w-4xl',
        'full' => 'max-w-full',
        default => 'max-w-md',
    };
@endphp

<div x-data="modal()" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="gc-modal-backdrop" @keydown.escape.prevent="hide()" aria-modal="true" role="dialog" {{ $attributes->merge(['class' => '']) }}>

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="hide()"></div>

    {{-- Panel --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="gc-modal-panel {{ $maxWidthClass }}" @click.stop>
        {{-- Header --}}
        @if(isset($title) || isset($header))
            <div class="gc-modal-header">
                <h3 class="font-[var(--font-family-display)] text-lg font-semibold text-[var(--color-gc-text)]">
                    {{ $title ?? '' }}
                </h3>
                <button @click="hide()" class="gc-btn-icon gc-btn-ghost" aria-label="Close modal">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        @endif

        {{-- Body --}}
        <div class="gc-modal-body">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @if(isset($footer))
            <div class="gc-modal-footer">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>