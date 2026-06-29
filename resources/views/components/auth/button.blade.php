@props([
    'type' => 'submit',
    'loadingText' => 'Đang xử lý...',
])

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'auth-btn-primary']) }}
>
    <span x-show="!loading">{{ $slot }}</span>
    <span x-show="loading" x-cloak>{{ $loadingText }}</span>
</button>
