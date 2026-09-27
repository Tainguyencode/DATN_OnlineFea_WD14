<x-instructor-layout title="Trao đổi với học viên" pageTitle="Chi tiết trao đổi" breadcrumb="Giảng viên / Trao đổi / Chi tiết" :back-url="route('instructor.discussions.index')">
    @php
        $student = $discussion->user;
        $course = $discussion->course ?: $discussion->lesson?->course;
        $waitingForInstructor = (int) $discussion->last_message_user_id !== (int) auth()->id();
    @endphp

    <div class="flex h-[calc(100vh-8rem)] min-h-[620px] w-full min-w-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 py-3.5 dark:border-slate-800 dark:bg-slate-900 sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                @if($student?->avatar)
                    <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="h-11 w-11 shrink-0 rounded-full border border-slate-200 object-cover dark:border-slate-700">
                @else
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-800 text-sm font-bold text-white">
                        {{ strtoupper(mb_substr($student?->name ?? 'H', 0, 1)) }}
                    </span>
                @endif
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <h2 class="truncate text-sm font-extrabold text-slate-950 dark:text-white sm:text-base">{{ $student?->name ?? 'Học viên' }}</h2>
                        <span class="rounded-md border border-blue-200 bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-300">Học viên</span>
                    </div>
                    <p class="mt-0.5 truncate text-xs text-slate-600 dark:text-slate-400">
                        {{ $course?->title }}
                        @if($discussion->lesson)
                            <span aria-hidden="true"> · </span>{{ $discussion->lesson->title }}
                        @endif
                    </p>
                </div>
            </div>
            <span class="hidden shrink-0 rounded-full px-3 py-1 text-xs font-bold sm:inline-flex {{ $waitingForInstructor ? 'border border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300' : 'border border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300' }}">
                {{ $waitingForInstructor ? 'Đang chờ bạn trả lời' : 'Đã phản hồi' }}
            </span>
        </header>

        <x-chat.thread
            :context="$chatContext"
            :lesson-id="$discussion->lesson_id"
            accent="emerald"
            composer-placeholder="Nhập phản hồi cho học viên..."
        />
    </div>
</x-instructor-layout>
