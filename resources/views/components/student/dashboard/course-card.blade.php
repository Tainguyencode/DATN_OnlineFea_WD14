@props(['course', 'progress' => null, 'status' => null, 'viewedAt' => null])
@php
    $target = $progress !== null
        ? ($course->learningEntryUrl() ?? route('courses.show', $course->slug))
        : route('courses.show', $course->slug);
@endphp
<article {{ $attributes->class('flex h-full min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900') }}>
    <a href="{{ route('courses.show', $course->slug) }}" class="relative block aspect-video overflow-hidden bg-blue-50 dark:bg-slate-800">
        <img src="{{ $course->thumbnailUrl() }}" alt="Ảnh khóa học {{ $course->title }}" loading="lazy" class="h-full w-full object-cover" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex')">
        <span class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-blue-100 to-indigo-50 text-[#0056D2] dark:from-slate-800 dark:to-blue-950/40 dark:text-blue-300">
            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5a2.5 2.5 0 0 1 2.5 2.5v-16Z"/></svg>
        </span>
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-2">
            <span class="truncate text-xs font-extrabold uppercase tracking-wide text-[#0056D2] dark:text-blue-300">{{ $course->category?->name ?? 'Khóa học' }}</span>
            @if($status)<x-student.dashboard.status-badge :status="$status" />@endif
        </div>
        <h2 class="mt-2 line-clamp-2 text-base font-extrabold leading-6 text-slate-950 dark:text-white" title="{{ $course->title }}">{{ $course->title }}</h2>
        <p class="mt-1 truncate text-sm text-slate-500 dark:text-slate-400">Giảng viên: {{ $course->instructor?->name ?? 'Đang cập nhật' }}</p>
        @if($viewedAt)<p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Xem gần nhất {{ $viewedAt->diffForHumans() }}</p>@endif
        @if($progress !== null)
            <div class="mt-4">
                <div class="mb-1.5 flex justify-between text-xs font-semibold text-slate-500"><span>Tiến độ</span><span>{{ number_format((float) $progress) }}%</span></div>
                <progress class="h-2 w-full overflow-hidden rounded-full [&::-moz-progress-bar]:bg-[#0056D2] [&::-webkit-progress-bar]:bg-slate-100 [&::-webkit-progress-value]:bg-[#0056D2] dark:[&::-webkit-progress-bar]:bg-slate-800" max="100" value="{{ min(100, (float) $progress) }}">{{ number_format((float) $progress) }}%</progress>
            </div>
        @endif
        <div class="mt-auto pt-5">
            @isset($actions)
                {{ $actions }}
            @else
                <a href="{{ $target }}" class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-[#0056D2] px-4 text-sm font-bold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#0046B8] hover:shadow-md active:translate-y-0 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-2">{{ $progress !== null ? 'Tiếp tục học' : 'Xem chi tiết' }}</a>
            @endisset
        </div>
    </div>
</article>
