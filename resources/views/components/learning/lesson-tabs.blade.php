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
        'qa' => 'Trao đổi với giảng viên',
        'ai' => 'AI hỗ trợ',
        'resources' => 'Tài liệu',
    ];

    if (!in_array($lesson->type, ['quiz', 'assignment'], true)) {
        $tabsList['comments'] = 'Bình luận' . (isset($lessonComments) && $lessonComments->isNotEmpty() ? ' (' . $lessonComments->count() . ')' : '');
    }

    $initialTab = 'overview';
    if (request()->query('tab') === 'qa' || request()->has('discussion_id')) {
        $initialTab = 'qa';
    } elseif (request()->query('tab') === 'comments') {
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
         x-init="
            $watch('tab', value => {
                if (value === 'qa') {
                    $nextTick(() => {
                        const chatScroll = document.querySelector('.chat-scroll');
                        if (chatScroll) {
                            chatScroll.scrollTop = chatScroll.scrollHeight;
                        }
                    });
                }
            });

            if (tab === 'qa') {
                $nextTick(() => {
                    const chatScroll = document.querySelector('.chat-scroll');
                    if (chatScroll) {
                        chatScroll.scrollTop = chatScroll.scrollHeight;
                    }
                });
            }
         "
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
            <div x-show="tab === 'qa'" x-cloak>
                <div class="py-2">
                    @php
                        $user = auth()->user();
                        $instructor = $course->instructor;
                        $isInstructor = $user && $user->isInstructor() && (int) $course->instructor_id === (int) $user->id;
                        $isAdmin = $user && $user->isAdmin();
                        $canAsk = $user && $user->isStudent() && $isEnrolled;

                        // Tự động chọn cuộc hội thoại nếu chỉ có 1 câu hỏi và không yêu cầu xem danh sách
                        if (! $activeDiscussion && $discussions->count() === 1 && ! request()->has('list') && ! request()->has('new_question')) {
                            $activeDiscussion = $discussions->first();
                        }
                    @endphp

                    @if(!$isEnrolled && !$isInstructor && !$isAdmin)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-center text-amber-800" role="alert">
                            <h5 class="font-bold text-base mb-1">Ghi danh để trao đổi với giảng viên</h5>
                            <p class="text-sm mb-0 text-amber-700">Bạn cần ghi danh khóa học này để có thể xem thảo luận và đặt câu hỏi trực tiếp cho giảng viên.</p>
                        </div>
                    @else
                        <!-- KHUNG CHAT TRAO ĐỔI VỚI GIẢNG VIÊN (CHAT APP STYLE) -->
                        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm flex flex-col min-h-[520px]">
                            <!-- HEADER CHAT (LUÔN HIỂN THỊ AVATAR + TÊN GIẢNG VIÊN TRÊN NỀN TRẮNG SẠCH) -->
                            <div class="bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($activeDiscussion && $discussions->count() > 1)
                                        <a href="{{ route('courses.lessons.show', [$course, $lesson]) }}?tab=qa&list=1" class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 transition shrink-0" title="Danh sách câu hỏi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                        </a>
                                    @endif

                                    <div class="relative shrink-0">
                                        @if($instructor?->avatar)
                                            <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="w-12 h-12 rounded-full object-cover border border-slate-200 shadow-2xs">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-slate-800 text-white font-semibold flex items-center justify-center text-base border border-slate-200 shadow-2xs">
                                                {{ strtoupper(mb_substr($instructor?->name ?? 'G', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-semibold text-base text-slate-900 leading-tight truncate">
                                                {{ $instructor?->name ?? 'Giảng viên' }}
                                            </h3>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Giảng viên
                                            </span>
                                        </div>
                                        <p class="text-[13px] text-slate-500 truncate mt-0.5">
                                            Khóa học: {{ $course->title }}
                                        </p>
                                    </div>
                                </div>

                                <div class="shrink-0">
                                    @if($activeDiscussion)
                                        @if($activeDiscussion->needsReply())
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 border border-amber-200">
                                                 Chờ giảng viên trả lời
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                            Đã có phản hồi
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if($activeDiscussion)
                                <!-- DANH SÁCH BONG BÓNG TIN NHẮN (CHAT BODY KHI ĐÃ CÓ CONVERSATION) -->
                                <div id="student-chat-body" class="flex-1 p-4 sm:p-6 bg-[#f8fafc] overflow-y-auto space-y-4 max-h-[460px]">
                                    <!-- TIN NHẮN GỐC (CÂU HỎI BAN ĐẦU) -->
                                    @php
                                        $isOriginalByMe = (int) $activeDiscussion->user_id === (int) $user->id;
                                        $isOriginalInstructor = $instructor && (int) $activeDiscussion->user_id === (int) $instructor->id;
                                        $originalTimeFormat = $activeDiscussion->created_at->isToday() ? $activeDiscussion->created_at->format('H:i') : $activeDiscussion->created_at->format('d/m/Y H:i');
                                        $cleanFirstMsgContent = preg_replace('/\s+/', ' ', $activeDiscussion->content);
                                    @endphp

                                    <div id="msg-disc-{{ $activeDiscussion->id }}" class="group flex items-end gap-2.5 {{ $isOriginalByMe ? 'justify-end' : 'justify-start' }} transition-all duration-300 rounded-2xl p-1.5 hover:bg-slate-100/60">
                                        @if(! $isOriginalByMe)
                                            <div class="shrink-0 mb-1">
                                                @if($activeDiscussion->user?->avatar)
                                                    <img src="{{ $activeDiscussion->user->avatarUrl() }}" alt="{{ $activeDiscussion->user->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                                                @else
                                                    <div class="w-8 h-8 rounded-full {{ $isOriginalInstructor ? 'bg-emerald-600' : 'bg-slate-700' }} text-white font-bold flex items-center justify-center text-xs">
                                                        {{ strtoupper(mb_substr($activeDiscussion->user?->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="max-w-[85%] sm:max-w-[75%] space-y-1 {{ $isOriginalByMe ? 'items-end text-right' : 'items-start text-left' }}">
                                            <div class="flex flex-wrap items-center gap-2 px-1 {{ $isOriginalByMe ? 'justify-end' : 'justify-start' }}">
                                                <span class="text-[11px] font-bold text-slate-700">{{ $isOriginalByMe ? 'Bạn' : ($activeDiscussion->user?->name ?? 'Người dùng') }}</span>
                                                @if($isOriginalInstructor)
                                                    <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">Giảng viên</span>
                                                @endif
                                                <span class="text-[10px] text-slate-400">{{ $originalTimeFormat }}</span>

                                                @if(! $activeDiscussion->is_recalled)
                                                    <!-- NÚT TRẢ LỜI CÂU HỎI BAN ĐẦU -->
                                                    <button type="button" 
                                                            onclick="setReplyContext('', '{{ addslashes($activeDiscussion->user?->name ?? 'Người dùng') }}', '{{ addslashes(Str::limit($cleanFirstMsgContent, 70)) }}')"
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isOriginalByMe ? 'text-[#0056D2] bg-blue-50 hover:bg-blue-100 border-blue-200' : 'text-slate-700 bg-slate-100 hover:bg-blue-50 hover:text-[#0056D2] border-slate-200' }} border transition cursor-pointer shadow-2xs"
                                                            title="Trả lời tin nhắn này">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                                        <span>Trả lời</span>
                                                    </button>

                                                    @if($isOriginalByMe)
                                                        <form action="{{ route('discussions.recall', $activeDiscussion) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn thu hồi tin nhắn này?')">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-semibold text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Thu hồi tin nhắn">
                                                                <span>Thu hồi</span>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>

                                            @if($activeDiscussion->is_recalled)
                                                <div class="rounded-2xl px-4 py-3 text-sm italic bg-slate-100 text-slate-400 border border-slate-200 rounded-bl-xs flex items-center gap-1.5 select-none">
                                                    <span>🚫</span>
                                                    <span>Tin nhắn đã được thu hồi</span>
                                                </div>
                                            @else
                                                <div class="relative rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-xs {{ $isOriginalByMe ? 'bg-[#0056D2] text-white rounded-br-xs' : 'bg-white text-slate-900 border border-slate-200 rounded-bl-xs' }}">
                                                    @if($activeDiscussion->content)
                                                        <p class="whitespace-pre-line text-left">{{ $activeDiscussion->content }}</p>
                                                    @endif
                                                    
                                                    <!-- Đính kèm -->
                                                    @if($activeDiscussion->attachment_path)
                                                        <div class="{{ $activeDiscussion->content ? 'mt-2.5 pt-2 border-t ' . ($isOriginalByMe ? 'border-white/20' : 'border-slate-100') : '' }}">
                                                            @if($activeDiscussion->attachment_type === 'image')
                                                                <a href="{{ $activeDiscussion->attachmentUrl() }}" target="_blank" class="block">
                                                                    <img src="{{ $activeDiscussion->attachmentUrl() }}" alt="Attachment" class="rounded-xl border max-h-[200px] object-contain bg-black/10">
                                                                </a>
                                                            @elseif($activeDiscussion->attachment_type === 'video')
                                                                <video controls class="rounded-xl w-full max-h-[220px] max-w-[320px] bg-black">
                                                                    <source src="{{ $activeDiscussion->attachmentUrl() }}">
                                                                </video>
                                                            @else
                                                                <a href="{{ $activeDiscussion->attachmentUrl() }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold {{ $isOriginalByMe ? 'text-white underline' : 'text-[#0056D2] hover:underline' }}">
                                                                    <span>📎</span> Tải tệp: {{ Str::limit($activeDiscussion->attachment_name, 35) }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        @if($isOriginalByMe)
                                            <div class="shrink-0 mb-1">
                                                @if($user->avatar)
                                                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-[#0056D2]">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-[#0056D2] text-white font-bold flex items-center justify-center text-xs">
                                                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- CÁC TIN NHẮN PHẢN HỒI (REPLIES) -->
                                    @foreach($activeDiscussion->replies as $reply)
                                        @php
                                            $isMyReply = (int) $reply->user_id === (int) $user->id;
                                            $isInstructorReply = $reply->is_instructor_answer || ($instructor && (int) $reply->user_id === (int) $instructor->id);
                                            $replyTimeFormat = $reply->created_at->isToday() ? $reply->created_at->format('H:i') : $reply->created_at->format('d/m/Y H:i');
                                            $cleanReplyContent = preg_replace('/\s+/', ' ', $reply->content);
                                        @endphp

                                        <div id="msg-reply-{{ $reply->id }}" class="group flex items-end gap-2.5 {{ $isMyReply ? 'justify-end' : 'justify-start' }} transition-all duration-300 rounded-2xl p-1.5 hover:bg-slate-100/60">
                                            @if(! $isMyReply)
                                                <div class="shrink-0 mb-1">
                                                    @if($reply->user?->avatar)
                                                        <img src="{{ $reply->user->avatarUrl() }}" alt="{{ $reply->user->name }}" class="w-8 h-8 rounded-full object-cover border {{ $isInstructorReply ? 'border-emerald-500' : 'border-slate-200' }}">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full {{ $isInstructorReply ? 'bg-emerald-600' : 'bg-slate-700' }} text-white font-bold flex items-center justify-center text-xs">
                                                            {{ strtoupper(mb_substr($reply->user?->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="max-w-[85%] sm:max-w-[75%] space-y-1 {{ $isMyReply ? 'items-end text-right' : 'items-start text-left' }}">
                                                <div class="flex flex-wrap items-center gap-2 px-1 {{ $isMyReply ? 'justify-end' : 'justify-start' }}">
                                                    <span class="text-[11px] font-bold text-slate-700">{{ $isMyReply ? 'Bạn' : ($reply->user?->name ?? 'Người dùng') }}</span>
                                                    @if($isInstructorReply)
                                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">Giảng viên</span>
                                                    @endif
                                                    <span class="text-[10px] text-slate-400">{{ $replyTimeFormat }}</span>
                                                    @if($reply->is_helpful)
                                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 rounded">✔️ Hữu ích</span>
                                                    @endif
                                                    
                                                    @if(! $reply->is_recalled)
                                                        <!-- NÚT TRẢ LỜI (ACTION TRÊN MỖI TIN NHẮN) -->
                                                        <button type="button" 
                                                                onclick="setReplyContext('{{ $reply->id }}', '{{ addslashes($reply->user?->name ?? 'Người dùng') }}', '{{ addslashes(Str::limit($cleanReplyContent, 70)) }}')"
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isMyReply ? 'text-[#0056D2] bg-blue-50 hover:bg-blue-100 border-blue-200' : 'text-slate-700 bg-slate-100 hover:bg-blue-50 hover:text-[#0056D2] border-slate-200' }} border transition cursor-pointer shadow-2xs"
                                                                title="Trả lời tin nhắn này">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                                            <span>Trả lời</span>
                                                        </button>

                                                        @if($isMyReply)
                                                            <form action="{{ route('discussions.replies.recall', $reply) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn thu hồi tin nhắn này?')">
                                                                @csrf
                                                                <button type="submit" class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[11px] font-semibold text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition cursor-pointer" title="Thu hồi tin nhắn">
                                                                    <span>Thu hồi</span>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>

                                                @if($reply->is_recalled)
                                                    <div class="rounded-2xl px-4 py-3 text-sm italic {{ $isMyReply ? 'bg-blue-50 text-blue-400 border border-blue-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }} rounded-bl-xs flex items-center gap-1.5 select-none">
                                                        <span>🚫</span>
                                                        <span>Tin nhắn đã được thu hồi</span>
                                                    </div>
                                                @else
                                                    <div class="relative rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-xs {{ $isMyReply ? 'bg-[#0056D2] text-white rounded-br-xs' : ($isInstructorReply ? 'bg-[#ecfdf5] text-slate-900 border border-emerald-200 rounded-bl-xs' : 'bg-white text-slate-900 border border-slate-200 rounded-bl-xs') }}">
                                                        <!-- QUOTE TRÍCH DẪN TIN NHẮN GỐC NẾU CÓ -->
                                                        @if($reply->replyTo)
                                                            <div onclick="scrollToMessage('msg-reply-{{ $reply->replyTo->id }}')" class="cursor-pointer mb-2.5 rounded-xl {{ $isMyReply ? 'bg-white/20 text-white border-l-3 border-white' : 'bg-slate-100/90 text-slate-700 border-l-3 border-[#0056D2]' }} px-3 py-1.5 text-xs transition hover:opacity-80 select-none text-left" title="Bấm để xem tin nhắn gốc">
                                                                <div class="font-bold text-[11px] flex items-center gap-1">
                                                                    <span>↪</span> {{ $reply->replyTo->user?->name ?? 'Người dùng' }}
                                                                    @if($reply->replyTo->is_instructor_answer)
                                                                        <span class="text-[9px] font-semibold px-1 rounded {{ $isMyReply ? 'bg-white/30 text-white' : 'bg-emerald-100 text-emerald-800' }}">Giảng viên</span>
                                                                    @endif
                                                                </div>
                                                                <p class="truncate text-[11px] opacity-90 mt-0.5 italic">"{{ Str::limit($reply->replyTo->content, 65) }}"</p>
                                                            </div>
                                                        @endif

                                                        @if($reply->content)
                                                            <p class="whitespace-pre-line text-left">{{ $reply->content }}</p>
                                                        @endif

                                                        <!-- Đính kèm ở reply -->
                                                        @if($reply->attachment_path)
                                                            <div class="{{ $reply->content ? 'mt-2.5 pt-2 border-t ' . ($isMyReply ? 'border-white/20' : 'border-slate-200/60') : '' }}">
                                                                @if($reply->attachment_type === 'image')
                                                                    <a href="{{ $reply->attachmentUrl() }}" target="_blank" class="block">
                                                                        <img src="{{ $reply->attachmentUrl() }}" alt="Attachment" class="rounded-xl border max-h-[180px] object-contain bg-black/10">
                                                                    </a>
                                                                @elseif($reply->attachment_type === 'video')
                                                                    <video controls class="rounded-xl w-full max-h-[200px] max-w-[280px] bg-black">
                                                                        <source src="{{ $reply->attachmentUrl() }}">
                                                                    </video>
                                                                @else
                                                                    <a href="{{ $reply->attachmentUrl() }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold {{ $isMyReply ? 'text-white underline' : 'text-[#0056D2] hover:underline' }}">
                                                                        <span>📎</span> Tải tệp: {{ Str::limit($reply->attachment_name, 30) }}
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if(auth()->check() && (int)$reply->user_id !== (int)auth()->id())
                                                            @php
                                                                $isDiscussionOwner = (int) $activeDiscussion->user_id === (int) auth()->id();
                                                                $isInstructorUser = auth()->user()->role === 'admin' || (auth()->user()->role === 'instructor' && (int) $course->instructor_id === (int) auth()->id());
                                                            @endphp
                                                            @if($isDiscussionOwner || $isInstructorUser)
                                                                <div class="mt-2 pt-2 border-t border-slate-100 flex justify-end">
                                                                    <form action="{{ route('discussions.replies.toggle-helpful', $reply) }}" method="POST" onsubmit="sessionStorage.setItem('qa_submitted_scroll', window.scrollY.toString())">
                                                                        @csrf
                                                                        <button type="submit" class="text-[11px] font-bold px-2 py-0.5 rounded border transition duration-200 cursor-pointer {{ $reply->is_helpful ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-300' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-300' }}">
                                                                            {{ $reply->is_helpful ? 'Bỏ đánh dấu hữu ích' : '👍 Hữu ích' }}
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>

                                            @if($isMyReply)
                                                <div class="shrink-0 mb-1">
                                                    @if($user->avatar)
                                                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-[#0056D2]">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-[#0056D2] text-white font-bold flex items-center justify-center text-xs">
                                                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <!-- FORM GỬI TIN NHẮN PHẢN HỒI (CHAT INPUT FOOTER) -->
                                <div class="bg-white p-3.5 sm:p-4 border-t border-slate-200">
                                    <!-- THANH HIỂN THỊ ĐANG TRẢ LỜI TIN NHẮN NÀO -->
                                    <div id="reply-context-bar" class="hidden items-center justify-between bg-blue-50 border-l-4 border-[#0056D2] px-3.5 py-2 rounded-xl mb-2 transition-all">
                                        <div class="min-w-0 flex-1 pr-2">
                                            <div class="text-xs font-bold text-[#0056D2] flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                                <span>Đang trả lời <span id="reply-target-sender" class="font-extrabold underline"></span></span>
                                            </div>
                                            <p id="reply-target-text" class="text-[11px] text-slate-600 truncate mt-0.5 italic"></p>
                                        </div>
                                        <button type="button" onclick="cancelReplyContext()" class="text-slate-400 hover:text-slate-700 text-lg font-bold leading-none p-1 rounded-full hover:bg-blue-100 shrink-0 cursor-pointer" title="Hủy trả lời tin nhắn này">&times;</button>
                                    </div>

                                    <form id="student-reply-form" action="{{ route('discussions.replies.store', $activeDiscussion) }}" method="POST" enctype="multipart/form-data" class="space-y-2.5" onsubmit="return validateStudentReplyForm()">
                                        @csrf
                                        <input type="hidden" name="reply_to_message_id" id="reply-to-message-id" value="">
                                        <div class="relative">
                                            <textarea id="student-reply-content" 
                                                      name="content" 
                                                      rows="2" 
                                                      oninput="clearStudentReplyError()"
                                                      class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-[#1c1d1f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0056D2] @error('content') border-rose-500 focus:ring-rose-500 @enderror" 
                                                      placeholder="Nhập câu trả lời hoặc trao đổi thêm với giảng viên...">{{ old('content') }}</textarea>
                                            <p id="student-reply-validation-error" class="hidden mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Vui lòng nhập nội dung phản hồi hoặc đính kèm tệp tin.</span>
                                            </p>
                                            @error('content') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition" for="reply-file">
                                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    <span>Đính kèm</span>
                                                </label>
                                                <input type="file" name="attachment" id="reply-file" class="hidden" onchange="handleFileSelected(this, 'reply-file-preview', 'reply-file-name', 'student')">
                                                
                                                <!-- BOX HIỂN THỊ TÊN FILE KHI ĐÃ CHỌN -->
                                                <div id="reply-file-preview" class="hidden items-center gap-1.5 bg-blue-50 border border-blue-300 text-[#0056D2] px-3 py-1 rounded-lg text-xs font-medium shadow-2xs">
                                                    <span>📎</span>
                                                    <span id="reply-file-name" class="max-w-[200px] sm:max-w-[280px] truncate font-semibold"></span>
                                                    <button type="button" onclick="removeSelectedFile('reply-file', 'reply-file-preview')" class="text-rose-500 hover:text-rose-700 font-bold ml-1 cursor-pointer text-sm leading-none" title="Hủy tệp này">&times;</button>
                                                </div>

                                                <span id="reply-file-hint" class="text-[11px] text-slate-400">Ảnh, video hoặc tài liệu (tối đa 50MB)</span>
                                            </div>
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] px-5 py-2 text-xs font-bold text-white hover:bg-[#0046B8] transition shadow-xs">
                                                <span>Gửi</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @elseif($discussions->count() > 1 && ! request()->has('new_question'))
                                <!-- DANH SÁCH CÁC CUỘC HỘI THOẠI TRƯỚC ĐÓ (KHI CÓ NHIỀU CÂU HỎI) -->
                                <div class="flex-1 p-4 sm:p-5 bg-[#f8fafc] overflow-y-auto space-y-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-semibold text-slate-900">Các cuộc trao đổi của bạn</h4>
                                        @if($canAsk)
                                            <a href="{{ route('courses.lessons.show', [$course, $lesson]) }}?tab=qa&new_question=1" class="inline-flex items-center gap-1 rounded-xl bg-[#0056D2] px-3.5 py-1.5 text-xs font-bold text-white hover:bg-[#0046B8] transition shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Đặt câu hỏi mới
                                            </a>
                                        @endif
                                    </div>

                                    <div class="rounded-xl border border-slate-200 divide-y border-slate-200 overflow-hidden bg-white shadow-2xs">
                                        @foreach($discussions as $disc)
                                            @php
                                                $isAnswered = $disc->isAnswered();
                                                $timeLabel = $disc->created_at->isToday() ? $disc->created_at->format('H:i') : $disc->created_at->format('d/m/Y H:i');
                                            @endphp
                                            <a href="{{ route('courses.lessons.show', [$course, $lesson, 'discussion_id' => $disc->id, 'tab' => 'qa']) }}"
                                               class="block p-4 hover:bg-slate-50 transition group">
                                                <div class="flex items-start justify-between gap-3 mb-1">
                                                    <h5 class="text-sm font-semibold text-slate-900 group-hover:text-[#0056D2] transition truncate">
                                                        {{ $disc->title }}
                                                    </h5>
                                                    <span class="text-[11px] text-slate-400 shrink-0">{{ $timeLabel }}</span>
                                                </div>
                                                <p class="text-xs text-slate-500 line-clamp-1 mb-2">{{ $disc->content }}</p>
                                                <div class="flex items-center gap-2">
                                                    @if($isAnswered)
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Đã trả lời</span>
                                                    @else
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Chờ giảng viên phản hồi</span>
                                                    @endif
                                                    <span class="text-[10px] text-slate-400">· {{ $disc->replies_count ?? $disc->replies->count() }} phản hồi</span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- MÀN HÌNH CHƯA CÓ CONVERSATION (EMPTY STATE + FORM GỬI CÂU HỎI TRỰC TIẾP) -->
                                <div class="flex-1 flex flex-col justify-between bg-[#f8fafc]">
                                    <!-- THÔNG ĐIỆP CHÀO MỪNG TỪ GIẢNG VIÊN -->
                                    <div class="flex-1 flex flex-col items-center justify-center py-12 px-4 text-center">
                                        <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-3 overflow-hidden border border-slate-200 shadow-2xs">
                                            @if($instructor?->avatar)
                                                <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-lg font-bold text-slate-700">{{ strtoupper(mb_substr($instructor?->name ?? 'G', 0, 1)) }}</span>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-semibold text-slate-900">{{ $instructor?->name ?? 'Giảng viên' }}</h4>
                                        <p class="text-xs text-slate-500 mt-0.5">Giảng viên khóa học</p>
                                        <p class="text-xs text-slate-600 mt-3 max-w-sm">Bạn có câu hỏi về bài học? Hãy gửi tin nhắn cho giảng viên.</p>
                                    </div>

                                    <!-- FORM NHẬP CÂU HỎI BAN ĐẦU -->
                                    <div class="bg-white p-3.5 sm:p-4 border-t border-slate-200">
                                        <form id="initial-qa-form" action="{{ route('courses.lessons.discussions.store', [$course, $lesson]) }}?tab=qa" method="POST" enctype="multipart/form-data" class="space-y-3" onsubmit="return validateInitialQaForm()">
                                            @csrf
                                            <div class="relative">
                                                <textarea id="initial-qa-content" 
                                                          name="content" 
                                                          rows="3" 
                                                          oninput="clearInitialQaError()"
                                                          class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3.5 py-2.5 text-sm text-[#1c1d1f] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0056D2] @error('content') border-rose-500 focus:ring-rose-500 @enderror" 
                                                          placeholder="Nhập câu hỏi hoặc vấn đề của bạn...">{{ old('content') }}</textarea>
                                                <p id="initial-qa-validation-error" class="hidden mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Vui lòng nhập câu hỏi hoặc đính kèm tệp tin.</span>
                                                </p>
                                                @error('content') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                            </div>

                                            <div class="flex flex-wrap items-center justify-between gap-3">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <label class="inline-flex items-center gap-1.5 cursor-pointer rounded-lg border border-slate-300 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition" for="initial-qa-file">
                                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                        <span>Đính kèm</span>
                                                    </label>
                                                    <input type="file" name="attachment" id="initial-qa-file" class="hidden" onchange="handleFileSelected(this, 'initial-file-preview', 'initial-file-name', 'initial')">
                                                    
                                                    <!-- BOX HIỂN THỊ TÊN FILE KHI ĐÃ CHỌN -->
                                                    <div id="initial-file-preview" class="hidden items-center gap-1.5 bg-blue-50 border border-blue-300 text-[#0056D2] px-3 py-1 rounded-lg text-xs font-medium shadow-2xs">
                                                        <span>📎</span>
                                                        <span id="initial-file-name" class="max-w-[200px] sm:max-w-[280px] truncate font-semibold"></span>
                                                        <button type="button" onclick="removeSelectedFile('initial-qa-file', 'initial-file-preview')" class="text-rose-500 hover:text-rose-700 font-bold ml-1 cursor-pointer text-sm leading-none" title="Hủy tệp này">&times;</button>
                                                    </div>

                                                    <span class="text-[11px] text-slate-400">Ảnh, video hoặc tài liệu (tối đa 50MB)</span>
                                                </div>
                                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-[#0056D2] px-5 py-2 text-xs font-bold text-white hover:bg-[#0046B8] transition shadow-xs">
                                                    <span>Gửi</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
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

<style>
    .reply-highlight {
        animation: highlightFlash 2s ease-out;
    }
    @keyframes highlightFlash {
        0% { background-color: rgba(254, 240, 138, 0.7); box-shadow: 0 0 0 2px #eab308; border-radius: 1rem; }
        70% { background-color: rgba(254, 240, 138, 0.4); box-shadow: 0 0 0 1px #eab308; }
        100% { background-color: transparent; box-shadow: none; }
    }
</style>

<script>
    function validateStudentReplyForm() {
        const textarea = document.getElementById('student-reply-content');
        const fileInput = document.getElementById('reply-file');
        const errEl = document.getElementById('student-reply-validation-error');
        const hasText = textarea && textarea.value.trim().length > 0;
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

        if (!hasText && !hasFile) {
            if (errEl) errEl.classList.remove('hidden');
            if (textarea) {
                textarea.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                textarea.focus();
            }
            return false;
        }
        if (errEl) errEl.classList.add('hidden');
        sessionStorage.setItem('qa_submitted_scroll', window.scrollY.toString());
        return true;
    }

    function clearStudentReplyError() {
        const textarea = document.getElementById('student-reply-content');
        const errEl = document.getElementById('student-reply-validation-error');
        if (errEl) errEl.classList.add('hidden');
        if (textarea) textarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
    }

    function validateInitialQaForm() {
        const textarea = document.getElementById('initial-qa-content');
        const fileInput = document.getElementById('initial-qa-file');
        const errEl = document.getElementById('initial-qa-validation-error');
        const hasText = textarea && textarea.value.trim().length > 0;
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

        if (!hasText && !hasFile) {
            if (errEl) errEl.classList.remove('hidden');
            if (textarea) {
                textarea.classList.add('border-rose-500', 'ring-2', 'ring-rose-500');
                textarea.focus();
            }
            return false;
        }
        if (errEl) errEl.classList.add('hidden');
        sessionStorage.setItem('qa_submitted_scroll', window.scrollY.toString());
        return true;
    }

    function clearInitialQaError() {
        const textarea = document.getElementById('initial-qa-content');
        const errEl = document.getElementById('initial-qa-validation-error');
        if (errEl) errEl.classList.add('hidden');
        if (textarea) textarea.classList.remove('border-rose-500', 'ring-2', 'ring-rose-500');
    }

    function handleFileSelected(input, previewId, nameId, formType) {
        if (formType === 'student') clearStudentReplyError();
        if (formType === 'initial') clearInitialQaError();
        const previewEl = document.getElementById(previewId);
        const nameEl = document.getElementById(nameId);
        if (!input.files || !input.files[0]) {
            if (previewEl) {
                previewEl.classList.add('hidden');
                previewEl.classList.remove('flex');
            }
            return;
        }
        const file = input.files[0];
        const fileSize = (file.size / (1024 * 1024)).toFixed(1);
        if (nameEl) {
            nameEl.textContent = `${file.name} (${fileSize} MB)`;
        }
        if (previewEl) {
            previewEl.classList.remove('hidden');
            previewEl.classList.add('flex');
        }
    }

    function removeSelectedFile(inputId, previewId) {
        const input = document.getElementById(inputId);
        const previewEl = document.getElementById(previewId);
        if (input) {
            input.value = '';
        }
        if (previewEl) {
            previewEl.classList.add('hidden');
            previewEl.classList.remove('flex');
        }
    }

    function setReplyContext(replyId, senderName, snippet) {
        const replyBar = document.getElementById('reply-context-bar');
        const replyInput = document.getElementById('reply-to-message-id');
        const replySender = document.getElementById('reply-target-sender');
        const replyText = document.getElementById('reply-target-text');
        const contentInput = document.getElementById('student-reply-content');

        if (replyBar && replyInput && replySender && replyText) {
            replyInput.value = replyId;
            replySender.textContent = senderName;
            replyText.textContent = `"${snippet}"`;
            replyBar.classList.remove('hidden');
            replyBar.classList.add('flex');
            
            if (contentInput) {
                contentInput.focus();
                contentInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }

    function cancelReplyContext() {
        const replyBar = document.getElementById('reply-context-bar');
        const replyInput = document.getElementById('reply-to-message-id');

        if (replyBar && replyInput) {
            replyInput.value = '';
            replyBar.classList.add('hidden');
            replyBar.classList.remove('flex');
        }
    }

    function scrollToMessage(elementId) {
        const el = document.getElementById(elementId);
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('reply-highlight');
            setTimeout(() => {
                el.classList.remove('reply-highlight');
            }, 2100);
        }
    }

    function restoreQaScroll() {
        const savedQaScroll = sessionStorage.getItem('qa_submitted_scroll');
        if (savedQaScroll !== null) {
            sessionStorage.removeItem('qa_submitted_scroll');
            window.scrollTo({
                top: parseInt(savedQaScroll, 10),
                behavior: 'instant'
            });
            setTimeout(() => {
                window.scrollTo({
                    top: parseInt(savedQaScroll, 10),
                    behavior: 'instant'
                });
            }, 50);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        restoreQaScroll();
        const studentChatBody = document.getElementById('student-chat-body');
        if (studentChatBody) {
            studentChatBody.scrollTop = studentChatBody.scrollHeight;
        }
    });

    window.addEventListener('load', () => {
        restoreQaScroll();
        const studentChatBody = document.getElementById('student-chat-body');
        if (studentChatBody) {
            studentChatBody.scrollTop = studentChatBody.scrollHeight;
        }
    });
</script>
