@props([
    'text',
    'linkText',
    'href',
])

<p class="mt-6 text-center text-sm text-slate-500 dark:text-slate-400">
    {{ $text }}
    <a href="{{ $href }}" class="font-semibold text-[#0056D2] transition duration-200 hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200">
        {{ $linkText }}
    </a>
</p>
