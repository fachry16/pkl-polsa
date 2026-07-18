@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="pagination-nav">

        <div class="flex gap-2 items-center justify-between sm-hidden">

            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-secondary disabled">{{ __('pagination.previous') }}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-sm btn-secondary">{{ __('pagination.previous') }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-sm btn-secondary">{{ __('pagination.next') }}</a>
            @else
                <span class="btn btn-sm btn-secondary disabled">{{ __('pagination.next') }}</span>
            @endif

        </div>

        <div class="pagination-info-block">

            <div>
                <p class="text-sm">
                    {{ __('Showing') }}
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        {{ __('to') }}
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {{ __('of') }}
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    {{ __('results') }}
                </p>
            </div>

            <div class="pagination">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span aria-hidden="true">&lsaquo;</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">&lsaquo;</a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="disabled" aria-disabled="true"><span>{{ $element }}</span></span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="active" aria-current="page"><span>{{ $page }}</span></span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">&rsaquo;</a>
                @else
                    <span class="disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span aria-hidden="true">&rsaquo;</span>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
