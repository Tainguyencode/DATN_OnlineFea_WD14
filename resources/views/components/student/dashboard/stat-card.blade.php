@props(['label', 'value', 'href' => null, 'tone' => 'blue', 'icon' => 'book'])
@php
    $tones = [
        'blue' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
        'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
        'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
        'violet' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-300',
    ];
@endphp
<article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900" aria-label="{{ $label }}: {{ $value }}">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-950 dark:text-white">{{ number_format((int) $value) }}</p>
        </div>
        <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $tones[$tone] ?? $tones['blue'] }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                @if($icon === 'play')<path stroke-linecap="round" stroke-linejoin="round" d="m9 7 8 5-8 5V7Z"/>
                @elseif($icon === 'check')<path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                @elseif($icon === 'award')<circle cx="12" cy="9" r="6"/><path stroke-linecap="round" d="m8.5 14-1 7 4.5-2 4.5 2-1-7"/>
                @else<path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5a2.5 2.5 0 0 1 2.5 2.5v-16Z"/>@endif
            </svg>
        </span>
    </div>
    @if($href)<a href="{{ $href }}" class="mt-3 inline-flex text-xs font-bold text-[#0056D2] hover:underline dark:text-blue-300">Xem chi tiết <span aria-hidden="true">→</span></a>@endif
</article>
