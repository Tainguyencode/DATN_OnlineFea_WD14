@props(['question'])

<div class="grid gap-3 sm:grid-cols-[1fr_120px]">
    <div class="flex h-[50px] items-center rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 text-sm font-semibold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        {{ $question }}
    </div>
    <input
        type="text"
        name="captcha_answer"
        inputmode="numeric"
        placeholder="Kết quả"
        class="auth-input"
    >
</div>
