@props([
    'type' => 'submit',
    'loadingText' => 'Đang xử lý...',
])

@php
    $label = trim((string) $slot);
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'auth-btn-primary']) }}
>
    <span x-text="loading ? @js($loadingText) : @js($label)">{{ $label }}</span>
</button>
