@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')
@php
    $sectionTitle = $lesson->section?->title ?? $lesson->chapter?->title;
    $progressUrl = $isEnrolled ? route('courses.lessons.progress', [$course, $lesson]) : null;
    $currentCourseProgress = (float) ($enrollment?->progress_percent ?? 0);
    $currentWatchedSeconds = (int) ($lessonProgress?->watched_seconds ?? 0);
    $lessonCompleted = (bool) ($lessonProgress?->is_completed ?? false);
    $lessonDurationSeconds = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);
@endphp

<section class="bg-slate-50 py-8 dark:bg-[#0a0a0a]">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <a href="{{ route('courses.show', $course->slug) }}" class="text-sm font-bold text-indigo-600 hover:underline dark:text-indigo-300">
                    Quay lại khóa học
                </a>
                <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white sm:text-3xl">{{ $lesson->title }}</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    {{ $course->title }}@if($sectionTitle) · {{ $sectionTitle }} @endif
                </p>
            </div>

            @if($lesson->is_preview)
                <span class="inline-flex w-fit rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30">
                    Bài xem thử
                </span>
            @endif
        </div>

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#161615]">
            @if(! $canAccessLesson)
                <div class="p-6 sm:p-8">
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm font-semibold text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-200">
                        Bạn cần đăng ký khóa học để xem bài học này.
                    </div>

                    <div class="mt-5 flex flex-wrap gap-3">
                        @auth
                            @if(auth()->user()->isStudent())
                                <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-extrabold text-white transition hover:bg-indigo-700 cursor-pointer">
                                        Đăng ký học
                                    </button>
                                </form>
                            @else
                                <a href="{{ auth()->user()->dashboardUrl() }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-slate-950 px-5 text-sm font-extrabold text-white transition hover:bg-indigo-700">
                                    Vào Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-extrabold text-white transition hover:bg-indigo-700 cursor-pointer">
                                Đăng nhập để đăng ký
                            </a>
                        @endauth
                    </div>
                </div>
            @else
                @if($lesson->type === 'video')
                    @if($videoSource)
                        <div class="bg-slate-950 p-2">
                            <video controls preload="metadata" class="aspect-video w-full rounded-lg bg-slate-950" playsinline
                                @if($isEnrolled)
                                    data-lesson-progress-video
                                    data-progress-url="{{ $progressUrl }}"
                                    data-initial-watched="{{ $currentWatchedSeconds }}"
                                    data-initial-completed="{{ $lessonCompleted ? 1 : 0 }}"
                                    data-duration-seconds="{{ $lessonDurationSeconds }}"
                                @endif
                            >
                                <source src="{{ $videoSource }}" @if($lesson->video_mime) type="{{ $lesson->video_mime }}" @endif>
                                Trình duyệt của bạn không hỗ trợ phát video HTML5.
                            </video>
                        </div>
                        @if($isEnrolled)
                            <div class="border-t border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-[#161615]">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white" data-progress-status>
                                            {{ $lessonCompleted ? 'Bài học đã hoàn thành' : 'Đang ghi nhận tiến độ khi bạn xem video' }}
                                        </p>
                                        <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            Tiến độ khóa học: <span data-course-progress-percent>{{ number_format($currentCourseProgress, 0) }}</span>%
                                        </p>
                                    </div>
                                    <button type="button" data-mark-lesson-complete class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-extrabold text-white transition hover:bg-indigo-700">
                                        Đánh dấu đã học
                                    </button>
                                </div>
                                <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                    <div data-course-progress-bar class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all duration-300" style="width: {{ min(100, $currentCourseProgress) }}%"></div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="p-6 sm:p-8">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm font-semibold text-slate-600 dark:border-slate-800 dark:bg-slate-900/70 dark:text-slate-300">
                                Bài học này chưa có video để phát.
                            </div>
                        </div>
                    @endif
                @endif

                @if($lesson->type === 'quiz')
                    <div class="p-6 sm:p-8">
                        <div class="rounded-xl border border-violet-200 bg-violet-50 p-5 text-sm text-violet-900 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-100">
                            <div class="font-extrabold">Quiz</div>
                            <a href="{{ route('learn.lessons.quiz.show', [$course->slug, $lesson]) }}" class="mt-3 inline-flex h-11 items-center rounded-xl bg-violet-600 px-5 text-sm font-extrabold text-white transition hover:bg-violet-700">
                                Lam quiz
                            </a>
                        </div>
                    </div>
                @endif

                @if($lesson->content)
                    <div class="p-6 sm:p-8">
                        <h2 class="text-lg font-extrabold text-slate-950 dark:text-white">Nội dung bài học</h2>
                        <div class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $lesson->content }}</div>
                    </div>
                @endif
            @endif
        </article>
    </div>
