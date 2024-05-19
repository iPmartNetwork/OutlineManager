@if ($paginator->hasPages())
    <nav role="navigation" class="d-flex justify-content-center" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-tool disabled" aria-disabled="true">{!! __('pagination.previous') !!}</span>
        @else
            <a class="btn btn-tool" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="btn btn-tool" href="{{ $paginator->nextPageUrl() }}" rel="next">{!! __('pagination.next') !!}</a>
        @else
            <span class="btn btn-tool disabled" aria-disabled="true">{!! __('pagination.next') !!}</span>
        @endif
    </nav>
@endif
