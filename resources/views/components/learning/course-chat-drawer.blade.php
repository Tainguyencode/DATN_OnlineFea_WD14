@props([
    'course',
    'lesson',
    'isEnrolled' => false,
    'courseDiscussion' => null,
    'chatContext' => null,
])

@php
    $user = auth()->user();
    $instructor = $course->instructor;
    $isInstructor = $user && $user->isInstructor() && (int) $course->instructor_id === (int) $user->id;
    $isAdmin = $user && $user->isAdmin();
    $canAsk = $user && $user->isStudent() && $isEnrolled;
    $canOpen = $isEnrolled || $isInstructor || $isAdmin;
    $initialSendUrl = $canAsk && ! $chatContext
        ? route('courses.lessons.discussions.store', [$course, $lesson])
        : null;
@endphp

<div x-show="chatOpen" x-cloak data-learning-chat-drawer class="pointer-events-none fixed inset-0 z-50 overflow-hidden" role="dialog" aria-modal="false" aria-label="Trao đổi với giảng viên">
    <div class="pointer-events-auto fixed inset-y-0 right-0 flex max-w-full shadow-2xl">
        <div
            x-show="chatOpen"
            x-transition:enter="transform transition ease-out duration-300 motion-reduce:transition-none"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200 motion-reduce:transition-none"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="flex h-full w-screen max-w-md flex-col border-l border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900 sm:max-w-lg"
        >
            <header class="flex shrink-0 items-center justify-between gap-3 border-b border-slate-200 px-4 py-3.5 dark:border-slate-800">
                <div class="flex min-w-0 items-center gap-3">
                    @if($instructor?->avatar)
                        <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="h-11 w-11 shrink-0 rounded-full border border-slate-200 object-cover dark:border-slate-700">
                    @else
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-slate-800 text-sm font-bold text-white">
                            {{ strtoupper(mb_substr($instructor?->name ?? 'G', 0, 1)) }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="truncate text-sm font-extrabold text-slate-950 dark:text-white">{{ $instructor?->name ?? 'Giảng viên' }}</h2>
                            <span class="rounded-md border border-emerald-200 bg-emerald-50 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300">Giảng viên</span>
                        </div>
                        <p class="mt-0.5 truncate text-xs text-slate-600 dark:text-slate-400">{{ $course->title }}</p>
                    </div>
                </div>
                <button type="button" @click="chatOpen = false" class="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Đóng cửa sổ trao đổi">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </header>

            @if(! $canOpen)
                <div class="flex flex-1 flex-col items-center justify-center bg-slate-50 px-6 text-center dark:bg-slate-950">
                    <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 10V8a5 5 0 0 1 10 0v2m-11 0h12a1 1 0 0 1 1 1v9H5v-9a1 1 0 0 1 1-1Z"/></svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Ghi danh để trao đổi</h3>
                    <p class="mt-1 max-w-xs text-xs leading-5 text-slate-600 dark:text-slate-400">Bạn cần quyền học khóa này để gửi và nhận tin nhắn với giảng viên.</p>
                </div>
            @else
                <x-chat.thread
                    :context="$chatContext"
                    :send-url="$initialSendUrl"
                    :lesson-id="$lesson->id"
                    empty-title="Bắt đầu cuộc trao đổi"
                    empty-text="Gửi câu hỏi về bài học hoặc khóa học cho giảng viên."
                    composer-placeholder="Nhập câu hỏi hoặc tin nhắn..."
                />
            @endif
        </div>
    </div>
</div>
