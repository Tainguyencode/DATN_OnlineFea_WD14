@props([
    'lesson',
    'course',
    'sectionTitle' => null,
    'navigation' => ['previous' => null, 'next' => null],
    'lessonState' => 'available',
    'isEnrolled' => false,
    'canAccessLesson' => false,
    'canUseLessonAi' => false,
    'aiSummaryUrl' => null,
    'aiExplainUrl' => null,
    'canUseLessonNotes' => false,
    'lessonNotes' => collect(),
    'lessonNotesIndexUrl' => null,
    'lessonNotesStoreUrl' => null,
])

@php
    $notesPayload = collect($lessonNotes)->map(fn ($note) => [
        'id' => $note->id,
        'content' => $note->content,
        'timestamp_seconds' => $note->timestamp_seconds,
        'timestamp_label' => $note->timestampLabel(),
        'created_at' => $note->created_at?->format('d/m/Y H:i'),
        'updated_at' => $note->updated_at?->format('d/m/Y H:i'),
        'lesson_type' => $lesson->type,
        'update_url' => route('lesson-notes.update', $note),
        'delete_url' => route('lesson-notes.destroy', $note),
    ])->values();
    $videoDurationSeconds = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);
@endphp

<div class="learning-tabs border-t border-[#d1d7dc] bg-white">
    <div class="border-b border-[#d1d7dc] px-4 sm:px-6" x-data="{ tab: 'overview' }">
        <div class="flex gap-1 overflow-x-auto" role="tablist">
            @foreach(['overview' => 'Nội dung', 'notes' => 'Ghi chú', 'qa' => 'Thảo luận', 'ai' => 'AI hỗ trợ', 'resources' => 'Tài liệu'] as $key => $label)
                <button
                    type="button"
                    role="tab"
                    class="shrink-0 border-b-2 px-4 py-3 text-sm font-semibold transition"
                    :class="tab === '{{ $key }}' ? 'border-[#1c1d1f] text-[#1c1d1f]' : 'border-transparent text-[#6a6f73] hover:text-[#1c1d1f]'"
                    x-on:click="tab = '{{ $key }}'"
                    :aria-selected="tab === '{{ $key }}'"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="max-w-4xl px-4 py-6 sm:px-6">
            <div x-show="tab === 'overview'" x-cloak>
                <h1 class="text-xl font-bold text-[#1c1d1f] sm:text-2xl">{{ $lesson->title }}</h1>
                @if($sectionTitle)
                    <p class="mt-1 text-sm text-[#6a6f73]">Chương: {{ $sectionTitle }}</p>
                @endif

                @if($lesson->content)
                    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-[#1c1d1f]">{{ $lesson->content }}</div>
                @else
                    <p class="mt-4 text-sm text-[#6a6f73]">Bài học chưa có mô tả chi tiết.</p>
                @endif

                @if($lesson->document_file)
                    <div class="mt-6">
                        <h3 class="text-sm font-bold text-[#1c1d1f]">Tài nguyên đính kèm</h3>
                        <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-[#0056D2] hover:underline">
                            Tải tài liệu bài học
                        </a>
                    </div>
                @endif

                <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-[#6a6f73]">Giảng viên</dt>
                        <dd class="mt-1 text-[#1c1d1f]">{{ $course->instructor?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-[#6a6f73]">Cập nhật</dt>
                        <dd class="mt-1 text-[#1c1d1f]">{{ $lesson->updated_at?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div
                x-show="tab === 'ai'"
                x-cloak
                data-lesson-ai
                data-ai-summary-url="{{ $aiSummaryUrl }}"
                data-ai-explain-url="{{ $aiExplainUrl }}"
                data-can-use-ai="{{ $canUseLessonAi ? '1' : '0' }}"
            >
                @if($canUseLessonAi)
                    <div class="space-y-6">
                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-base font-bold text-[#1c1d1f]">Tóm tắt bài học</h2>
                                    <p class="mt-1 text-xs text-[#6a6f73]">Dựa trên nội dung/transcript đã lưu. Không quét video YouTube.</p>
                                </div>
                                <button
                                    type="button"
                                    data-ai-generate-summary
                                    class="inline-flex h-9 items-center rounded bg-[#0056D2] px-3 text-sm font-semibold text-white hover:bg-[#0046B8] disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    Tóm tắt bài học
                                </button>
                            </div>

                            <p data-ai-summary-status class="mt-2 text-xs text-[#6a6f73]"></p>
                            <p data-ai-summary-error class="mt-2 hidden text-sm text-red-600"></p>

                            <div data-ai-summary-panel class="mt-3 space-y-4">
                                <div>
                                    <h3 class="text-sm font-bold text-[#1c1d1f]">Tóm tắt ngắn</h3>
                                    <div data-ai-summary-box class="mt-2 min-h-[72px] whitespace-pre-line rounded border border-[#d1d7dc] bg-[#f7f9fa] p-4 text-sm leading-6 text-[#1c1d1f]">
                                        Chưa có bản tóm tắt. Nhấn “Tóm tắt bài học” để tạo.
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#1c1d1f]">Các ý chính</h3>
                                    <ul data-ai-key-points class="mt-2 list-disc space-y-1 pl-5 text-sm text-[#1c1d1f]"></ul>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#1c1d1f]">Kiến thức cần nhớ</h3>
                                    <ul data-ai-takeaways class="mt-2 list-disc space-y-1 pl-5 text-sm text-[#1c1d1f]"></ul>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-[#d1d7dc] pt-5">
                            <h2 class="text-base font-bold text-[#1c1d1f]">AI giải thích</h2>
                            <p class="mt-1 text-xs text-[#6a6f73]">Chỉ trả lời theo nội dung bài hiện tại. Không tiết lộ đáp án quiz.</p>

                            <div data-ai-chat-log class="mt-3 max-h-72 space-y-3 overflow-y-auto rounded border border-[#d1d7dc] bg-white p-3"></div>

                            <form data-ai-ask-form class="mt-3 space-y-3">
                                <label class="block text-sm font-semibold text-[#1c1d1f]" for="lesson-ai-question">Bạn chưa hiểu phần nào?</label>
                                <textarea
                                    id="lesson-ai-question"
                                    name="question"
                                    rows="3"
                                    maxlength="1000"
                                    required
                                    data-ai-question-input
                                    class="w-full rounded border border-[#d1d7dc] px-3 py-2 text-sm text-[#1c1d1f] outline-none ring-[#0056D2] focus:ring-2"
                                    placeholder="Ví dụ: Phần routing trong bài này hoạt động thế nào?"
                                ></textarea>
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p data-ai-ask-status class="text-xs text-[#6a6f73]"></p>
                                    <button
                                        type="submit"
                                        data-ai-ask-submit
                                        class="inline-flex h-9 items-center rounded bg-[#1c1d1f] px-4 text-sm font-semibold text-white hover:bg-black disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        Giải thích
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-[#6a6f73]">Bạn cần đăng nhập, xác thực email và ghi danh khóa học để dùng AI hỗ trợ.</p>
                @endif
            </div>

            <div
                x-show="tab === 'notes'"
                x-cloak
                data-lesson-notes
                data-can-use-notes="{{ $canUseLessonNotes ? '1' : '0' }}"
                data-lesson-type="{{ $lesson->type }}"
                data-video-duration="{{ $videoDurationSeconds }}"
                data-store-url="{{ $lessonNotesStoreUrl }}"
                data-index-url="{{ $lessonNotesIndexUrl }}"
            >
                <script type="application/json" data-lesson-notes-json>@json($notesPayload)</script>

                @if($canUseLessonNotes)
                    <div class="space-y-5">
                        <form data-note-create-form class="space-y-3 rounded border border-[#d1d7dc] bg-[#f7f9fa] p-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <h2 class="text-base font-bold text-[#1c1d1f]">Ghi chú bài học</h2>
                                    <p class="mt-1 text-xs text-[#6a6f73]">Ghi chú chỉ hiển thị với riêng bạn.</p>
                                </div>

                                @if($lesson->type === 'video')
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="font-semibold text-[#6a6f73]">Mốc:</span>
                                        <input
                                            type="number"
                                            min="0"
                                            @if($videoDurationSeconds > 0) max="{{ $videoDurationSeconds }}" @endif
                                            name="timestamp_seconds"
                                            data-note-timestamp
                                            class="h-9 w-24 rounded border border-[#d1d7dc] px-2 text-sm text-[#1c1d1f] outline-none focus:ring-2 focus:ring-[#0056D2]"
                                            placeholder="giây"
                                        >
                                        <span data-note-timestamp-label class="min-w-12 font-semibold text-[#1c1d1f]">0:00</span>
                                    </div>
                                @endif
                            </div>

                            <textarea
                                name="content"
                                rows="4"
                                maxlength="2000"
                                required
                                data-note-content
                                class="w-full rounded border border-[#d1d7dc] bg-white px-3 py-2 text-sm leading-6 text-[#1c1d1f] outline-none focus:ring-2 focus:ring-[#0056D2]"
                                placeholder="Nhập ghi chú của bạn..."
                            ></textarea>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <p data-note-form-status class="text-xs text-[#6a6f73]"></p>
                                    <p data-note-form-error class="hidden text-sm text-red-600"></p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span data-note-char-count class="text-xs font-semibold text-[#6a6f73]">0/2000</span>
                                    <button type="submit" data-note-submit class="inline-flex h-9 items-center rounded bg-[#0056D2] px-4 text-sm font-bold text-white hover:bg-[#0046B8] disabled:cursor-not-allowed disabled:opacity-60">
                                        Lưu ghi chú
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div>
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <h3 class="text-sm font-bold text-[#1c1d1f]">Ghi chú của bạn</h3>
                                <span data-note-count class="text-xs font-semibold text-[#6a6f73]"></span>
                            </div>
                            <div data-note-empty class="rounded border border-dashed border-[#d1d7dc] p-5 text-center text-sm text-[#6a6f73]">
                                Bạn chưa có ghi chú nào cho bài học này.
                            </div>
                            <div data-note-list class="space-y-3"></div>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-[#6a6f73]">Bạn cần đăng nhập bằng tài khoản học viên và có quyền học khóa này để sử dụng ghi chú.</p>
                @endif
            </div>
            <div x-show="tab === 'qa'" x-cloak>
                <p class="text-sm text-[#6a6f73]">Khu vực thảo luận sẽ được bổ sung trong phiên bản tiếp theo.</p>
            </div>
            <div x-show="tab === 'resources'" x-cloak>
                @if($lesson->document_file)
                    <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="inline-flex h-10 items-center rounded border border-[#d1d7dc] px-4 text-sm font-semibold text-[#1c1d1f] hover:bg-[#f7f9fa]">Tải tài liệu</a>
                @else
                    <p class="text-sm text-[#6a6f73]">Không có tài liệu đính kèm.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-[#d1d7dc] px-4 py-4 sm:px-6">
        <div>
            @if($navigation['previous'])
                <a href="{{ $navigation['previous']['url'] }}" class="inline-flex h-10 items-center rounded border border-[#d1d7dc] px-4 text-sm font-semibold text-[#1c1d1f] hover:bg-[#f7f9fa]">
                    ← Bài trước
                </a>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($isEnrolled && $lesson->type !== 'video' && $canAccessLesson && $lessonState !== 'completed')
                <button type="button" data-mark-lesson-complete class="inline-flex h-10 items-center rounded bg-[#0056D2] px-4 text-sm font-bold text-white hover:bg-[#0046B8]">
                    Đánh dấu hoàn thành
                </button>
            @endif

            @if($navigation['next'])
                <a href="{{ $navigation['next']['url'] }}" class="inline-flex h-10 items-center rounded bg-[#1c1d1f] px-4 text-sm font-bold text-white hover:bg-black">
                    Bài tiếp theo →
                </a>
            @endif
        </div>
    </div>
</div>
