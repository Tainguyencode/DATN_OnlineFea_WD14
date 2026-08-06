@props([
    'lesson',
    'lessonNumber' => null,
    'sectionTitle' => null,
])

@php
    $moderation = $lesson->videoModeration;
    if (!$moderation && isset($lesson->draft_update) && !empty($lesson->draft_update->payload['ai_moderation'])) {
        $moderation = new \App\Models\VideoModeration($lesson->draft_update->payload['ai_moderation']);
    }
    $badgeTones = [
        'red'    => 'border-rose-200 bg-rose-50 text-rose-800',
        'orange' => 'border-orange-200 bg-orange-50 text-orange-800',
        'yellow' => 'border-amber-200 bg-amber-50 text-amber-800',
        'green'  => 'border-emerald-200 bg-emerald-50 text-emerald-800',
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

    // Xác định trạng thái dấu hiệu
    $hasAnySign = $moderation && $moderation->hasDetectedSigns();
    $hasHardViolation = $moderation && ($moderation->violence || $moderation->adult || $moderation->weapon);
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
                @php
                    $hlsPath = 'lesson-hls/' . $lesson->id . '/playlist.m3u8';
                    $hasHls = \Illuminate\Support\Facades\Storage::disk('local')->exists($hlsPath);
                    $hasLocalFile = \Illuminate\Support\Facades\Storage::disk('local')->exists($lesson->video_path);
                    $hasPublicFile = \Illuminate\Support\Facades\Storage::disk('public')->exists($lesson->video_path);
                    $fileExists = $hasHls || $hasLocalFile || $hasPublicFile;
                @endphp
                @if ($fileExists)
                    <video
                        src="{{ route('admin.ai-moderation.stream-video', $lesson) }}"
                        controls
                        preload="metadata"
                        playsinline
                        class="mx-auto aspect-video w-full max-w-3xl rounded-lg bg-black"
                    >
                        Trình duyệt không hỗ trợ phát video.
                    </video>
                @else
                    <div class="mx-auto flex max-w-3xl flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-rose-800 bg-rose-950/40 p-8 text-center text-rose-200">
                        <svg class="h-10 w-10 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="font-bold text-base">Video không tồn tại trên hệ thống</p>
                        <p class="text-xs text-rose-300">File video của bài học này không có trên máy chủ.</p>
                    </div>
                @endif
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
        @php
            $lessonUpdate = $lesson->draft_update ?? null;
            if (!$lessonUpdate && isset($lesson->id)) {
                $lessonUpdate = \App\Models\ContentUpdate::where('entity_id', $lesson->id)
                    ->latest()
                    ->first();
            }
            $lPayload = $lessonUpdate?->payload ?? [];
            $cardAdminNote = $lPayload['admin_note'] ?? ($lessonUpdate?->rejection_reason ?? null);
            $cardRequireReupload = !empty($lPayload['require_reupload']);
        @endphp

        @if (filled($cardAdminNote) || $cardRequireReupload)
            <div class="rounded-lg border border-amber-200 bg-amber-50/90 p-4 shadow-2xs">
                <div class="flex items-start gap-2.5">
                    <span class="text-lg">⚠️</span>
                    <div class="w-full">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-900">Ghi chú & Phản hồi từ Admin cho bài học này:</p>
                        @if (filled($cardAdminNote))
                            <div class="mt-2 text-xs text-amber-950 leading-relaxed font-medium whitespace-pre-line bg-white/90 p-3 rounded-lg border border-amber-200/80">
                                {!! nl2br(e($cardAdminNote)) !!}
                            </div>
                        @endif
                        @if ($cardRequireReupload)
                            <p class="mt-2 text-xs font-bold text-rose-700 flex items-center gap-1">
                                <span>📹</span> Yêu cầu từ Admin: Vui lòng upload lại video gốc mới.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if (! $moderation)
            <p class="text-sm text-slate-500">Chưa có dữ liệu kiểm duyệt AI.</p>
        @else
            {{-- Banner trạng thái tổng --}}
            @if ($hasHardViolation)
                <div class="flex items-start gap-2 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm font-semibold text-rose-900">
                    <span aria-hidden="true">🔴</span>
                    <span>AI phát hiện nội dung có thể vi phạm chính sách (bạo lực / 18+ / vũ khí). Admin cần xem lại.</span>
                </div>
            @elseif ($hasAnySign)
                <div class="flex items-start gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm font-semibold text-amber-900">
                    <span aria-hidden="true">🔍</span>
                    <span>AI phát hiện một số dấu hiệu cần xem lại. Không phải kết luận vi phạm.</span>
                </div>
            @else
                <div class="flex items-start gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2.5 text-sm font-semibold text-emerald-800">
                    <span aria-hidden="true">✅</span>
                    <span>Không phát hiện dấu hiệu đáng chú ý.</span>
                </div>
            @endif

            {{-- Badges chi tiết dấu hiệu --}}
            @if (count($moderation->summaryBadgeItems()) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach ($moderation->summaryBadgeItems() as $badge)
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold {{ $badgeTones[$badge['tone']] ?? $badgeTones['yellow'] }}">
                            <span aria-hidden="true">{{ $badge['emoji'] }}</span>
                            {{ $badge['label'] }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Tóm tắt AI --}}
            @if ($moderation->summary)
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Nhận xét của AI</p>
                    <p class="mt-1 text-sm leading-6 text-slate-700">{{ $moderation->summary }}</p>
                </div>
            @endif

            {{-- Chi tiết từng frame --}}
            @php $frames = $moderation->violatedFrameDetails(); @endphp

            @if ($frames !== [])
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Dấu hiệu phát hiện theo thời điểm</p>
                    <ul class="mt-3 divide-y divide-slate-100 rounded-lg border border-slate-200">
                        @foreach ($frames as $frame)
                            <li class="px-4 py-3">
                                <p class="font-mono text-sm font-bold text-slate-900">{{ $frame['timestamp'] }}</p>
                                <ul class="mt-2 space-y-1">
                                    @foreach ($frame['labels'] as $label)
                                        <li class="text-sm font-semibold text-amber-800">
                                            <span aria-hidden="true">🔍</span> {{ $label }}
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($frame['reason'] !== '')
                                    <div class="mt-2 text-sm text-slate-600">
                                        <p class="font-semibold text-slate-700">Ghi chú của AI:</p>
                                        <p class="mt-0.5 leading-6">{{ $frame['reason'] }}</p>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Khuyến nghị cho giảng viên --}}
            @if ($hasAnySign)
                <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                    <p class="font-bold">💡 Khuyến nghị dành cho giảng viên</p>
                    <p class="mt-1 leading-6">
                        Nếu đây chỉ là video demo hoặc minh họa thì <strong>không cần chỉnh sửa</strong>.
                        Nếu là nội dung của bên thứ ba, nên thay bằng nội dung do bạn tự tạo để tránh bị từ chối khi admin xem xét.
                    </p>
                </div>
            @endif
        @endif
    </div>
</article>
