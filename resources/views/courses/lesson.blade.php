@extends('layouts.learning')

@section('title', $lesson->title . ' - ' . $course->title)

@section('content')
<div
    class="learning-player"
    data-learning-player
    data-course-progress="{{ $courseProgress }}"
    data-progress-url="{{ $progressUrl }}"
>
    <x-learning.header :course="$course" :course-progress="$courseProgress" />

    <div class="learning-player-body flex min-h-[calc(100vh-3.5rem)] flex-col lg:flex-row">
        <main class="learning-main min-w-0 flex-1" data-learning-main>
            @if(! $canAccessLesson)
                <div class="flex min-h-[320px] items-center justify-center bg-[#1c1d1f] p-6">
                    <div class="max-w-md rounded border border-amber-400/30 bg-amber-500/10 p-6 text-center text-white">
                        <h2 class="text-lg font-bold">Bài học bị khóa</h2>
                        <p class="mt-2 text-sm text-white/80">Bạn cần đăng ký khóa học để xem bài học này.</p>
                        <div class="mt-5 flex flex-wrap justify-center gap-3">
                            @auth
                                @if(auth()->user()->isStudent())
                                    <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex h-10 items-center rounded bg-[#0056D2] px-5 text-sm font-bold text-white hover:bg-[#0046B8]">Đăng ký học</button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="inline-flex h-10 items-center rounded bg-[#0056D2] px-5 text-sm font-bold text-white hover:bg-[#0046B8]">Đăng nhập</a>
                            @endauth
                            <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex h-10 items-center rounded border border-white/20 px-5 text-sm font-semibold text-white hover:bg-white/10">Về khóa học</a>
                        </div>
                    </div>
                </div>
            @elseif($lesson->type === 'quiz')
                <x-learning.quiz-player :quiz-context="$quizContext" :lesson="$lesson" />
            @elseif($lesson->type === 'video')
                <x-learning.video-player
                    :video-source="$videoSource"
                    :lesson="$lesson"
                    :progress-url="$progressUrl"
                    :lesson-progress="$lessonProgress"
                    :required-video-percent="$requiredVideoPercent"
                    :is-enrolled="$isEnrolled"
                />
            @elseif($lesson->type === 'document')
                <div class="flex min-h-[320px] items-center justify-center bg-[#1c1d1f] p-6 lg:min-h-[calc(100vh-14rem)]">
                    <div class="max-w-lg text-center text-white">
                        <h2 class="text-xl font-bold">{{ $lesson->title }}</h2>
                        <p class="mt-2 text-sm text-white/80">Bài đọc / tài liệu</p>
                        @if($lesson->document_file)
                            <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="mt-5 inline-flex h-11 items-center rounded bg-[#0056D2] px-6 text-sm font-bold text-white hover:bg-[#0046B8]">Mở tài liệu</a>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex min-h-[320px] items-center justify-center bg-[#1c1d1f] p-6 text-white/80">
                    Loại bài học này chưa được hỗ trợ trong player.
                </div>
            @endif

            @if($canAccessLesson)
                <x-learning.lesson-tabs
                    :lesson="$lesson"
                    :course="$course"
                    :section-title="$sectionTitle"
                    :navigation="$navigation"
                    :lesson-state="$lessonState"
                    :is-enrolled="$isEnrolled"
                    :can-access-lesson="$canAccessLesson"
                />
            @endif
        </main>

        <x-learning.sidebar
            :sections="$curriculumSections"
            :course-progress="$courseProgress"
            :completed-lessons="$completedLessons"
            :total-lessons="$totalLessons"
        />
    </div>
</div>
@endsection
