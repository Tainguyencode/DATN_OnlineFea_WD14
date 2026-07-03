@props(['label', 'value', 'icon' => '', 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => 'from-indigo-500 to-violet-600',
        'emerald' => 'from-emerald-500 to-teal-600',
        'amber' => 'from-amber-500 to-orange-500',
        'rose' => 'from-rose-500 to-pink-600',
        'blue' => 'from-blue-500 to-cyan-500',
        'purple' => 'from-purple-500 to-indigo-600',
    ];
    $gradient = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-[0_14px_34px_rgba(15,23,42,0.06)] transition-shadow duration-200 hover:shadow-[0_18px_40px_rgba(15,23,42,0.08)]">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm text-slate-500 font-medium truncate">{{ $label }}</p>
            <p class="text-2xl sm:text-3xl font-bold text-slate-900 mt-2 tracking-normal">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br {{ $gradient }} flex shrink-0 items-center justify-center text-white shadow-lg shadow-slate-900/10">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
