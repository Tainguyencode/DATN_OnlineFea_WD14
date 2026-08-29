@props(['paginator'])
@if($paginator->hasPages())
    <div {{ $attributes->class('mt-7') }}>{{ $paginator->onEachSide(1)->links() }}</div>
@endif
