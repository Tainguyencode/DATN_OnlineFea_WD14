<header data-public-header class="sticky top-0 z-40 h-16 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">
    <div class="mx-auto flex h-full max-w-[1600px] items-center gap-3 px-4 sm:px-6">
        <button type="button" x-on:click="$dispatch('open-student-sidebar')"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-700 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] lg:hidden dark:text-slate-200 dark:hover:bg-slate-800"
                aria-label="Mở menu học viên" aria-controls="student-mobile-sidebar">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>

        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2" aria-label="FEA Learning - Trang chủ">
            <img src="{{ asset('images/fea-logo.png') }}" alt="FEA Learning" class="h-10 w-auto object-contain">
            <span class="hidden text-sm font-extrabold text-slate-900 sm:inline dark:text-white">Không gian học tập</span>
        </a>

        <a href="{{ route('courses.index') }}" class="ml-auto hidden rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-blue-50 hover:text-[#0056D2] sm:inline-flex dark:text-slate-300 dark:hover:bg-slate-800">Khám phá khóa học</a>

        <a href="{{ route('student.cart') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-blue-50 hover:text-[#0056D2] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Giỏ hàng" title="Giỏ hàng">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2 5h13M9 21h.01M17 21h.01"/></svg>
            @if(($studentCartCount ?? 0) > 0)
                <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white">{{ min(99, $studentCartCount) }}</span>
            @endif
        </a>

        <div x-data="{ accountOpen: false }" class="relative">
            <button type="button" x-on:click="accountOpen = !accountOpen" x-on:keydown.escape="accountOpen = false"
                    class="flex items-center gap-2 rounded-xl p-1.5 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:hover:bg-slate-800"
                    aria-haspopup="true" :aria-expanded="accountOpen.toString()">
                <img src="{{ auth()->user()->avatarUrl() }}" alt="" class="h-8 w-8 rounded-lg object-cover">
                <span class="hidden max-w-36 truncate text-sm font-bold sm:block">{{ auth()->user()->name }}</span>
                <svg class="hidden h-4 w-4 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
            </button>
            <div x-cloak x-show="accountOpen" x-on:click.outside="accountOpen = false"
                 x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900" role="menu">
                <a href="{{ route('student.profile') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" role="menuitem">Hồ sơ cá nhân</a>
                <a href="{{ route('student.profile.security') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800" role="menuitem">Bảo mật</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-1 dark:border-slate-800">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">Đăng xuất</button>
                </form>
            </div>
        </div>
    </div>
</header>
