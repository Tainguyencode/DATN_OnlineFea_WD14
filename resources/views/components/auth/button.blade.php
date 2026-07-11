@props([
    'type' => 'submit',
    'loadingText' => 'Đang xử lý...',
])

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'auth-btn-primary']) }}
>
    <span x-show="typeof loading === 'undefined' || !loading">{{ $slot }}</span>
    <span x-show="typeof loading !== 'undefined' && loading" x-cloak>{{ $loadingText }}</span>
</button>
