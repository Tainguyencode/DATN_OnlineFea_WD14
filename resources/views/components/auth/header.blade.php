@props([
    'title',
    'subtitle' => null,
    'showLogo' => false,
])

<div class="mb-6 text-center">
    @if($showLogo)
        <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="mx-auto mb-5 h-12 w-auto object-contain">
    @endif

    <h1 class="text-3xl font-bold leading-tight tracking-tight text-slate-900 dark:text-white">
        {{ $title }}
    </h1>

    @if($subtitle)
        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
            {{ $subtitle }}
        </p>
    @endif
</div>
