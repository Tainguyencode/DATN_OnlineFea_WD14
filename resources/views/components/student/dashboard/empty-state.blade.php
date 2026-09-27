@props([
    'title',
    'description' => null,
    'actionUrl' => null,
    'actionLabel' => null,
])

<div {{ $attributes->class('rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-center dark:border-slate-700 dark:bg-slate-900') }}>
    <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.3v13m0-13C10.8 5.5 9.2 5 7.5 5S4.2 5.5 3 6.3v13c1.2-.8 2.8-1.3 4.5-1.3s3.3.5 4.5 1.3m0-13C13.2 5.5 14.8 5 16.5 5s3.3.5 4.5 1.3v13c-1.2-.8-2.8-1.3-4.5-1.3s-3.3.5-4.5 1.3"/></svg>
    </div>
    <h2 class="mt-3 text-base font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
    @if($description)<p class="mx-auto mt-1 max-w-lg text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</p>@endif
    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0056D2] px-5 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0046B8] hover:shadow-md active:translate-y-0 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-2">{{ $actionLabel }}</a>
    @endif
</div>
