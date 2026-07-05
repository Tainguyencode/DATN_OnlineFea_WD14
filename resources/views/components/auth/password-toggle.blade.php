@props([
    'toggle' => 'showPassword = !showPassword',
    'visible' => 'showPassword',
])

<button
    type="button"
    x-on:click="{{ $toggle }}"
    class="absolute inset-y-0 right-3 my-auto rounded-md px-2 text-xs font-semibold text-slate-500 transition duration-200 hover:bg-slate-100 hover:text-slate-700 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200"
>
    <span x-text="{{ $visible }} ? 'Ẩn' : 'Hiện'"></span>
</button>
