@props([
    'lesson',
    'course',
    'canUseLessonAi' => false,
    'aiChatUrl' => null,
])

@if($canUseLessonAi && $aiChatUrl)
    <div
        data-ai-study-assistant
        data-ai-chat-url="{{ $aiChatUrl }}"
        data-ai-history-url="{{ $aiChatUrl }}"
        data-lesson-title="{{ $lesson->title }}"
        class="fixed bottom-5 right-5 z-[70] sm:bottom-6 sm:right-6"
    >
        <button
            type="button"
            data-ai-assistant-open
            class="inline-flex h-12 items-center gap-2 rounded-full bg-[#0056D2] px-5 text-sm font-bold text-white shadow-lg shadow-blue-950/20 transition hover:bg-[#0046B8] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-2"
            aria-haspopup="dialog"
            aria-controls="ai-study-assistant-panel"
        >
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/15 text-xs">AI</span>
            Hỏi AI
        </button>

        <section
            id="ai-study-assistant-panel"
            data-ai-assistant-panel
            class="hidden w-[calc(100vw-2rem)] max-w-[410px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-950/20 transition duration-200 sm:w-[410px]"
            role="dialog"
            aria-label="Trợ lý học tập"
        >
            <header class="flex items-start justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#0056D2] text-xs font-bold text-white">AI</span>
                        <div class="min-w-0">
                            <h2 class="truncate text-sm font-bold text-slate-950">Trợ lý học tập</h2>
                            <p class="truncate text-xs text-slate-500">Bài: {{ $lesson->title }}</p>
                        </div>
                    </div>
                    <p data-ai-assistant-status class="mt-2 text-xs font-medium text-slate-500">Sẵn sàng hỗ trợ bạn học bài.</p>
                </div>

                <div class="flex shrink-0 items-center gap-1">
                    <button
                        type="button"
                        data-ai-assistant-minimize
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-white hover:text-slate-900"
                        aria-label="Thu nhỏ"
                    >
                        <span aria-hidden="true">_</span>
                    </button>
                    <button
                        type="button"
                        data-ai-assistant-close
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-white hover:text-slate-900"
                        aria-label="Đóng"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </header>

            <div data-ai-assistant-body class="flex max-h-[min(660px,calc(100vh-8rem))] min-h-[420px] flex-col">
                <div
                    data-ai-assistant-messages
                    class="flex-1 space-y-3 overflow-y-auto bg-white px-4 py-4"
                    aria-live="polite"
                ></div>

                <div data-ai-assistant-quick-actions class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            'Tóm tắt bài' => 'Bạn hãy tóm tắt bài học hiện tại thành các ý chính dễ nhớ.',
                            'Giải thích dễ hiểu' => 'Giải thích bài học hiện tại bằng ngôn ngữ dễ hiểu hơn, có ví dụ nếu phù hợp.',
                            'Cho ví dụ' => 'Cho mình ví dụ thực tế liên quan đến bài học hiện tại.',
                            'Tạo câu hỏi ôn tập' => 'Tạo 5 câu hỏi ôn tập để mình tự kiểm tra sau bài học này.',
                            'Điểm quan trọng' => 'Phần nào trong bài học này quan trọng nhất và vì sao?',
                            'Kiểm tra kiến thức' => 'Hãy hỏi mình vài câu ngắn để kiểm tra mức độ hiểu bài học này.',
                        ] as $label => $prompt)
                            <button
                                type="button"
                                data-ai-assistant-quick-action
                                data-prompt="{{ $prompt }}"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-left text-xs font-semibold text-slate-700 transition hover:border-[#0056D2] hover:text-[#0056D2] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <form data-ai-assistant-form class="border-t border-slate-200 bg-white p-3">
                    <label for="ai-study-assistant-message" class="sr-only">Nhập câu hỏi cho AI</label>
                    <textarea
                        id="ai-study-assistant-message"
                        data-ai-assistant-input
                        rows="2"
                        maxlength="2000"
                        class="max-h-32 min-h-12 w-full resize-none rounded-xl border border-slate-300 px-3 py-2 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0056D2] focus:ring-2 focus:ring-[#0056D2]/20 disabled:bg-slate-50"
                        placeholder="Hỏi AI về bài học, code, ví dụ hoặc kiến thức liên quan..."
                    ></textarea>

                    <div class="mt-2 flex items-center justify-between gap-3">
                        <span data-ai-assistant-count class="text-xs font-medium text-slate-400">0/2000</span>
                        <button
                            type="submit"
                            data-ai-assistant-submit
                            class="inline-flex h-9 items-center rounded-lg bg-[#1c1d1f] px-4 text-sm font-bold text-white transition hover:bg-black disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Gửi
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endif
