@php
    $typeStyles = $typeStyles ?? [
        'video' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'document' => 'bg-sky-50 text-sky-700 border-sky-200',
        'quiz' => 'bg-violet-50 text-violet-700 border-violet-200',
        'assignment' => 'bg-amber-50 text-amber-700 border-amber-200',
    ];

    $statusStyles = $statusStyles ?? [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];

    $lessonTypes = $lessonTypes ?? [
        'video' => 'Video',
        'document' => 'Tài liệu',
        'quiz' => 'Quiz',
        'assignment' => 'Bài tập',
    ];

    $lessonStatuses = $lessonStatuses ?? [
        'draft' => 'Nháp',
        'published' => 'Đã xuất bản',
    ];

    $formatDuration = $formatDuration ?? function ($seconds) {
        $seconds = (int) $seconds;
        if ($seconds <= 0) {
            return 'Chưa đặt';
        }

        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return $minutes > 0 ? $minutes.' phút'.($remaining ? ' '.$remaining.' giây' : '') : $remaining.' giây';
    };

    $typeClass = $typeStyles[$lesson->type] ?? $typeStyles['video'];
    $statusClass = $statusStyles[$lesson->status] ?? $statusStyles['draft'];
@endphp

<div class="p-5" id="lesson-item-{{ $lesson->id }}">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $typeClass }}">{{ $lessonTypes[$lesson->type] ?? $lesson->type }}</span>
                @php
                    $lessonUpdate = $lesson->draft_update ?? null;
                    if (!$lessonUpdate && isset($lesson->id)) {
                        $lessonUpdate = \App\Models\ContentUpdate::where('course_id', $course->id)
                            ->where('entity_id', $lesson->id)
                            ->latest()
                            ->first();
                    }
                    $lPayload = $lessonUpdate?->payload ?? [];
                    $effectiveReviewStatus = $lPayload['review_status'] ?? null;
                    if (!$effectiveReviewStatus && isset($lesson->update_status)) {
                        if ($lesson->update_status === 'rejected') $effectiveReviewStatus = 'fail';
                        elseif ($lesson->update_status === 'approved') $effectiveReviewStatus = 'pass';
                    }
                @endphp
                @if($effectiveReviewStatus === 'pass')
                    <span class="rounded-full border border-emerald-300 bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Đạt</span>
                @elseif($effectiveReviewStatus === 'need_revision')
                    <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Cần chỉnh sửa</span>
                @elseif($effectiveReviewStatus === 'fail')
                    <span class="rounded-full border border-rose-300 bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">Từ chối</span>
                @elseif(isset($lesson->update_status))
                    @if($lesson->update_status === 'draft')
                        <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Draft</span>
                    @elseif($lesson->update_status === 'pending')
                        <span class="rounded-full border border-blue-300 bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">Đã gửi duyệt</span>
                    @endif
                @else
                    <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $lessonStatuses[$lesson->status] ?? $lesson->status }}</span>
                @endif
                @if($lesson->is_preview)
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Xem thử</span>
                @endif
                @if($lesson->type === 'video')
                    @php
                        $hasVideoContent = filled($lesson->original_video_key) || filled($lesson->hls_manifest_key) || filled($lesson->video_path) || filled($lesson->video_url);
                    @endphp
                    <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $hasVideoContent ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                        {{ $hasVideoContent ? 'Đã có video' : 'Chưa có video' }}
                    </span>
                @endif
            </div>
            @php
                $hlsKey = !empty($lesson->is_draft_create) && isset($lesson->draft_update) 
                    ? 'update_' . $lesson->draft_update->id 
                    : 'lesson_' . $lesson->id;
            @endphp
            <h4 class="mt-2 font-bold text-slate-950">{{ $lesson->title }}</h4>
            <div class="mt-1 flex flex-wrap gap-3 text-xs text-slate-500">
                <span data-lesson-duration-key="{{ $hlsKey }}">Thời lượng: {{ $formatDuration($lesson->duration ?? $lesson->duration_seconds) }}</span>
                <span>Bài {{ $lesson->sort_order }}</span>
                @if($lesson->type === 'video' && ($lesson->original_video_key || $lesson->hls_manifest_key || $lesson->video_path))
                    @php
                        $isHlsReady = $lesson->isHlsReady();
                        $isHlsFailed = $lesson->hasFailedProcessing();
                        $isHlsProcessing = $lesson->isProcessing();
                    @endphp
                    <span data-hls-status-key="{{ $hlsKey }}"
                          @if($isHlsProcessing) data-hls-processing="true" @endif
                          class="font-semibold @if($isHlsReady) text-emerald-600 @elseif($isHlsFailed) text-rose-600 @else text-amber-600 @endif">
                        @if($isHlsReady)
                            Video đã được xử lý bảo mật thành công.
                        @elseif($isHlsFailed)
                            Video xử lý bảo mật thất bại.
                        @else
                            Video đang trong quá trình xử lý bảo mật. Vui lòng chờ trong giây lát.
                        @endif
                    </span>
                @elseif($lesson->type === 'video' && $lesson->video_url)
                    <a href="{{ $lesson->video_url }}" target="_blank" class="font-semibold text-indigo-600 hover:underline">Video URL</a>
                @endif
                @if(in_array($lesson->type, ['document', 'assignment'], true) && $lesson->document_file)
                    <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="font-semibold text-sky-600 hover:underline">Tài liệu</a>
                @endif
            </div>
            @if($lesson->content && $lesson->type !== 'quiz')
                <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $lesson->content }}</p>
            @endif

            {{-- Ghi chú kiểm duyệt của Admin dành riêng cho bài học này --}}
            @php
                $adminNote = $lPayload['admin_note'] ?? null;
                $requireReupload = !empty($lPayload['require_reupload']);
                $reviewStatus = $effectiveReviewStatus;
                $hasAdminFeedback = filled($adminNote) || $requireReupload || filled($reviewStatus);
            @endphp

            @if($hasAdminFeedback)
                @php
                    $cardBoxStyle = match($reviewStatus) {
                        'pass' => [
                            'wrapper' => 'border-emerald-200 bg-emerald-50/90',
                            'title' => 'text-emerald-950',
                            'badge' => 'bg-emerald-200/80 text-emerald-900',
                            'badge_text' => 'Đạt',
                            'icon' => '✅',
                            'note_box' => 'border-emerald-200 bg-white text-slate-800',
                            'note_title' => 'text-emerald-900',
                        ],
                        'need_revision' => [
                            'wrapper' => 'border-amber-200 bg-amber-50/90',
                            'title' => 'text-amber-950',
                            'badge' => 'bg-amber-200/80 text-amber-900',
                            'badge_text' => 'Cần chỉnh sửa',
                            'icon' => '⚠️',
                            'note_box' => 'border-amber-200 bg-white text-slate-800',
                            'note_title' => 'text-amber-900',
                        ],
                        default => [
                            'wrapper' => 'border-rose-200 bg-rose-50/90',
                            'title' => 'text-rose-950',
                            'badge' => 'bg-rose-200/80 text-rose-900',
                            'badge_text' => 'Từ chối',
                            'icon' => '❌',
                            'note_box' => 'border-rose-200 bg-white text-slate-800',
                            'note_title' => 'text-rose-900',
                        ],
                    };
                @endphp
                <div class="mt-4 max-w-2xl rounded-xl border {{ $cardBoxStyle['wrapper'] }} p-4 shadow-2xs">
                    <div class="flex items-start gap-3">
                        <span class="text-xl">{{ $cardBoxStyle['icon'] }}</span>
                        <div class="w-full">
                            <div class="flex items-center justify-between">
                                <h5 class="text-sm font-bold {{ $cardBoxStyle['title'] }}">Phản hồi từ Admin</h5>
                                <span class="rounded-full {{ $cardBoxStyle['badge'] }} px-2.5 py-0.5 text-xs font-bold">{{ $cardBoxStyle['badge_text'] }}</span>
                            </div>
                            
                            @if(filled($adminNote))
                                <div class="mt-2.5 rounded-lg border {{ $cardBoxStyle['note_box'] }} p-3 text-xs leading-relaxed font-medium shadow-2xs">
                                    <p class="font-bold {{ $cardBoxStyle['note_title'] }} mb-1">Ghi chú từ Admin:</p>
                                    <div class="whitespace-pre-line text-slate-700">{!! nl2br(e($adminNote)) !!}</div>
                                </div>
                            @endif

                            @if($requireReupload)
                                <p class="mt-2.5 text-xs font-bold text-rose-700 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span>Yêu cầu: Vui lòng upload lại video gốc</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @php
            $isDraftCreate = !empty($lesson->is_draft_create) && isset($lesson->draft_update);
            $updateActionUrl = $isDraftCreate
                ? route('instructor.courses.content-updates.update', [$course, $lesson->draft_update])
                : route('instructor.courses.lessons.update', [$course, $lesson->id]);
            $destroyActionUrl = $isDraftCreate
                ? route('instructor.courses.content-updates.destroy', [$course, $lesson->draft_update])
                : route('instructor.courses.lessons.destroy', [$course, $lesson->id]);
            $errorBagKey = 'updateLesson_' . ($isDraftCreate ? 'update_' . $lesson->draft_update->id : $lesson->id);
        @endphp

        <div class="flex shrink-0 flex-wrap gap-2">
            @if($lesson->type === 'quiz' && ! $isDraftCreate)
                <a href="{{ route('instructor.courses.lessons.quiz.show', [$course, $lesson]) }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-violet-200 px-4 py-2 text-sm font-bold text-violet-700 transition-colors duration-200 hover:bg-violet-50 cursor-pointer">
                    Quản lý câu hỏi
                </a>
            @endif

            {{-- Nút Sửa bài học --}}
            <button type="button"
                onclick="document.getElementById('edit-lesson-modal-{{ $lesson->id }}').classList.remove('hidden')"
                class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 cursor-pointer">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Sửa bài học
            </button>

            {{-- Nút Xóa --}}
            <form method="POST" action="{{ $destroyActionUrl }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài học này?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg border border-rose-200 bg-white px-4 py-2 text-sm font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-50 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Xóa
                </button>
            </form>
        </div>

        {{-- Modal Sửa bài học --}}
        <div id="edit-lesson-modal-{{ $lesson->id }}"
             class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/60 p-4 {{ isset($errors) && $errors->hasBag($errorBagKey) ? '' : 'hidden' }}"
             onclick="if(event.target===this) this.classList.add('hidden')">
            <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl"
                 onclick="event.stopPropagation()">
                {{-- Header modal --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-950">Sửa bài học</h3>
                        <p class="mt-0.5 text-xs text-slate-500 truncate max-w-xs">{{ $lesson->title }}</p>
                    </div>
                    <button type="button"
                        onclick="document.getElementById('edit-lesson-modal-{{ $lesson->id }}').classList.add('hidden')"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 hover:text-slate-700 transition-colors cursor-pointer">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body modal --}}
                <div class="p-6">
                    @include('instructor.courses.partials.lesson-form', [
                        'course' => $course,
                        'action' => $updateActionUrl,
                        'method' => 'PUT',
                        'lesson' => $lesson,
                        'errorBag' => $errorBagKey,
                        'lessonTypes' => $lessonTypes,
                        'lessonStatuses' => $lessonStatuses,
                        'submitLabel' => 'Lưu thay đổi',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
