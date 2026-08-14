@props(['paginator'])
@if ($paginator->hasPages())
    <nav class='pagination-wrap' aria-label='Pagination'>{{ $paginator->onEachSide(1)->links() }}</nav>
@endif
