@props([
    'title',
    'description' => null,
    'backUrl' => null,
    'showBack' => true,
])

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl dark:text-white">{{ $title }}</h1>
        @if($description)
            <p class="mt-1.5 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $description }}</p>
        @endif
    </div>
    <div class="flex items-center gap-3 shrink-0">
        @isset($actions)
            {{ $actions }}
        @endisset

        @if($showBack)
            <button type="button"
                    onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ $backUrl ?? route('student.dashboard') }}'; }"
                    class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition-all duration-200 hover:-translate-x-1 hover:border-[#0056D2]/40 hover:bg-blue-50/60 hover:text-[#0056D2] hover:shadow active:scale-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-blue-500/40 dark:hover:bg-slate-800 dark:hover:text-blue-400 dark:hover:shadow-slate-950/50">
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Quay lại</span>
            </button>
        @endif
    </div>
</div>

