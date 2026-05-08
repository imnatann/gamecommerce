@props([
    'paginator' => null,
])

@if($paginator && $paginator->hasPages())
    <nav class="gc-pagination flex-wrap gap-1 mt-8" role="navigation" aria-label="Pagination">
        {{-- Previous --}}
        @if($paginator->onFirstPage())
            <span class="gc-pagination-btn opacity-50 cursor-not-allowed" aria-disabled="true">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="gc-pagination-btn" rel="prev" aria-label="Previous page">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if($page == $paginator->currentPage())
                <span class="gc-pagination-btn gc-pagination-btn-active" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="gc-pagination-btn">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="gc-pagination-btn" rel="next" aria-label="Next page">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        @else
            <span class="gc-pagination-btn opacity-50 cursor-not-allowed" aria-disabled="true">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </span>
        @endif
    </nav>
@endif