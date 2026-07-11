@props([
    'lesson',
    'lessonNumber' => null,
    'sectionTitle' => null,
])

@php
    $moderation = $lesson->videoModeration;
    $badgeTones = [
        'red' => 'border-rose-200 bg-rose-50 text-rose-800',
        'orange' => 'border-orange-200 bg-orange-50 text-orange-800',
        'yellow' => 'border-amber-200 bg-amber-50 text-amber-800',
        'green' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
    ];

    $videoFileName = $lesson->video_original_name
        ?: ($lesson->video_path ? basename($lesson->video_path) : null);

    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        if ($seconds <= 0) {
            return null;
        }

        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return $minutes > 0
            ? $minutes.' phút'.($remaining ? ' '.$remaining.' giây' : '')
            : $remaining.' giây';
    };

    $durationLabel = $formatDuration($lesson->duration ?? $lesson->duration_seconds);
@endphp

<article {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm']) }}>
    <div class="border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-5">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Bài học</p>
        <div class="mt-1 flex flex-wrap items-center gap-2">
            @if ($lessonNumber)
                <span class="inline-flex shrink-0 rounded-md bg-indigo-600 px-2.5 py-1 text-xs font-bold text-white">
                    Bài {{ $lessonNumber }}
                </span>
            @endif
            <h4 class="text-base font-bold text-slate-950">{{ $lesson->title }}</h4>
        </div>
        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500">
            @if ($sectionTitle)
                <span>Chương: <span class="font-semibold text-slate-700">{{ $sectionTitle }}</span></span>
            @endif
            @if ($durationLabel)
                <span>Thời lượng: <span class="font-semibold text-slate-700">{{ $durationLabel }}</span></span>
            @endif
            @if ($videoFileName)
                <span>File: <span class="font-semibold text-slate-700">{{ $videoFileName }}</span></span>
            @endif
        </div>
    </div>

    @if ($lesson->video_path || $lesson->video_url)
        <div class="border-b border-slate-100 bg-slate-950 p-2 sm:p-3">
            @if ($lesson->video_path)
                <video
                    src="{{ asset('storage/'.$lesson->video_path) }}"
                    controls
                    preload="metadata"
                    playsinline
                    class="mx-auto aspect-video w-full max-w-3xl rounded-lg bg-black"
                >
                    Trình duyệt không hỗ trợ phát video.
                </video>
            @elseif ($lesson->video_url)
                <div class="mx-auto flex max-w-3xl flex-col items-center justify-center gap-3 rounded-lg border border-dashed border-slate-600 bg-slate-900 px-4 py-10 text-center">
                    <p class="text-sm text-slate-300">Video được liên kết từ URL bên ngoài</p>
                    <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-indigo-700">
                        Mở video
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="border-b border-slate-100 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800 sm:px-5">
            Bài học này chưa có video đính kèm.
        </div>
    @endif

    <div class="space-y-4 p-4 sm:p-5">
        @if (! $moderation)
            <p class="text-sm text-slate-500">Chưa có dữ liệu kiểm duyệt AI.</p>
        @else
            @if ($moderation->hasViolations())
                <div class="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm font-semibold text-amber-900">
                    <span aria-hidden="true">⚠</span>
                    <span>Phát hiện vi phạm</span>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach ($moderation->summaryBadgeItems() as $badge)
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold {{ $badgeTones[$badge['tone']] ?? $badgeTones['orange'] }}">
                            <span aria-hidden="true">{{ $badge['emoji'] }}</span>
                            {{ $badge['label'] }}
                        </span>
                    @endforeach
                </div>
            @else
                <div class="flex items-start gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-800">
                    <span aria-hidden="true">✅</span>
                    <span>Không phát hiện vi phạm.</span>
                </div>
            @endif

            @if ($moderation->summary)
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Tóm tắt</p>
                    <p class="mt-1 text-sm leading-6 text-slate-700">{{ $moderation->summary }}</p>
                </div>
            @endif

            @php $frames = $moderation->violatedFrameDetails(); @endphp

            @if ($frames !== [])
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Chi tiết khung hình</p>
                    <ul class="mt-3 divide-y divide-slate-100 rounded-lg border border-slate-200">
                        @foreach ($frames as $frame)
                            <li class="px-4 py-3">
                                <p class="font-mono text-sm font-bold text-slate-900">{{ $frame['timestamp'] }}</p>
                                <ul class="mt-2 space-y-1">
                                    @foreach ($frame['labels'] as $label)
                                        <li class="text-sm font-semibold text-amber-800">
                                            <span aria-hidden="true">⚠</span> {{ $label }}
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($frame['reason'] !== '')
                                    <div class="mt-2 text-sm text-slate-600">
                                        <p class="font-semibold text-slate-700">Lý do:</p>
                                        <p class="mt-0.5 leading-6">{{ $frame['reason'] }}</p>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
    </div>
</article>
