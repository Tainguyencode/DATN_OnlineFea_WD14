@props(['title', 'description' => null])

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl dark:text-white">{{ $title }}</h1>
        @if($description)
            <p class="mt-1.5 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="shrink-0">{{ $actions }}</div>
    @endisset
</div>
