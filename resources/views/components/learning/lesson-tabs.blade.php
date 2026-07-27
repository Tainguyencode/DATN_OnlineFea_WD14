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
    <div class="border-b border-[#d1d7dc] px-4 sm:px-6" x-data="{ tab: '{{ (request()->has('discussion_id') || request()->query('tab') === 'qa') ? 'qa' : 'overview' }}' }">
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
                <div class="py-2">
                    @php
                        $user = auth()->user();
                        $isInstructor = $user && $user->isInstructor() && (int) $course->instructor_id === (int) $user->id;
                        $isAdmin = $user && $user->isAdmin();
                        $canAsk = $user && $user->isStudent() && $isEnrolled;
                    @endphp

                    @if(!$isEnrolled && !$isInstructor && !$isAdmin)
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-center text-amber-800" role="alert">
                            <h5 class="font-bold text-base mb-1">Ghi danh để đặt câu hỏi</h5>
                            <p class="text-sm mb-0">Bạn cần ghi danh khóa học này để có thể xem thảo luận và đặt câu hỏi cho giảng viên.</p>
                        </div>
                    @else
                        @if($activeDiscussion)
                            <!-- CHI TIẾT CUỘC HỘI THOẠI (CHAT STYLE NATIVE TAILWIND) -->
                            <div class="rounded-lg border border-[#d1d7dc] bg-white overflow-hidden shadow-sm">
                                <div class="bg-[#f7f9fa] border-b border-[#d1d7dc] flex items-center justify-between px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('courses.lessons.show', [$course, $lesson]) }}?tab=qa" class="inline-flex items-center gap-1 rounded border border-[#1c1d1f] bg-white px-3 py-1.5 text-xs font-semibold text-[#1c1d1f] hover:bg-[#f7f9fa] transition">
                                            <span>&larr;</span> Quay lại
                                        </a>
                                        <h6 class="mb-0 text-[#1c1d1f] font-bold text-sm ml-2">{{ $activeDiscussion->title }}</h6>
                                    </div>
                                    <div>
                                        @if($activeDiscussion->is_resolved)
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">Đã giải quyết</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 border border-blue-200">Đang trao đổi</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="p-4 bg-[#f8fafc] overflow-y-auto space-y-4 chat-scroll" style="max-height: 450px;">
                                    <!-- Tin nhắn gốc (Câu hỏi) -->
                                    <div class="flex items-start">
                                        <img src="{{ $activeDiscussion->user->avatarUrl() }}" alt="Avatar" class="rounded-full mr-3 border" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-1">
                                            <div class="bg-white p-3 rounded-lg shadow-sm border border-[#d1d7dc]">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="font-bold text-sm text-[#1c1d1f]">{{ $activeDiscussion->user->name }}</span>
                                                    <span class="text-xs text-[#6a6f73]">{{ $activeDiscussion->created_at->diffForHumans() }}</span>
                                                </div>
                                                <div class="inline-flex items-center rounded bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700 border border-blue-200 mb-2">Học viên</div>
                                                <p class="text-sm text-[#1c1d1f] whitespace-pre-line leading-relaxed">{{ $activeDiscussion->content }}</p>
                                                
                                                <!-- Đính kèm -->
                                                @if($activeDiscussion->attachment_path)
                                                    <div class="mt-3 p-2 rounded border border-[#d1d7dc] bg-[#f7f9fa] inline-block max-w-full">
                                                        @if($activeDiscussion->attachment_type === 'image')
                                                            <a href="{{ $activeDiscussion->attachmentUrl() }}" target="_blank">
                                                                <img src="{{ $activeDiscussion->attachmentUrl() }}" alt="Attachment" class="rounded border shadow-xs max-h-[180px] object-contain">
                                                            </a>
                                                        @elseif($activeDiscussion->attachment_type === 'video')
                                                            <video controls class="rounded w-full max-h-[200px] max-w-[320px]">
                                                                <source src="{{ $activeDiscussion->attachmentUrl() }}">
                                                                Trình duyệt không hỗ trợ xem video.
                                                            </video>
                                                        @else
                                                            <a href="{{ $activeDiscussion->attachmentUrl() }}" target="_blank" class="text-xs font-semibold text-[#0056D2] hover:underline flex items-center gap-1">
                                                                <span>📎</span> Tải xuống: {{ Str::limit($activeDiscussion->attachment_name, 35) }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lịch sử phản hồi -->
                                    @foreach($activeDiscussion->replies as $reply)
                                        <div class="flex items-start {{ (int) $reply->user_id === (int) $user->id ? 'flex-row-reverse' : '' }}">
                                            <img src="{{ $reply->user->avatarUrl() }}" alt="Avatar" class="rounded-full border {{ (int) $reply->user_id === (int) $user->id ? 'ml-3' : 'mr-3' }}" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div class="max-w-[80%]">
                                                <div class="p-3 rounded-lg shadow-sm border {{ $reply->is_instructor_answer ? 'bg-[#fffbeb] border-[#fde68a]' : 'bg-white border-[#d1d7dc]' }}">
                                                    <div class="flex justify-between items-center mb-1 gap-4">
                                                        <span class="font-bold text-sm text-[#1c1d1f]">{{ $reply->user->name }}</span>
                                                        <span class="text-xs text-[#6a6f73]">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    
                                                    @if($reply->is_instructor_answer)
                                                        <div class="inline-flex items-center rounded bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-800 border border-amber-200 mb-2">Giảng viên</div>
                                                    @else
                                                        <div class="inline-flex items-center rounded bg-gray-50 px-2 py-0.5 text-xs font-semibold text-gray-600 border border-gray-200 mb-2">Học viên</div>
                                                    @endif
                                                    
                                                    <p class="text-sm text-[#1c1d1f] whitespace-pre-line leading-relaxed">{{ $reply->content }}</p>

                                                    <!-- Đính kèm ở reply -->
                                                    @if($reply->attachment_path)
                                                        <div class="mt-2 p-2 rounded border border-[#d1d7dc] bg-[#f7f9fa] inline-block max-w-full">
                                                            @if($reply->attachment_type === 'image')
                                                                <a href="{{ $reply->attachmentUrl() }}" target="_blank">
                                                                    <img src="{{ $reply->attachmentUrl() }}" alt="Attachment" class="rounded border shadow-xs max-h-[150px] object-contain">
                                                                </a>
                                                            @elseif($reply->attachment_type === 'video')
                                                                <video controls class="rounded w-full max-h-[180px] max-w-[280px]">
                                                                    <source src="{{ $reply->attachmentUrl() }}">
                                                                    Trình duyệt không hỗ trợ xem video.
                                                                </video>
                                                            @else
                                                                <a href="{{ $reply->attachmentUrl() }}" target="_blank" class="text-xs font-semibold text-[#0056D2] hover:underline flex items-center gap-1">
                                                                    <span>📎</span> Tải xuống: {{ Str::limit($reply->attachment_name, 25) }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Form gửi trả lời -->
                                <div class="bg-[#f7f9fa] p-3 border-t border-[#d1d7dc]">
                                    <form action="{{ route('discussions.replies.store', $activeDiscussion) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <textarea name="content" rows="3" class="w-full rounded border border-[#d1d7dc] px-3 py-2 text-sm text-[#1c1d1f] focus:outline-none focus:ring-2 focus:ring-[#0056D2]" placeholder="Nhập câu trả lời của bạn..." required></textarea>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex items-center gap-2">
                                                <label class="text-xs font-semibold text-[#6a6f73] text-nowrap" for="reply-file">Đính kèm:</label>
                                                <input type="file" name="attachment" id="reply-file" class="text-xs text-[#6a6f73] file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                            </div>
                                            <button type="submit" class="inline-flex items-center rounded bg-[#1c1d1f] px-4 py-2 text-xs font-bold text-white hover:bg-black transition">Gửi phản hồi</button>
                                        </div>
                                        <div class="text-[11px] text-[#6a6f73] mt-1">Ảnh, video hoặc tài liệu (tối đa 50MB).</div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- DANH SÁCH CUỘC HỘI THOẠI -->
                            <div x-data="{ showAskForm: false }">
                                <!-- Form đặt câu hỏi mới (chỉ hiện khi click nút) -->
                                <div x-show="showAskForm" class="rounded-lg border border-[#d1d7dc] bg-white overflow-hidden shadow-sm mb-4" x-transition x-cloak>
                                    <div class="bg-[#f7f9fa] border-b border-[#d1d7dc] flex items-center justify-between px-4 py-3">
                                        <h6 class="mb-0 text-[#1c1d1f] font-bold text-sm">Đặt câu hỏi mới cho Giảng viên</h6>
                                        <button type="button" class="text-[#6a6f73] hover:text-[#1c1d1f] text-lg font-bold" @click="showAskForm = false">&times;</button>
                                    </div>
                                    <div class="p-4 space-y-4">
                                        <form action="{{ route('courses.lessons.discussions.store', [$course, $lesson]) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                            @csrf
                                            <div>
                                                <label for="new-title" class="block text-xs font-bold text-[#1c1d1f] mb-1">Tiêu đề câu hỏi <span class="text-red-500">*</span></label>
                                                <input type="text" name="title" id="new-title" class="w-full rounded border border-[#d1d7dc] px-3 py-2 text-sm text-[#1c1d1f] focus:outline-none focus:ring-2 focus:ring-[#0056D2]" placeholder="Tóm tắt ngắn gọn câu hỏi..." required>
                                            </div>
                                            <div>
                                                <label for="new-content" class="block text-xs font-bold text-[#1c1d1f] mb-1">Nội dung chi tiết <span class="text-red-500">*</span></label>
                                                <textarea name="content" id="new-content" rows="4" class="w-full rounded border border-[#d1d7dc] px-3 py-2 text-sm text-[#1c1d1f] focus:outline-none focus:ring-2 focus:ring-[#0056D2]" placeholder="Chi tiết câu hỏi của bạn để giảng viên dễ trả lời..." required></textarea>
                                            </div>
                                            <div>
                                                <label for="new-file" class="block text-xs font-bold text-[#1c1d1f] mb-1">Tệp đính kèm (Ảnh, video hoặc tài liệu)</label>
                                                <input type="file" name="attachment" id="new-file" class="w-full rounded border border-[#d1d7dc] px-3 py-1.5 text-xs text-[#1c1d1f]">
                                            </div>
                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" class="inline-flex items-center rounded border border-[#d1d7dc] bg-white px-3 py-2 text-xs font-semibold text-[#6a6f73] hover:bg-[#f7f9fa]" @click="showAskForm = false">Hủy</button>
                                                <button type="submit" class="inline-flex items-center rounded bg-[#1c1d1f] px-4 py-2 text-xs font-bold text-white hover:bg-black">Gửi câu hỏi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Danh sách chính -->
                                <div x-show="!showAskForm">
                                    <div class="flex items-center justify-between mb-3">
                                        <h6 class="mb-0 text-[#1c1d1f] font-bold text-sm">Danh sách hội thoại hỏi đáp</h6>
                                        @if($canAsk)
                                            <button type="button" class="inline-flex items-center rounded bg-[#1c1d1f] px-3 py-2 text-xs font-bold text-white hover:bg-black transition" @click="showAskForm = true">
                                                + Đặt câu hỏi
                                            </button>
                                        @endif
                                    </div>

                                    @if($discussions->isEmpty())
                                        <div class="rounded-lg border border-dashed border-[#d1d7dc] bg-[#f7f9fa] text-center py-8">
                                            <p class="text-sm text-[#6a6f73] mb-1 font-semibold">Chưa có câu hỏi nào trong bài học này.</p>
                                            @if($canAsk)
                                                <p class="text-xs text-[#6a6f73] mb-3">Đặt câu hỏi đầu tiên của bạn cho giảng viên.</p>
                                                <button type="button" class="inline-flex items-center rounded bg-[#1c1d1f] px-3 py-1.5 text-xs font-bold text-white hover:bg-black" @click="showAskForm = true">Đặt câu hỏi</button>
                                            @endif
                                        </div>
                                    @else
                                        <div class="rounded-lg border border-[#d1d7dc] divide-y divide-[#d1d7dc] overflow-hidden bg-white shadow-xs">
                                            @foreach($discussions as $disc)
                                                @php
                                                    $hasInstructorReply = $disc->replies->contains('is_instructor_answer', true);
                                                @endphp
                                                <a href="{{ route('courses.lessons.show', [$course, $lesson, 'discussion_id' => $disc->id, 'tab' => 'qa']) }}"
                                                   class="block p-3 hover:bg-[#f7f9fa] transition">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <h6 class="mb-0 text-sm text-[#1c1d1f] font-bold">{{ $disc->title }}</h6>
                                                        <span class="text-xs text-[#6a6f73] text-nowrap ml-3">{{ $disc->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-xs text-[#6a6f73] mb-2 truncate max-w-full">
                                                        {{ Str::limit($disc->content, 110) }}
                                                    </p>
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-2">
                                                            @if($isInstructor || $isAdmin)
                                                                <img src="{{ $disc->user->avatarUrl() }}" alt="Avatar" class="rounded-full" style="width: 20px; height: 20px; object-fit: cover;">
                                                                <span class="text-xs text-[#6a6f73] font-semibold">{{ $disc->user->name }}</span>
                                                            @else
                                                                <span class="text-xs text-[#6a6f73] font-semibold">Tôi</span>
                                                            @endif
                                                            <span class="text-xs text-[#6a6f73]">&bull; {{ $disc->replies->count() }} phản hồi</span>
                                                        </div>
                                                        <div>
                                                            @if($hasInstructorReply)
                                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-100">Giảng viên đã trả lời</span>
                                                            @else
                                                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-800 border border-amber-100">Đang chờ</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
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
