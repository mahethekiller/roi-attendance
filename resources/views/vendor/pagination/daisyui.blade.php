@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3">
        <div class="text-sm text-base-content/70">
            Showing <span class="font-semibold text-base-content">{{ $paginator->firstItem() }}</span> to <span class="font-semibold text-base-content">{{ $paginator->lastItem() }}</span> of <span class="font-semibold text-base-content">{{ $paginator->total() }}</span> results
        </div>
        <div class="join">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button class="join-item btn btn-sm btn-disabled" disabled aria-disabled="true">«</button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="join-item btn btn-sm">«</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <button class="join-item btn btn-sm btn-disabled" disabled>{{ $element }}</button>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="join-item btn btn-sm btn-primary" aria-current="page">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-sm">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="join-item btn btn-sm">»</a>
            @else
                <button class="join-item btn btn-sm btn-disabled" disabled aria-disabled="true">»</button>
            @endif
        </div>
    </nav>
@endif
