@props(['question'])

<div class="grid gap-3 sm:grid-cols-[1fr_120px]">
    <div class="flex h-[50px] items-center rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 text-sm font-semibold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        {{ $question }}
    </div>
    <div>
        <input
            type="text"
            name="captcha_answer"
            inputmode="numeric"
            placeholder="Kết quả"
            class="auth-input @error('captcha_answer') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"
        >
        @error('captcha_answer')
            <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>
