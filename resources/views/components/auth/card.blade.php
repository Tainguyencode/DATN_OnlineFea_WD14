<div {{ $attributes->merge([
    'class' => 'animate-auth-fade-in rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-8',
]) }}>
    {{ $slot }}
</div>
