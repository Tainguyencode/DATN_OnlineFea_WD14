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

<div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-500 font-medium">{{ $label }}</p>
            <p class="text-3xl font-bold text-slate-900 mt-2">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-white shadow-lg">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
