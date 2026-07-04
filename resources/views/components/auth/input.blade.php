@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'id' => null,
    'autofocus' => false,
    'inputClass' => '',
])

@php
    $inputId = $id ?? $name;
    $resolvedValue = $value ?? old($name);
@endphp

<div {{ $attributes->only('class') }}>
    @if($label || isset($labelAction))
        <div class="mb-2 flex items-center justify-between gap-3">
            @if($label)
                <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                    {{ $label }}
                </label>
            @endif
            @isset($labelAction)
                {{ $labelAction }}
            @endisset
        </div>
    @endif

    <div class="relative">
        <input
            @unless($attributes->has('x-bind:type') || $attributes->has(':type'))
                type="{{ $type }}"
            @endunless
            id="{{ $inputId }}"
            name="{{ $name }}"
            @if(! is_null($resolvedValue) && $resolvedValue !== '') value="{{ $resolvedValue }}" @endif
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($autofocus) autofocus @endif
            {{ $attributes->except('class')->class(['auth-input', $inputClass]) }}
        />

        @isset($trailing)
            {{ $trailing }}
        @endisset
    </div>

    @isset($hint)
        <div class="mt-1.5">
            {{ $hint }}
        </div>
    @endisset
</div>
