@props([
    'videoSource' => null,
    'lesson',
    'progressUrl' => null,
    'lessonProgress' => null,
    'requiredVideoPercent' => 80,
    'isEnrolled' => false,
])

@php
    $watchedSeconds = (int) ($lessonProgress['watched_seconds'] ?? 0);
    $lessonCompleted = (bool) ($lessonProgress['is_completed'] ?? false);
    $durationSeconds = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);
@endphp

<div class="learning-video-stage flex min-h-[220px] w-full items-center justify-center bg-[#1c1d1f] sm:min-h-[320px] lg:min-h-[calc(100vh-14rem)]">
    @if($videoSource)
        <video
            id="learning-video"
            controls
            preload="metadata"
            playsinline
            class="aspect-video max-h-[calc(100vh-14rem)] w-full max-w-full bg-black"
            @if($isEnrolled && $progressUrl)
                data-lesson-progress-video
                data-progress-url="{{ $progressUrl }}"
                data-initial-watched="{{ $watchedSeconds }}"
                data-initial-completed="{{ $lessonCompleted ? '1' : '0' }}"
                data-duration-seconds="{{ $durationSeconds }}"
                data-required-percent="{{ $requiredVideoPercent }}"
            @endif
        >
            <source src="{{ $videoSource }}" @if($lesson->video_mime) type="{{ $lesson->video_mime }}" @endif>
            Trình duyệt không hỗ trợ phát video HTML5.
        </video>
    @else
        <div class="px-6 py-12 text-center text-sm text-white/70">
            Bài học này chưa có video để phát.
        </div>
    @endif
</div>
