@props([
    'course',
    'courseProgress' => 0,
    'sidebarOpen' => true,
])

<header class="learning-header sticky top-0 z-50 flex h-14 items-center gap-3 border-b border-[#2d2f31] bg-[#1c1d1f] px-3 text-white sm:h-16 sm:px-4">
    <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-white/90 transition hover:text-white" aria-label="Quay lại khóa học">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span class="hidden sm:inline">Quay lại</span>
    </a>

    <div class="hidden min-w-0 flex-1 lg:block">
        <p class="truncate text-sm font-semibold">{{ $course->title }}</p>
    </div>

    <div class="ml-auto flex items-center gap-2 sm:gap-3">
        <div class="hidden items-center gap-2 sm:flex">
            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-white/20 sm:w-32">
                <div class="h-full rounded-full bg-[#0056D2] transition-all duration-300" data-header-progress-bar style="width: {{ min(100, max(0, $courseProgress)) }}%"></div>
            </div>
            <span class="text-xs font-semibold text-white/80" data-header-progress-text>{{ number_format($courseProgress, 0) }}%</span>
        </div>

        <a href="{{ route('courses.show', $course->slug) }}#reviews" class="hidden rounded border border-white/20 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/10 sm:inline-flex">
            Đánh giá
        </a>

        <button
            type="button"
            class="inline-flex h-9 w-9 items-center justify-center rounded border border-white/20 text-white transition hover:bg-white/10 lg:hidden"
            data-toggle-sidebar
            aria-label="Mở nội dung khóa học"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <button
            type="button"
            class="hidden h-9 items-center justify-center rounded border border-white/20 px-3 text-xs font-semibold text-white transition hover:bg-white/10 lg:inline-flex"
            data-toggle-sidebar-desktop
            aria-label="Thu gọn nội dung khóa học"
        >
            Nội dung
        </button>

        @auth
            <a href="{{ auth()->user()->dashboardUrl() }}" class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#0056D2] text-xs font-bold text-white" aria-label="Tài khoản">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </a>
        @endauth
    </div>
</header>
