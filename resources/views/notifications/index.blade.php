@php
    $layout = match (auth()->user()?->role) {
        'admin' => 'admin-layout',
        'instructor' => 'instructor-layout',
        default => 'student-layout',
    };
@endphp

<x-dynamic-component :component="$layout" title="Thông báo" page-title="Thông báo" breadcrumb="Cập nhật thông tin và hoạt động mới nhất">
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Thông báo</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    @if($unreadCount > 0)
                        Bạn có <span class="font-semibold text-[#0056D2]">{{ $unreadCount }}</span> thông báo chưa đọc.
                    @else
                        Tất cả thông báo đã được đọc.
                    @endif
                </p>
            </div>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Đánh dấu tất cả đã đọc
                    </button>
                </form>
            @endif
        </div>

        {{-- Notification List --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
            @forelse($notifications as $notification)
                <div class="border-b border-slate-100 last:border-b-0 dark:border-slate-700 {{ $notification->is_read ? 'bg-white dark:bg-slate-800' : 'bg-blue-50/40 dark:bg-blue-950/20' }}">
                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $notification->typeColor() }}">
                                    {{ $notification->typeLabel() }}
                                </span>
                                @unless($notification->is_read)
                                    <span class="inline-flex h-2 w-2 rounded-full bg-[#0056D2]"></span>
                                @endunless
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>

                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $notification->title }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $notification->message }}</p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            @if($notification->url)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl bg-[#0056D2] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#0046B8]">
                                        Xem chi tiết
                                    </button>
                                </form>
                            @elseif(! $notification->is_read)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700">
                                        Đánh dấu đã đọc
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-500">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Chưa có thông báo</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Các thông báo từ hệ thống, giảng viên và quản trị viên sẽ hiển thị tại đây.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div>{{ $notifications->links() }}</div>
        @endif
    </div>
</x-dynamic-component>
