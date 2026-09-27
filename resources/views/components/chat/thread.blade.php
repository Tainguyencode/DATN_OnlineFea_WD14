@props([
    'context' => null,
    'sendUrl' => null,
    'lessonId' => null,
    'accent' => 'blue',
    'emptyTitle' => 'Chưa có tin nhắn',
    'emptyText' => 'Hãy bắt đầu cuộc trao đổi.',
    'composerPlaceholder' => 'Nhập tin nhắn...',
])

@php
    $instance = 'chat-'.str_replace('-', '', (string) Str::uuid());
    $conversationId = $context['conversation_id'] ?? null;
    $resolvedSendUrl = $context['send_url'] ?? $sendUrl;
    $accentClasses = $accent === 'emerald'
        ? 'bg-emerald-600 hover:bg-emerald-700 focus-visible:ring-emerald-500'
        : 'bg-blue-600 hover:bg-blue-700 focus-visible:ring-blue-500';
@endphp

<section
    {{ $attributes->class(['flex min-h-0 flex-1 flex-col bg-white dark:bg-slate-900']) }}
    data-course-chat-root
    data-chat-instance="{{ $instance }}"
    data-current-user-id="{{ auth()->id() }}"
    data-discussion-id="{{ $conversationId }}"
    data-messages-url="{{ $context['messages_url'] ?? '' }}"
    data-message-url-template="{{ $context['message_url_template'] ?? '' }}"
    data-read-url="{{ $context['read_url'] ?? '' }}"
    data-chat-cursor="{{ $context['cursor'] ?? '' }}"
    data-chat-accent="{{ $accent }}"
>
    @if($context)
        <script type="application/json" data-chat-initial>@json($context)</script>
    @endif

    <div class="relative min-h-0 flex-1 bg-slate-50 dark:bg-slate-950">
        <div data-chat-empty class="absolute inset-0 flex flex-col items-center justify-center px-6 text-center {{ !empty($context['messages']) ? 'hidden' : '' }}">
            <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-300" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.4-4 8-9 8a10 10 0 0 1-4.3-.95L3 20l1.4-3.7A7.2 7.2 0 0 1 3 12c0-4.4 4-8 9-8s9 3.6 9 8Z"/></svg>
            </span>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $emptyTitle }}</h3>
            <p class="mt-1 max-w-xs text-xs leading-5 text-slate-600 dark:text-slate-400">{{ $emptyText }}</p>
        </div>
        <div data-chat-messages class="absolute inset-0 space-y-3 overflow-y-auto p-4 sm:p-5" role="log" aria-live="polite" aria-relevant="additions text"></div>
    </div>

    @if($resolvedSendUrl)
        <div class="shrink-0 border-t border-slate-200 bg-white p-3 dark:border-slate-800 dark:bg-slate-900 sm:p-4">
            <div data-chat-reply-context class="mb-2 hidden items-center justify-between rounded-xl border-l-4 border-blue-500 bg-blue-50 px-3 py-2 dark:bg-blue-950/40">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-blue-700 dark:text-blue-300">Đang trả lời <span data-chat-reply-name></span></p>
                    <p data-chat-reply-snippet class="mt-0.5 truncate text-[11px] text-slate-600 dark:text-slate-400"></p>
                </div>
                <button type="button" data-chat-cancel-reply class="cursor-pointer rounded-lg p-1 text-slate-500 transition-colors hover:bg-blue-100 hover:text-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-blue-900" aria-label="Hủy trả lời">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>

            <form data-course-chat-send action="{{ $resolvedSendUrl }}" method="POST" enctype="multipart/form-data" class="relative space-y-2">
                @csrf
                <input type="hidden" name="reply_to_key" data-chat-reply-input>
                @if($lessonId)
                    <input type="hidden" name="lesson_id" value="{{ $lessonId }}">
                @endif

                {{-- Popup gợi ý hashtag khi gõ # --}}
                <div data-chat-hashtag-popup class="hidden absolute bottom-full left-0 right-0 mb-2 z-40 max-h-52 overflow-y-auto rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-2xl backdrop-blur-md dark:border-slate-700 dark:bg-slate-900/95">
                    <div class="px-2.5 py-1.5 flex items-center justify-between border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <span class="flex items-center gap-1.5">
                            <span>🏷️</span>
                            <span>Khóa học đã mua</span>
                        </span>
                        <button type="button" data-chat-hashtag-close class="inline-flex h-5 w-5 cursor-pointer items-center justify-center rounded-md text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white transition-colors" title="Đóng gợi ý">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div data-chat-hashtag-list class="mt-1 space-y-0.5"></div>
                </div>

                <label for="{{ $instance }}-content" class="sr-only">Nội dung tin nhắn</label>
                <textarea id="{{ $instance }}-content" name="content" rows="2" data-chat-content class="w-full resize-none rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-950 dark:text-white" placeholder="{{ $composerPlaceholder }}"></textarea>
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <label for="{{ $instance }}-file" class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-100 focus-within:ring-2 focus-within:ring-blue-500 dark:text-slate-300 dark:hover:bg-slate-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m15.2 7-6.6 6.6a2 2 0 1 0 2.8 2.8l6.4-6.6a4 4 0 0 0-5.6-5.6l-6.4 6.6a6 6 0 1 0 8.4 8.4l6.3-6.2"/></svg>
                            <span data-chat-file-label>Đính kèm</span>
                        </label>
                        <input id="{{ $instance }}-file" type="file" name="attachment" data-chat-file class="sr-only">
                    </div>
                    <button type="submit" class="{{ $accentClasses }} inline-flex cursor-pointer items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60">
                        <span>Gửi</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m4 4 17 8-17 8 4-8-4-8Zm4 8h13"/></svg>
                    </button>
                </div>
                <p data-chat-error class="hidden text-xs font-semibold text-rose-600" role="alert"></p>
            </form>
        </div>
    @endif
</section>
