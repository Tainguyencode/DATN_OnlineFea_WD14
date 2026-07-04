<div class="my-5 flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
    <span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>
    {{ $slot->isEmpty() ? 'hoặc' : $slot }}
    <span class="h-px flex-1 bg-slate-200 dark:bg-slate-800"></span>
</div>
