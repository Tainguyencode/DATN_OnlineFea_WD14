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
    'discussions' => collect(),
    'activeDiscussion' => null,
    'lessonComments' => collect(),
    'canUseLessonNotes' => false,
    'lessonNotes' => collect(),
    'lessonNotesIndexUrl' => null,
    'lessonNotesStoreUrl' => null,
    'videoDurationSeconds' => 0,
    'notesPayload' => null,
])

@php
    $videoDurationSeconds = $videoDurationSeconds ?: (isset($lesson) ? ((int) ($lesson->duration_seconds ?: $lesson->duration ?: 0)) : 0);
    $notesPayload = $notesPayload ?? $lessonNotes;

    $tabsList = [
        'overview' => 'Nội dung',
        'notes' => 'Ghi chú',
        'ai' => 'AI hỗ trợ',
        'resources' => 'Tài liệu',
    ];

    if (!in_array($lesson->type, ['quiz', 'assignment'], true)) {
        $tabsList['comments'] = 'Bình luận' . (isset($lessonComments) && $lessonComments->isNotEmpty() ? ' (' . $lessonComments->count() . ')' : '');
    }

    $initialTab = 'overview';
    if (request()->query('tab') === 'comments') {
        $initialTab = 'comments';
    } elseif (request()->query('tab') === 'notes') {
        $initialTab = 'notes';
    } elseif (request()->query('tab') === 'ai') {
        $initialTab = 'ai';
    } elseif (request()->query('tab') === 'resources') {
        $initialTab = 'resources';
    }
@endphp

<div class="learning-tabs border-t border-[#d1d7dc] bg-white" id="learning-tabs">
    <div class="border-b border-[#d1d7dc] px-4 sm:px-6" 
         x-data="{ tab: '{{ $initialTab }}' }"
    >
        <div class="flex gap-1 overflow-x-auto" role="tablist">
            @foreach($tabsList as $key => $label)
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
                    {{-- Collapsible content block --}}
                    <div
                        x-data="{
                            expanded: false,
                            shouldCollapse: false,
                            init() {
                                this.$nextTick(() => {
                                    const el = this.$refs.contentBody;
                                    if (el && el.scrollHeight > 220) {
                                        this.shouldCollapse = true;
                                    }
                                });
                            }
                        }"
                        class="mt-4 relative"
                    >
                        {{-- Content body with max-height clamp when collapsed --}}
                        <div
                            x-ref="contentBody"
                            class="whitespace-pre-line text-sm leading-7 text-[#1c1d1f] overflow-hidden transition-all duration-300"
                            :style="shouldCollapse && !expanded ? 'max-height: 200px;' : 'max-height: none;'"
                        >{{ $lesson->content }}</div>

                        {{-- Fade overlay when collapsed --}}
                        <div
                            x-show="shouldCollapse && !expanded"
                            class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent pointer-events-none"
                        ></div>

                        {{-- Toggle button --}}
                        <button
                            x-show="shouldCollapse"
                            x-on:click="expanded = !expanded"
                            type="button"
                            class="mt-2 flex items-center gap-1.5 text-sm font-semibold text-[#0056D2] hover:text-[#0040a0] transition-colors"
                        >
                            <span x-text="expanded ? 'Thu gọn' : 'Xem thêm'"></span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 transition-transform duration-200"
                                :class="expanded ? 'rotate-180' : ''"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
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
                            <p class="mt-1 text-xs text-[#6a6f73]">Ưu tiên nội dung bài học, đồng thời có thể giải thích thêm bằng kiến thức học tập liên quan. Không tiết lộ đáp án quiz.</p>

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
                                    <div class="flex flex-wrap items-center gap-2 text-sm">
                                        <span class="font-semibold text-[#6a6f73]">Mốc:</span>
                                        <input
                                            type="hidden"
                                            name="timestamp_seconds"
                                            data-note-timestamp
                                            value="0"
                                        >
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#eef5ff] border border-[#d0e2ff] px-2.5 py-1 font-mono text-sm font-bold text-[#0056D2]">
                                            <svg class="h-4 w-4 text-[#0056D2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span data-note-timestamp-label>00:00</span>
                                        </span>
                                        <span class="text-[11px] text-[#6a6f73] hidden sm:inline">Tự động lấy theo video</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <textarea
                                    name="content"
                                    rows="4"
                                    maxlength="2000"
                                    data-note-content
                                    class="w-full rounded border border-[#d1d7dc] bg-white px-3 py-2 text-sm leading-6 text-[#1c1d1f] outline-none focus:ring-2 focus:ring-[#0056D2]"
                                    placeholder="Nhập ghi chú của bạn..."
                                ></textarea>
                                <p data-note-form-error class="hidden mt-1 text-xs font-semibold text-rose-600"></p>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <p data-note-form-status class="text-xs text-[#6a6f73]"></p>
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

            <div x-show="tab === 'resources'" x-cloak>
                @if($lesson->document_file)
                    <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="inline-flex h-10 items-center rounded border border-[#d1d7dc] px-4 text-sm font-semibold text-[#1c1d1f] hover:bg-[#f7f9fa]">Tải tài liệu</a>
                @else
                    <p class="text-sm text-[#6a6f73]">Không có tài liệu đính kèm.</p>
                @endif
            </div>

            @if(!in_array($lesson->type, ['quiz', 'assignment'], true))
                <div x-show="tab === 'comments'" x-cloak>
                    <x-learning.lesson-comments
                        :lesson="$lesson"
                        :course="$course"
                        :comments="$lessonComments"
                        :is-enrolled="$isEnrolled"
                    />
                </div>
            @endif
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
            @if($isEnrolled && $canAccessLesson && $lessonState !== 'completed' && ($lesson->type === 'document' || $lesson->type === 'video'))
                <button type="button" 
                        data-mark-lesson-complete 
                        @if($lesson->type === 'video') style="display: none;" @endif
                        class="inline-flex h-10 items-center rounded bg-[#0056D2] px-4 text-sm font-bold text-white hover:bg-[#0046B8]">
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
