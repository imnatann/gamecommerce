@props([
    'label' => '',
    'icon' => '',
    'url' => '#',
    'active' => false,
])

<a href="{{ $url }}" class="gc-category-chip {{ $active ? 'gc-category-chip-active' : '' }}">
    @if($icon)
        <span>{!! $icon !!}</span>
    @endif
    {{ $label }}
</a>