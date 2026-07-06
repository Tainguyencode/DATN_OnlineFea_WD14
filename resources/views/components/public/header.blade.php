<header class="sticky top-0 z-50 border-b border-[#E5E7EB] bg-white" x-data="{ mobileOpen: false, userOpen: false }">
    <div class="ui-container">
        <div class="flex h-16 items-center gap-4 lg:gap-8">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
                <img src="{{ asset('images/fea-logo.png') }}" alt="FEA Learning" class="h-9 w-9 object-contain">
                <span class="hidden text-lg font-bold text-[#0F172A] sm:inline">FEA Learning</span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium text-[#4B5563] lg:flex">
                <a href="{{ route('home') }}" class="transition hover:text-[#2563EB] {{ request()->routeIs('home') ? 'text-[#2563EB]' : '' }}">Trang chủ</a>
                <a href="{{ route('courses.index') }}" class="transition hover:text-[#2563EB] {{ request()->routeIs('courses.*') ? 'text-[#2563EB]' : '' }}">Khóa học</a>
                <a href="{{ route('home') }}#categories" class="transition hover:text-[#2563EB]">Danh mục</a>
                <a href="{{ route('home') }}#instructors" class="transition hover:text-[#2563EB]">Giảng viên</a>
                <a href="{{ route('home') }}#faq" class="transition hover:text-[#2563EB]">Blog</a>
            </nav>

            <form action="{{ route('courses.index') }}" method="GET" class="hidden flex-1 max-w-xl md:block">
                <label class="relative block">
                    <span class="sr-only">Tìm kiếm khóa học</span>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-[#9CA3AF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm khóa học, kỹ năng, giảng viên..." class="ui-input pl-10">
                </label>
            </form>

            <div class="ml-auto flex items-center gap-1 sm:gap-2">
                @auth
                    <a href="{{ route('student.cart') }}" class="ui-btn-ghost hidden sm:inline-flex" aria-label="Giỏ hàng">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </a>

                    <div class="relative">
                        <button type="button" @click="userOpen = !userOpen" class="flex items-center gap-2 rounded-lg border border-[#E5E7EB] px-2 py-1.5 transition hover:bg-[#F8FAFC]">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#2563EB] text-xs font-bold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden max-w-[120px] truncate text-sm font-medium text-[#1F2937] md:inline">{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 text-[#9CA3AF]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="userOpen" @click.outside="userOpen = false" x-cloak class="absolute right-0 mt-2 w-56 rounded-xl border border-[#E5E7EB] bg-white py-2 shadow-lg">
                            <a href="{{ auth()->user()->dashboardUrl() }}" class="block px-4 py-2 text-sm text-[#1F2937] hover:bg-[#F8FAFC]">Dashboard</a>
                            @if(auth()->user()->isStudent() && !auth()->user()->instructorApplication)
                                <a href="{{ route('student.become-instructor') }}" class="block px-4 py-2 text-sm text-[#1F2937] hover:bg-[#F8FAFC]">Trở thành Giảng viên</a>
                            @endif
                            <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-sm text-[#1F2937] hover:bg-[#F8FAFC]">Hồ sơ</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-[#E5E7EB] mt-2 pt-2">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-[#EF4444] hover:bg-[#F8FAFC]">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="ui-btn-ghost hidden sm:inline-flex">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="ui-btn-primary">Đăng ký</a>
                @endauth

                <button type="button" class="ui-btn-ghost lg:hidden" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-cloak class="border-t border-[#E5E7EB] py-4 lg:hidden">
            <form action="{{ route('courses.index') }}" method="GET" class="mb-4">
                <input type="search" name="search" placeholder="Tìm khóa học..." class="ui-input">
            </form>
            <nav class="flex flex-col gap-1 text-sm font-medium">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-[#F8FAFC]">Trang chủ</a>
                <a href="{{ route('courses.index') }}" class="rounded-lg px-3 py-2 hover:bg-[#F8FAFC]">Khóa học</a>
                <a href="{{ route('home') }}#categories" class="rounded-lg px-3 py-2 hover:bg-[#F8FAFC]">Danh mục</a>
                <a href="{{ route('home') }}#instructors" class="rounded-lg px-3 py-2 hover:bg-[#F8FAFC]">Giảng viên</a>
            </nav>
        </div>
    </div>
</header>
