@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')
@php
    $sectionTitle = $lesson->section?->title ?? $lesson->chapter?->title;
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
                            <video controls preload="metadata" class="aspect-video w-full rounded-lg bg-slate-950" playsinline>
                                <source src="{{ $videoSource }}" @if($lesson->video_mime) type="{{ $lesson->video_mime }}" @endif>
                                Trình duyệt của bạn không hỗ trợ phát video HTML5.
                            </video>
                        </div>
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
@endsection
