<style>

    .pagination-nav{
   
        text-align: right;
        margin-right: 20px;
    }
    .btns > button{
        width: 60px;
        height: 40px;
        border: 0;
        border-radius: 4px;
        background-color: #2B2B2B;
        text-align: center;


    }

</style>
@if ($paginator->hasPages())
    <nav class="pagination-nav">

        <div class="btns">
         
             
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <button class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="page-link" aria-hidden="true">&lsaquo;</span>
                        </button>
                    @else
                        <button class="page-item">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <button class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></button>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <button class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></button>
                                @else
                                    <button class="page-item"><a style="width: 100%; height:100%" class="page-link" href="{{ $url }}">{{ $page }}</a></button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button class="page-item">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </button>
                    @else
                        <button class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="page-link" aria-hidden="true">&rsaquo;</span>
                        </button>
                    @endif
            
         
        </div>
    </nav>
@endif
