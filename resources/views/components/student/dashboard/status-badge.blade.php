@props(['status'])
@php
    [$label, $class] = match($status) {
        'completed', 'paid', 'active' => [$status === 'completed' ? 'Hoàn thành' : ($status === 'paid' ? 'Đã thanh toán' : 'Còn hiệu lực'), 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:ring-emerald-900'],
        'in_progress', 'pending' => [$status === 'pending' ? 'Đang chờ' : 'Đang học', 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:ring-amber-900'],
        'used' => ['Đã sử dụng', 'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700'],
        'refunded' => ['Đã hoàn tiền', 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-950/40 dark:text-violet-300 dark:ring-violet-900'],
        'cancelled' => ['Đã hủy', 'bg-slate-100 text-slate-700 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700'],
        default => ['Hết hiệu lực', 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:ring-rose-900'],
    };
@endphp
<span {{ $attributes->class("inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 ring-inset $class") }}>{{ $label }}</span>
