<x-student-layout title="Khóa học của tôi" page-title="Khóa học của tôi">

<div class="mb-6 flex items-center justify-between gap-4">
    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $enrollments->total() }} khóa học đã đăng ký</p>
    <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 shadow-sm">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Khám phá thêm
    </a>
</div>

@if($enrollments->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 p-12 text-center shadow-sm">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-[#0056D2] dark:text-blue-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <h2 class="mt-5 text-xl font-bold text-slate-950 dark:text-white">Bạn chưa đăng ký khóa học nào</h2>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Khám phá danh sách khóa học và bắt đầu hành trình học tập ngay hôm nay.</p>
        <a href="{{ route('courses.index') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl bg-[#0056D2] px-6 text-sm font-bold text-white transition hover:bg-[#0046B8] shadow-sm">
            Khám phá khóa học
        </a>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($enrollments as $enrollment)
            @php
                $course = $enrollment->course;
                $progress = (float) ($enrollment->progress_percent ?? 0);
                $isCompleted = $enrollment->status === \App\Models\Enrollment::STATUS_COMPLETED || $enrollment->completed_at !== null;
            @endphp
            @continue(! $course)

            <article class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg flex flex-col h-full">
                <a href="{{ route('courses.show', $course->slug) }}" class="block aspect-video overflow-hidden bg-slate-900">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-4xl font-extrabold text-white/70">FEA</div>
                    @endif
                </a>

                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex items-center justify-between gap-3">
                        <span class="truncate text-xs font-bold uppercase tracking-wide text-[#0056D2] dark:text-blue-300">{{ $course->category?->name ?? 'Khóa học' }}</span>
                        @if($isCompleted)
                            <span class="rounded-full bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-200 ring-1 ring-emerald-200 dark:ring-emerald-900">Hoàn thành</span>
                        @else
                            <span class="rounded-full bg-blue-50 dark:bg-blue-950/50 px-2.5 py-1 text-xs font-bold text-blue-700 dark:text-blue-200 ring-1 ring-blue-200 dark:ring-blue-900">Đang học</span>
                        @endif
                    </div>

                    <h3 class="mt-3 line-clamp-2 text-lg font-extrabold leading-snug text-slate-950 dark:text-white">
                        {{ $course->title }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Giảng viên: {{ $course->instructor?->name ?? 'FEA Instructor' }}</p>

                    <div class="mt-auto pt-5">
                        <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span>Tiến độ</span>
                            <span>{{ number_format($progress, 0) }}%</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-[#0056D2] transition-all duration-300" style="width: {{ min(100, $progress) }}%"></div>
                        </div>

                        <a href="{{ $course->learningEntryUrl() ?? route('courses.show', $course->slug) }}" class="mt-5 flex h-11 w-full items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white transition hover:bg-[#0056D2] dark:bg-white dark:text-slate-950 dark:hover:bg-blue-100">
                            Tiếp tục học
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8">{{ $enrollments->links() }}</div>
@endif

</x-student-layout>
