@props([
    'label' => null,
    'name',
    'required' => false,
    'id' => null,
])

@php
    $inputId = $id ?? $name;
@endphp

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $inputId }}"
        name="{{ $name }}"
        @if($required) required @endif
        {{ $attributes->except('class')->class(['auth-select']) }}
    >
        {{ $slot }}
    </select>
</div>
