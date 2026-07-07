@props([
    'recentNotifications' => collect(),
    'unreadCount' => 0,
])

<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false">
    <button type="button"
            x-on:click="open = !open"
            class="relative rounded-lg p-2 text-slate-500 transition duration-200 hover:bg-slate-50 hover:text-[#0056D2] dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-blue-300"
            aria-label="Thông báo">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>
        @if($unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open"
         x-transition
         x-cloak
         class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:w-96">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <h3 class="text-sm font-semibold text-slate-900">Thông báo</h3>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-[#0056D2] hover:text-[#0046B8]">Đọc tất cả</button>
                </form>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($recentNotifications as $notification)
                <div class="border-b border-slate-100 px-4 py-3 last:border-b-0 {{ $notification->is_read ? '' : 'bg-blue-50/50' }}">
                    <div class="flex items-start gap-2">
                        @unless($notification->is_read)
                            <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#0056D2]"></span>
                        @endunless
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $notification->title }}</p>
                            <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $notification->message }}</p>
                            <p class="mt-1 text-[11px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('notifications.read', $notification) }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-[#0056D2] hover:text-[#0046B8]">
                            {{ $notification->url ? 'Xem chi tiết' : 'Đánh dấu đã đọc' }}
                        </button>
                    </form>
                </div>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="text-sm text-slate-500">Chưa có thông báo mới.</p>
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-100 px-4 py-3">
            <a href="{{ route('notifications.index') }}" class="block text-center text-sm font-semibold text-[#0056D2] hover:text-[#0046B8]">
                Xem tất cả thông báo
            </a>
        </div>
    </div>
</div>
