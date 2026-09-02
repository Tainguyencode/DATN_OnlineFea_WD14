@auth
    @if(auth()->user()->isStudent() || auth()->user()->isInstructor())
        <aside
            data-floating-messenger
            data-conversations-url="{{ route('messenger.conversations.index') }}"
            data-user-id="{{ auth()->id() }}"
            class="fixed bottom-5 right-4 z-40 sm:bottom-6 sm:right-6"
        >
            <section data-messenger-panel class="mb-3 hidden h-[min(620px,calc(100vh-7rem))] w-[calc(100vw-2rem)] max-w-[390px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-700 dark:bg-slate-900" role="dialog" aria-modal="false" aria-label="Messenger">
                <div data-messenger-list-view class="flex h-full flex-col">
                    <header class="flex shrink-0 items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-950 dark:text-white">Tin nhắn</h2>
                            <p class="text-[11px] text-slate-600 dark:text-slate-400">Trao đổi Student ↔ Instructor</p>
                        </div>
                        <button type="button" data-messenger-close class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Đóng Messenger">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <div data-messenger-loading class="flex flex-1 items-center justify-center text-sm text-slate-500">Đang tải cuộc trò chuyện…</div>
                    <div data-messenger-empty class="hidden flex-1 flex-col items-center justify-center px-6 text-center">
                        <svg class="mb-3 h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.4-4 8-9 8a10 10 0 0 1-4.3-.95L3 20l1.4-3.7A7.2 7.2 0 0 1 3 12c0-4.4 4-8 9-8s9 3.6 9 8Z"/></svg>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Chưa có cuộc trò chuyện</p>
                        <p class="mt-1 text-xs leading-5 text-slate-600 dark:text-slate-400">Học viên có thể bắt đầu chat từ learning player của khóa học.</p>
                    </div>
                    <div data-messenger-conversations class="hidden flex-1 overflow-y-auto p-2"></div>
                </div>

                <div data-messenger-chat-view class="hidden h-full flex-col">
                    <header class="flex shrink-0 items-center gap-2 border-b border-slate-200 px-3 py-3 dark:border-slate-800">
                        <button type="button" data-messenger-back class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl text-slate-500 transition-colors hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-slate-800" aria-label="Quay lại danh sách">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg>
                        </button>
                        <div class="min-w-0 flex-1">
                            <h2 data-messenger-chat-title class="truncate text-sm font-extrabold text-slate-950 dark:text-white"></h2>
                            <p data-messenger-chat-course class="truncate text-[11px] text-slate-600 dark:text-slate-400"></p>
                        </div>
                        <button type="button" data-messenger-close class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl text-slate-500 transition-colors hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-slate-800" aria-label="Đóng Messenger">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
                        </button>
                    </header>
                    <x-chat.thread send-url="#" composer-placeholder="Nhập tin nhắn..." data-messenger-thread />
                </div>
            </section>

            <button type="button" data-messenger-toggle class="relative ml-auto inline-flex h-14 w-14 cursor-pointer items-center justify-center rounded-full bg-blue-600 text-white shadow-lg shadow-blue-600/30 transition-colors hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2" aria-label="Mở Messenger" aria-expanded="false">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.4-4 8-9 8a10 10 0 0 1-4.3-.95L3 20l1.4-3.7A7.2 7.2 0 0 1 3 12c0-4.4 4-8 9-8s9 3.6 9 8Z"/></svg>
                <span data-messenger-badge class="absolute -right-1 -top-1 hidden min-w-5 rounded-full bg-rose-600 px-1.5 py-0.5 text-[10px] font-black leading-4 text-white"></span>
            </button>
        </aside>
    @endif
@endauth