</section>

@auth
<script>
document.addEventListener('DOMContentLoaded', () => {
    const video = document.querySelector('[data-lesson-progress-video]');
    if (!video) {
        return;
    }

    const progressUrl = video.dataset.progressUrl;
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const statusEl = document.querySelector('[data-progress-status]');
    const percentEl = document.querySelector('[data-course-progress-percent]');
    const barEl = document.querySelector('[data-course-progress-bar]');
    const completeButton = document.querySelector('[data-mark-lesson-complete]');
    const durationHint = Number(video.dataset.durationSeconds || 0);
    let lastSentAt = 0;
    let completed = video.dataset.initialCompleted === '1';
    let requestInFlight = false;
    let pendingCompleted = false;

    const updateUi = (data) => {
        if (!data) {
            return;
        }

        const percent = Number(data.progress_percent ?? 0);

        if (Number.isFinite(percent)) {
            if (percentEl) {
                percentEl.textContent = Math.round(percent).toString();
            }

            if (barEl) {
                barEl.style.width = `${Math.min(100, Math.max(0, percent))}%`;
            }
        }

        if (data.completed) {
            completed = true;

            if (statusEl) {
                statusEl.textContent = 'Bài học đã hoàn thành';
            }

            if (completeButton) {
                completeButton.textContent = 'Đã hoàn thành';
                completeButton.disabled = true;
                completeButton.classList.add('opacity-70', 'cursor-not-allowed');
            }
        } else if (statusEl) {
            statusEl.textContent = 'Đang ghi nhận tiến độ khi bạn xem video';
        }
    };

    const sendProgress = async (forceCompleted = false, forceSend = false) => {
        if (!progressUrl || !token) {
            return;
        }

        const watchedSeconds = Math.floor(Math.max(
            Number(video.currentTime || 0),
            Number(video.dataset.initialWatched || 0)
        ));
        const duration = Number.isFinite(video.duration) && video.duration > 0 ? video.duration : durationHint;
        const reachedThreshold = duration > 0 && watchedSeconds >= Math.ceil(duration * 0.9);
        const shouldComplete = forceCompleted || reachedThreshold;

        if (!forceSend && !shouldComplete && watchedSeconds - lastSentAt < 15) {
            return;
        }

        if (completed && shouldComplete) {
            return;
        }

        if (requestInFlight) {
            pendingCompleted = pendingCompleted || shouldComplete;
            return;
        }

        requestInFlight = true;
        lastSentAt = watchedSeconds;

        try {
            const response = await fetch(progressUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    watched_seconds: watchedSeconds,
                    completed: shouldComplete,
                }),
            });

            if (!response.ok) {
                throw new Error('progress_failed');
            }

            updateUi(await response.json());
        } catch (error) {
            if (statusEl) {
                statusEl.textContent = 'Chưa lưu được tiến độ, hệ thống sẽ thử lại khi bạn tiếp tục xem.';
            }
        } finally {
            requestInFlight = false;

            if (pendingCompleted && !completed) {
                pendingCompleted = false;
                sendProgress(true, true);
            }
        }
    };

    video.addEventListener('loadedmetadata', () => {
        const watchedSeconds = Number(video.dataset.initialWatched || 0);

        if (!completed && watchedSeconds > 0 && Number.isFinite(video.duration) && watchedSeconds < video.duration - 3) {
            video.currentTime = watchedSeconds;
        }
    }, { once: true });

    video.addEventListener('timeupdate', () => sendProgress(false, false));
    video.addEventListener('pause', () => sendProgress(false, true));
    video.addEventListener('ended', () => sendProgress(true, true));
    completeButton?.addEventListener('click', () => sendProgress(true, true));

    updateUi({ completed, progress_percent: Number(percentEl?.textContent || 0) });
});
</script>
@endauth
@endsection
