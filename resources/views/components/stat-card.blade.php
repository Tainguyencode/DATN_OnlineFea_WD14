@props(['label', 'value', 'icon' => '', 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => 'text-[#0056D2] bg-blue-50 dark:bg-blue-950/40 dark:text-blue-300',
        'emerald' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/40 dark:text-emerald-400',
        'amber' => 'text-amber-600 bg-amber-50 dark:bg-amber-900/40 dark:text-amber-300',
        'rose' => 'text-rose-600 bg-rose-50 dark:bg-rose-900/40 dark:text-rose-300',
        'blue' => 'text-[#0056D2] bg-blue-50 dark:bg-blue-950/40 dark:text-blue-300',
        'purple' => 'text-[#0056D2] bg-blue-50 dark:bg-blue-950/40 dark:text-blue-300',
    ];
    $accentClass = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $accentClass }}">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
