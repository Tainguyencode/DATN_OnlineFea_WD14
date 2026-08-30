@props([
    'lesson',
    'lessonNumber' => null,
    'sectionTitle' => null,
])

@php
    $hasDraftUpdate = isset($lesson->draft_update) || !empty($lesson->is_draft_update) || !empty($lesson->is_draft_create);
    $moderation = $hasDraftUpdate
        ? (!empty($lesson->draft_update?->payload['ai_moderation']) ? (is_array($lesson->draft_update->payload['ai_moderation']) ? new \App\Models\VideoModeration($lesson->draft_update->payload['ai_moderation']) : $lesson->draft_update->payload['ai_moderation']) : null)
        : $lesson->videoModeration;
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

    @php
        $videoLessonKey = (isset($lesson->draft_update) && $lesson->draft_update->action === 'create')
            ? ('update_les_' . $lesson->draft_update->id)
            : (isset($lesson->draft_update) && $lesson->draft_update->action === 'update'
                ? ('update_les_' . $lesson->draft_update->id)
                : $lesson->id);

        $hasVideo = filled($lesson->original_video_key)
            || filled($lesson->hls_manifest_key)
            || filled($lesson->video_path)
            || filled($lesson->video_url)
            || (isset($lesson->draft_update) && (!empty($lesson->draft_update->payload['original_video_key']) || !empty($lesson->draft_update->payload['hls_manifest_key']) || !empty($lesson->draft_update->payload['video_path'])));

        $isProcessingHls = method_exists($lesson, 'isProcessing') ? $lesson->isProcessing() : false;
        if (!$isProcessingHls && isset($lesson->draft_update)) {
            $updatePayload = $lesson->draft_update->payload ?? [];
            $isProcessingHls = ($updatePayload['processing_status'] ?? '') === 'processing';
        }
    @endphp

    @if ($hasVideo)
        <div class="border-b border-slate-100 bg-slate-950 p-2 sm:p-3">
            @if ($lesson->video_url && !filled($lesson->original_video_key) && !filled($lesson->video_path))
                <div class="mx-auto flex max-w-3xl flex-col items-center justify-center gap-3 rounded-lg border border-dashed border-slate-600 bg-slate-900 px-4 py-10 text-center">
                    <p class="text-sm text-slate-300">Video được liên kết từ URL bên ngoài</p>
                    <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-indigo-700">
                        Mở video
                    </a>
                </div>
            @elseif ($isProcessingHls)
                <div class="mx-auto flex max-w-3xl flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-indigo-500/30 bg-slate-900 px-4 py-8 text-center">
                    <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500/20 text-indigo-400 mb-1">
                        <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-white">Video đang được chuyển đổi HLS...</p>
                    <p class="text-xs text-slate-400">Vui lòng quay lại sau ít phút hoặc tải lại trang khi hệ thống hoàn tất xử lý.</p>
                </div>
            @else
                <div class="relative mx-auto aspect-video w-full max-w-3xl overflow-hidden rounded-lg bg-black" id="mod-container-{{ $videoLessonKey }}">
                    <video
                        id="mod-video-{{ $videoLessonKey }}"
                        data-hls-src="{{ route('admin.ai-moderation.hls.playlist', ['lesson' => $videoLessonKey]) }}"
                        controls
                        preload="metadata"
                        playsinline
                        class="h-full w-full bg-black"
                    >
                        Trình duyệt không hỗ trợ phát video.
                    </video>
                </div>
                <script>
                    (function() {
                        function initModPlayer() {
                            var v = document.getElementById('mod-video-{{ $videoLessonKey }}');
                            var container = document.getElementById('mod-container-{{ $videoLessonKey }}');
                            if (!v || v.dataset.hlsInit) return;
                            
                            var hlsUrl = v.getAttribute('data-hls-src');
                            var retryCount = 0;

                            function showFallbackNotice(msg) {
                                if (container) {
                                    container.innerHTML = '<div class="flex flex-col items-center justify-center h-full p-6 text-center text-slate-300 text-xs gap-2"><svg class="w-8 h-8 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg><p class="font-semibold">' + msg + '</p></div>';
                                }
                            }

                            function setupHlsInstance() {
                                if (typeof Hls !== 'undefined' && Hls.isSupported() && hlsUrl) {
                                    v.dataset.hlsInit = 'true';
                                    var hls = new Hls({ enableWorker: true, lowLatencyMode: false });
                                    hls.loadSource(hlsUrl);
                                    hls.attachMedia(v);

                                    hls.on(Hls.Events.ERROR, function (_, data) {
                                        if (data.fatal) {
                                            if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                                                retryCount++;
                                                if (retryCount <= 2) {
                                                    setTimeout(function() { hls.startLoad(); }, 1500);
                                                } else {
                                                    hls.destroy();
                                                    showFallbackNotice('Video đang xử lý HLS hoặc không khả dụng.');
                                                }
                                            } else {
                                                hls.destroy();
                                                showFallbackNotice('Không thể phát video kiểm duyệt lúc này.');
                                            }
                                        }
                                    });
                                }
                            }

                            if (typeof Hls !== 'undefined') {
                                setupHlsInstance();
                            } else {
                                var s = document.createElement('script');
                                s.src = 'https://cdn.jsdelivr.net/npm/hls.js@latest';
                                s.onload = function() { setupHlsInstance(); };
                                document.head.appendChild(s);
                            }
                        }
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initModPlayer);
                        } else {
                            initModPlayer();
                        }
                    })();
                </script>
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
            $cardReviewStatus = $lPayload['review_status'] ?? ($lessonUpdate?->status === 'rejected' ? 'fail' : ($lessonUpdate?->status === 'approved' ? 'pass' : null));
            $reviewedTimestamp = $lessonUpdate?->reviewed_at ?? $lessonUpdate?->updated_at ?? null;
        @endphp

        @if (filled($cardAdminNote) || $cardRequireReupload || filled($cardReviewStatus))
            @php
                $modCardStyle = match($cardReviewStatus) {
                    'pass' => [
                        'wrapper' => 'border-emerald-200 bg-emerald-50/90',
                        'title' => 'text-emerald-900',
                        'badge' => 'bg-emerald-200/80 text-emerald-900',
                        'badge_text' => 'Đạt',
                        'icon' => '✅',
                    ],
                    'need_revision' => [
                        'wrapper' => 'border-amber-200 bg-amber-50/90',
                        'title' => 'text-amber-900',
                        'badge' => 'bg-amber-200/80 text-amber-900',
                        'badge_text' => 'Cần chỉnh sửa',
                        'icon' => '⚠️',
                    ],
                    default => [
                        'wrapper' => 'border-rose-200 bg-rose-50/90',
                        'title' => 'text-rose-900',
                        'badge' => 'bg-rose-200/80 text-rose-900',
                        'badge_text' => 'Từ chối',
                        'icon' => '❌',
                    ],
                };
            @endphp
            <div class="rounded-lg border {{ $modCardStyle['wrapper'] }} p-4 shadow-2xs">
                <div class="flex items-start gap-2.5">
                    <span class="text-lg">{{ $modCardStyle['icon'] }}</span>
                    <div class="w-full">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wide {{ $modCardStyle['title'] }}">Ghi chú & Phản hồi từ Admin:</p>
                            <div class="flex items-center gap-2">
                                @if($reviewedTimestamp)
                                    <span class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($reviewedTimestamp)->format('d/m/Y H:i') }}</span>
                                @endif
                                <span class="rounded-full {{ $modCardStyle['badge'] }} px-2.5 py-0.5 text-xs font-bold">{{ $modCardStyle['badge_text'] }}</span>
                            </div>
                        </div>
                        @if (filled($cardAdminNote))
                            <div class="mt-2 text-xs text-slate-800 leading-relaxed font-medium whitespace-pre-line bg-white/90 p-3 rounded-lg border border-slate-200/80">
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

            {{-- Bổ sung: AI kiểm tra phù hợp danh mục --}}
            @php
                $catMatch = method_exists($moderation, 'categoryMatch') ? $moderation->categoryMatch() : ($moderation->details['category_match'] ?? null);
            @endphp

            @if($catMatch)
                @php
                    $catStatus = $catMatch['status'] ?? 'Cần Admin kiểm tra';
                    $catBadgeClass = match($catStatus) {
                        'Phù hợp' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'Không phù hợp' => 'bg-rose-100 text-rose-800 border-rose-300',
                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                    };
                    $catEmoji = match($catStatus) {
                        'Phù hợp' => '🟢',
                        'Không phù hợp' => '🔴',
                        default => '🟡',
                    };
                @endphp
                <div class="rounded-lg border border-indigo-200 bg-indigo-50/70 p-3.5 shadow-2xs">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="text-base">🎓</span>
                            <h6 class="font-bold text-indigo-950 text-xs sm:text-sm">AI kiểm tra phù hợp danh mục</h6>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $catBadgeClass }}">
                            <span>{{ $catEmoji }}</span>
                            {{ $catStatus }}
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs">
                        @if(isset($catMatch['confidence']))
                            <div class="flex items-center gap-2">
                                <span class="text-slate-500 font-medium">Độ tin cậy:</span>
                                <span class="font-bold text-indigo-700">{{ round(((float)$catMatch['confidence']) <= 1.0 ? ((float)$catMatch['confidence'] * 100) : (float)$catMatch['confidence']) }}%</span>
                            </div>
                        @endif

                        @if(!empty($catMatch['detected_topics']) && is_array($catMatch['detected_topics']))
                            <div class="flex flex-wrap items-center gap-1 pt-0.5">
                                <span class="text-slate-500 font-medium mr-1">Chủ đề phát hiện:</span>
                                @foreach($catMatch['detected_topics'] as $topic)
                                    <span class="inline-block rounded bg-white border border-indigo-200 px-2 py-0.5 text-indigo-800 font-semibold text-[11px]">{{ $topic }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($catMatch['reason']))
                            <div class="mt-2 text-slate-700 bg-white p-2.5 rounded-lg border border-indigo-100/80 leading-relaxed text-xs">
                                <span class="font-semibold text-slate-800">AI nhận xét về chuyên ngành:</span> {{ $catMatch['reason'] }}
                            </div>
                        @endif
                    </div>
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
