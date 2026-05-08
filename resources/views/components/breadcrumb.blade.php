@props([
    'items' => [],
])

<nav class="gc-breadcrumb py-3" aria-label="Breadcrumb">
    <ol class="flex items-center flex-wrap gap-0" itemscope itemtype="https://schema.org/BreadcrumbList">
        @foreach($items as $index => $item)
            <li class="flex items-center" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if($index > 0)
                    <span class="gc-breadcrumb-separator" aria-hidden="true">
                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                    </span>
                @endif
                @if(isset($item['url']) && $index < count($items) - 1)
                    <a href="{{ $item['url'] }}" class="gc-breadcrumb-link" itemprop="item">
                        <span itemprop="name">{{ $item['label'] ?? $item }}</span>
                    </a>
                @else
                    <span class="gc-breadcrumb-current" itemprop="name">{{ $item['label'] ?? $item }}</span>
                @endif
                <meta itemprop="position" content="{{ $index + 1 }}" />
            </li>
        @endforeach
    </ol>
</nav>