@props(['studentDashboard' => false])

@php
    $user = auth()->user();
    $studentCartCount = $studentCartCount ?? 0;

    $exploreActive = request()->routeIs('courses.*')
        || request()->routeIs('learning-paths.*')
        || request()->routeIs('leaderboard')
        || request()->routeIs('instructors.*');
    $learningActive = $user?->isStudent() && (
        request()->routeIs('my-courses')
        || request()->routeIs('student.courses')
        || request()->routeIs('student.recently-viewed')
        || request()->routeIs('student.recently-viewed.*')
        || request()->routeIs('student.lesson-notes.*')
        || request()->routeIs('student.reviews.*')
        || request()->routeIs('student.certificates')
    );
    $supportActive = $user && (
        request()->routeIs('study-groups.*')
        || request()->routeIs('student.study-groups.*')
        || request()->routeIs('support.tickets.*')
        || request()->routeIs('student.vouchers.*')
    );
    $favoriteActive = $user?->isStudent() && (
        request()->routeIs('favorites.*')
        || request()->routeIs('student.wishlist*')
    );
    $accountActive = $user && (
        request()->routeIs('student.dashboard')
        || request()->routeIs('student.orders.*')
        || request()->routeIs('student.profile*')
        || request()->routeIs('instructor.dashboard')
        || request()->routeIs('instructor.profile*')
        || request()->routeIs('admin.dashboard')
        || request()->routeIs('admin.profile*')
    );

    $navItemClass = 'inline-flex h-full items-center gap-1.5 border-b-2 border-transparent px-1 text-sm font-semibold transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-4 dark:focus-visible:ring-offset-slate-900';
    $dropdownItemClass = 'flex min-h-10 items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-150 hover:bg-blue-50 hover:text-[#0056D2] focus-visible:bg-blue-50 focus-visible:text-[#0056D2] focus-visible:outline-none dark:text-slate-200 dark:hover:bg-slate-800 dark:hover:text-blue-300 dark:focus-visible:bg-slate-800 dark:focus-visible:text-blue-300';
    $headerContainerClass = $studentDashboard ? 'w-full px-3 sm:px-4 lg:px-5 xl:px-6' : 'ui-container';
    $headerRowClass = $studentDashboard ? 'flex min-h-16 items-center gap-2 lg:gap-3' : 'flex min-h-20 items-center gap-2 sm:gap-3 lg:gap-4';
    $headerLogoClass = $studentDashboard ? 'h-10 w-auto object-contain sm:h-11' : 'h-14 w-auto object-contain sm:h-16';
    $headerNavClass = $studentDashboard ? 'hidden h-16 items-center gap-1 text-slate-700 xl:flex dark:text-slate-300' : 'hidden h-20 items-center gap-2 text-slate-700 lg:flex dark:text-slate-300';
    $headerSearchClass = $studentDashboard ? 'hidden min-w-0 max-w-md flex-1 items-center md:flex' : 'hidden min-w-0 flex-1 items-center lg:flex';
@endphp

<header
    data-public-header
    @if($studentDashboard) data-student-dashboard-header @endif
    class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
    x-data="publicHeader()"
    x-on:keydown.escape.window="closeMenus(); mobileOpen = false"
    x-on:click.outside="closeMenus()"
>
    <div class="{{ $headerContainerClass }}">
        <div class="{{ $headerRowClass }}">
            <button
                type="button"
                @if($studentDashboard)
                    x-on:click="$dispatch('toggle-student-sidebar')"
                    :aria-controls="window.matchMedia('(min-width: 1024px)').matches ? 'student-desktop-sidebar' : 'student-mobile-sidebar'"
                    :aria-expanded="(window.matchMedia('(min-width: 1024px)').matches ? studentSidebarDesktopOpen : studentSidebarOpen).toString()"
                @else
                    x-on:click="mobileOpen = true"
                @endif
                class="inline-flex shrink-0 cursor-pointer rounded-lg p-2 text-slate-900 transition-colors duration-200 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-white dark:hover:bg-slate-800 {{ $studentDashboard ? '' : 'lg:hidden' }}"
                aria-label="{{ $studentDashboard ? 'Ẩn/hiện menu học viên' : 'Mở menu' }}"
            >
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>

            <a href="{{ route('home') }}" class="flex shrink-0 items-center text-lg font-extrabold text-slate-900 dark:text-white" aria-label="FEA Learning - Trang chủ">
                <img src="{{ asset('images/fea-logo.png') }}" alt="FEA Learning" class="{{ $headerLogoClass }}">
            </a>

            <nav class="{{ $headerNavClass }}" aria-label="Điều hướng chính">
                <a href="{{ route('home') }}" class="{{ $navItemClass }} {{ request()->routeIs('home') ? 'border-[#0056D2] text-[#0056D2] dark:border-blue-400 dark:text-blue-300' : 'hover:border-blue-200 hover:text-[#0056D2] dark:hover:border-slate-600 dark:hover:text-blue-300' }}">Trang chủ</a>

                <div class="public-nav-group relative flex h-full items-center" :class="isOpen('explore') ? 'public-nav-group--open' : ''" x-on:mouseenter="activate('explore')" x-on:mouseleave="closeMenus()">
                    <button
                        type="button"
                        class="{{ $navItemClass }} cursor-pointer {{ $exploreActive ? 'border-[#0056D2] text-[#0056D2] dark:border-blue-400 dark:text-blue-300' : 'hover:border-blue-200 hover:text-[#0056D2] dark:hover:border-slate-600 dark:hover:text-blue-300' }}"
                        x-on:click="toggleMenu('explore')"
                        x-on:mouseenter="activate('explore')"
                        :aria-expanded="isOpen('explore') ? 'true' : 'false'"
                        aria-haspopup="true"
                    >
                        Khám phá
                        <svg class="h-4 w-4 transition-transform duration-200" :class="isOpen('explore') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div
                        id="public-nav-explore"
                        x-show="isOpen('explore')"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        x-on:mouseenter="activate('explore')"
                        class="public-nav-dropdown absolute left-0 top-full z-50 w-56 origin-top rounded-xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900"
                        :class="isOpen('explore') ? 'visible pointer-events-auto translate-y-0 opacity-100' : 'invisible pointer-events-none -translate-y-2 opacity-0'"
                        role="menu"
                    >
                        <a href="{{ route('courses.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('courses.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                            <span>Khóa học</span>
                        </a>
                        <a href="{{ route('learning-paths.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('learning-paths.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 0 1 3 16.382V5.618a1 1 0 0 1 1.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0 0 21 18.382V7.618a1 1 0 0 0-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <span>Lộ trình</span>
                        </a>
                        <a href="{{ route('leaderboard') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('leaderboard') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4m-7-7h14M5 10h14M7 3h10a2 2 0 0 1 2 2v5H5V5a2 2 0 0 1 2-2Z"/></svg>
                            <span>Xếp hạng</span>
                        </a>
                        <a href="{{ route('instructors.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('instructors.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM4 21a8 8 0 0 1 16 0M19 8v4m2-2h-4"/></svg>
                            <span>Giảng viên</span>
                        </a>
                    </div>
                </div>

                @auth
                    @if($user->isStudent())
                        <div class="public-nav-group relative flex h-full items-center" :class="isOpen('learning') ? 'public-nav-group--open' : ''" x-on:mouseenter="activate('learning')" x-on:mouseleave="closeMenus()">
                            <button
                                type="button"
                                class="{{ $navItemClass }} cursor-pointer {{ $learningActive ? 'border-[#0056D2] text-[#0056D2] dark:border-blue-400 dark:text-blue-300' : 'hover:border-blue-200 hover:text-[#0056D2] dark:hover:border-slate-600 dark:hover:text-blue-300' }}"
                                x-on:click="toggleMenu('learning')"
                                x-on:mouseenter="activate('learning')"
                                :aria-expanded="isOpen('learning') ? 'true' : 'false'"
                                aria-haspopup="true"
                            >
                                Học tập
                                <svg class="h-4 w-4 transition-transform duration-200" :class="isOpen('learning') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div
                                id="public-nav-learning"
                                x-show="isOpen('learning')"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-2"
                                x-on:mouseenter="activate('learning')"
                                class="public-nav-dropdown absolute left-0 top-full z-50 w-60 origin-top rounded-xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900"
                                :class="isOpen('learning') ? 'visible pointer-events-auto translate-y-0 opacity-100' : 'invisible pointer-events-none -translate-y-2 opacity-0'"
                                role="menu"
                            >
                                <a href="{{ route('student.courses') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.courses') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                                    <span>Khóa học của tôi</span>
                                </a>
                                <a href="{{ route('student.recently-viewed.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.recently-viewed.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Đã xem gần đây</a>
                                <a href="{{ route('student.lesson-notes.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.lesson-notes.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Ghi chú học tập</a>
                                <a href="{{ route('student.reviews.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.reviews.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Đánh giá của tôi</a>
                                <a href="{{ route('student.certificates') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.certificates') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Chứng chỉ</a>
                            </div>
                        </div>
                    @endif

                    <div class="public-nav-group relative flex h-full items-center" :class="isOpen('support') ? 'public-nav-group--open' : ''" x-on:mouseenter="activate('support')" x-on:mouseleave="closeMenus()">
                        <button
                            type="button"
                            class="{{ $navItemClass }} cursor-pointer {{ $supportActive ? 'border-[#0056D2] text-[#0056D2] dark:border-blue-400 dark:text-blue-300' : 'hover:border-blue-200 hover:text-[#0056D2] dark:hover:border-slate-600 dark:hover:text-blue-300' }}"
                            x-on:click="toggleMenu('support')"
                            x-on:mouseenter="activate('support')"
                            :aria-expanded="isOpen('support') ? 'true' : 'false'"
                            aria-haspopup="true"
                        >
                            Hỗ trợ
                            @if(($unreadStudyGroupCount ?? 0) > 0)
                                <span class="inline-flex min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold leading-4 text-white">{{ $unreadStudyGroupCount > 99 ? '99+' : $unreadStudyGroupCount }}</span>
                            @endif
                            <svg class="h-4 w-4 transition-transform duration-200" :class="isOpen('support') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div
                            id="public-nav-support"
                            x-show="isOpen('support')"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            x-on:mouseenter="activate('support')"
                            class="public-nav-dropdown absolute left-0 top-full z-50 w-56 origin-top rounded-xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900"
                            :class="isOpen('support') ? 'visible pointer-events-auto translate-y-0 opacity-100' : 'invisible pointer-events-none -translate-y-2 opacity-0'"
                            role="menu"
                        >
                            <a href="{{ route('study-groups.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('study-groups.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Nhóm học tập</a>
                            @if($user->isStudent() || $user->isInstructor())
                                <a href="{{ route('support.tickets.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('support.tickets.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Ticket hỗ trợ</a>
                            @endif
                            @if($user->isStudent())
                                <a href="{{ route('student.vouchers.index') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.vouchers.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Voucher của tôi</a>
                            @endif
                        </div>
                    </div>
                @endauth
            </nav>

            <form method="GET" action="{{ route('home') }}" class="{{ $headerSearchClass }}">
                <label class="relative block w-full">
                    <span class="sr-only">Tìm kiếm khóa học</span>
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm khóa học, kỹ năng hoặc giảng viên" class="h-11 w-full rounded-full border border-slate-300 bg-white pl-12 pr-4 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-[#0056D2] focus:ring-2 focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </label>
            </form>

            <div class="ml-auto flex shrink-0 items-center gap-0.5 sm:gap-1">
                <button type="button" data-theme-toggle onclick="toggleTheme()" class="{{ $studentDashboard ? 'hidden sm:inline-flex' : 'inline-flex' }} cursor-pointer rounded-lg p-2 text-slate-600 transition-colors duration-200 hover:bg-slate-50 hover:text-[#0056D2] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300" aria-label="Đổi giao diện" aria-pressed="false">
                    <svg class="hidden h-5 w-5 dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707m0-12.728.707.707m12.728 12.728-.707-.707M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"/></svg>
                    <svg class="block h-5 w-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 0 1 8.646 3.646 9.003 9.003 0 0 0 12 21a9.003 9.003 0 0 0 8.354-5.646Z"/></svg>
                </button>

                @auth
                    <div class="{{ $studentDashboard ? 'hidden sm:block' : 'contents' }}">
                        <x-notifications.bell
                            :recent-notifications="$recentNotifications ?? collect()"
                            :unread-count="$unreadNotificationCount ?? 0"
                        />
                    </div>

                    @if($user->isStudent())
                        <a href="{{ route('favorites.index') }}" data-favorite-count="{{ $favoriteCourseCount ?? 0 }}" class="relative {{ $studentDashboard ? 'hidden md:inline-flex' : 'inline-flex' }} cursor-pointer rounded-lg p-2 text-slate-600 transition-colors duration-200 hover:bg-slate-50 hover:text-[#0056D2] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300 {{ $favoriteActive ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" aria-label="Khóa học yêu thích" title="Khóa học yêu thích">
                            <svg class="h-5 w-5" fill="{{ ($favoriteCourseCount ?? 0) > 0 ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/></svg>
                            @if(($favoriteCourseCount ?? 0) > 0)
                                <span data-favorite-badge class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold leading-none text-white">{{ $favoriteCourseCount > 99 ? '99+' : $favoriteCourseCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('student.cart') }}" class="relative inline-flex cursor-pointer rounded-lg p-2 text-slate-600 transition-colors duration-200 hover:bg-slate-50 hover:text-[#0056D2] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300 {{ request()->routeIs('student.cart*') || request()->routeIs('student.checkout.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" aria-label="Giỏ hàng" title="Giỏ hàng">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 5h13M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm8 0a1 1 0 0 0-1-2 1 1 0 0 0 0 2Z"/></svg>
                            @if($studentCartCount > 0)
                                <span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-500 px-1 text-[9px] font-bold leading-none text-white">{{ $studentCartCount > 99 ? '99+' : $studentCartCount }}</span>
                            @endif
                        </a>
                    @endif

                    <div class="public-nav-group relative hidden h-full items-center lg:flex" :class="isOpen('account') ? 'public-nav-group--open' : ''" x-on:mouseenter="activate('account')" x-on:mouseleave="closeMenus()">
                        <button
                            type="button"
                            x-on:click="toggleMenu('account')"
                            x-on:mouseenter="activate('account')"
                            class="inline-flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-semibold text-[#0056D2] transition-colors duration-200 hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-blue-300 dark:hover:bg-slate-800 {{ $accountActive ? 'bg-blue-50 dark:bg-slate-800' : '' }}"
                            :aria-expanded="isOpen('account') ? 'true' : 'false'"
                            aria-haspopup="true"
                            aria-label="Mở menu tài khoản"
                        >
                            <span class="{{ $studentDashboard ? 'hidden 2xl:inline' : 'hidden xl:inline' }}">Dashboard</span>
                            <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-slate-300 bg-slate-50 text-sm font-bold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                @if($user->avatar)
                                    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </span>
                            <svg class="{{ $studentDashboard ? 'hidden 2xl:block' : 'hidden xl:block' }} h-4 w-4 text-slate-400 transition-transform duration-200" :class="isOpen('account') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div
                            id="public-nav-account"
                            x-show="isOpen('account')"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            x-on:mouseenter="activate('account')"
                            class="public-nav-dropdown absolute right-0 top-full z-50 w-56 origin-top-right rounded-xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900"
                            :class="isOpen('account') ? 'visible pointer-events-auto translate-y-0 opacity-100' : 'invisible pointer-events-none -translate-y-2 opacity-0'"
                            role="menu"
                        >
                            @if($user->isStudent())
                                <a href="{{ route('student.dashboard') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.dashboard') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Tổng quan</a>
                                <a href="{{ route('student.orders') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.orders.*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Đơn hàng</a>
                                <a href="{{ route('student.profile') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }} {{ request()->routeIs('student.profile*') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}" role="menuitem">Hồ sơ</a>
                            @elseif($user->isInstructor())
                                <a href="{{ $user->dashboardUrl() }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }}" role="menuitem">Dashboard</a>
                                <a href="{{ route('instructor.profile') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }}" role="menuitem">Hồ sơ</a>
                            @else
                                <a href="{{ $user->dashboardUrl() }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }}" role="menuitem">Dashboard</a>
                                <a href="{{ route('admin.profile') }}" x-on:click="closeMenus()" class="{{ $dropdownItemClass }}" role="menuitem">Hồ sơ</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-slate-100 pt-2 dark:border-slate-800">
                                @csrf
                                <button type="submit" class="flex min-h-10 w-full cursor-pointer items-center rounded-lg px-3 py-2 text-left text-sm font-semibold text-rose-600 transition-colors duration-150 hover:bg-rose-50 focus-visible:bg-rose-50 focus-visible:outline-none dark:text-rose-400 dark:hover:bg-rose-950/30 dark:focus-visible:bg-rose-950/30">Đăng xuất</button>
                            </form>
                        </div>
                    </div>

                    <button type="button" x-on:click="mobileOpen = true" class="inline-flex h-9 w-9 cursor-pointer items-center justify-center overflow-hidden rounded-full border border-slate-300 bg-slate-50 text-sm font-bold text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-800 dark:text-white lg:hidden" aria-label="Mở menu tài khoản">
                        @if($user->avatar)
                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </button>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 sm:inline-flex">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="inline-flex rounded-lg bg-[#0056D2] px-3 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-[#0046B8] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] focus-visible:ring-offset-2">Đăng ký</a>
                @endauth
            </div>
        </div>
    </div>

    @unless($studentDashboard)
    <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800 lg:hidden">
        <form method="GET" action="{{ route('home') }}">
            <label class="relative block">
                <span class="sr-only">Tìm kiếm khóa học</span>
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm khóa học" class="h-11 w-full rounded-full border border-slate-300 bg-white pl-12 pr-4 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-[#0056D2] focus:ring-2 focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-950 dark:text-white">
            </label>
        </form>
    </div>
    @endunless

    <div x-show="mobileOpen" x-cloak class="fixed inset-0 z-[60] lg:hidden" role="dialog" aria-modal="true" aria-label="Menu chính">
        <div class="absolute inset-0 bg-slate-950/45" x-on:click="mobileOpen = false"></div>
        <aside
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="absolute inset-y-0 left-0 flex w-80 max-w-[88vw] flex-col overflow-y-auto bg-white shadow-2xl dark:bg-slate-900"
        >
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 px-5 dark:border-slate-800">
                <a href="{{ route('home') }}" x-on:click="mobileOpen = false" class="flex items-center font-extrabold text-slate-900 dark:text-white" aria-label="FEA Learning - Trang chủ">
                    <img src="{{ asset('images/fea-logo.png') }}" alt="FEA Learning" class="h-11 w-auto">
                </a>
                <button type="button" x-on:click="mobileOpen = false" class="cursor-pointer rounded-lg p-2 text-slate-700 transition-colors duration-200 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-200 dark:hover:bg-slate-800" aria-label="Đóng menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex flex-col gap-1 p-5 text-base font-semibold text-slate-700 dark:text-slate-200" aria-label="Menu di động">
                <a href="{{ route('home') }}" x-on:click="mobileOpen = false" class="rounded-lg px-3 py-3 transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:hover:bg-slate-800 {{ request()->routeIs('home') ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : '' }}">Trang chủ</a>

                <div>
                    <button type="button" x-on:click="toggleMenu('mobile-explore')" class="flex w-full cursor-pointer items-center justify-between rounded-lg px-3 py-3 text-left transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:hover:bg-slate-800" :aria-expanded="isOpen('mobile-explore') ? 'true' : 'false'" aria-controls="mobile-explore-menu">
                        <span>Khám phá</span>
                        <svg class="h-5 w-5 transition-transform duration-200" :class="isOpen('mobile-explore') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="mobile-explore-menu" x-show="isOpen('mobile-explore')" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="ml-3 mt-1 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                        <a href="{{ route('courses.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Khóa học</a>
                        <a href="{{ route('learning-paths.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Lộ trình</a>
                        <a href="{{ route('leaderboard') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Xếp hạng</a>
                        <a href="{{ route('instructors.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Giảng viên</a>
                    </div>
                </div>

                @auth
                    @if($user->isStudent())
                        <div>
                            <button type="button" x-on:click="toggleMenu('mobile-learning')" class="flex w-full cursor-pointer items-center justify-between rounded-lg px-3 py-3 text-left transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:hover:bg-slate-800" :aria-expanded="isOpen('mobile-learning') ? 'true' : 'false'" aria-controls="mobile-learning-menu">
                                <span>Học tập</span>
                                <svg class="h-5 w-5 transition-transform duration-200" :class="isOpen('mobile-learning') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <div id="mobile-learning-menu" x-show="isOpen('mobile-learning')" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="ml-3 mt-1 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                                <a href="{{ route('student.courses') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Khóa học của tôi</a>
                                <a href="{{ route('student.recently-viewed.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Đã xem gần đây</a>
                                <a href="{{ route('student.lesson-notes.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Ghi chú học tập</a>
                                <a href="{{ route('student.reviews.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Đánh giá của tôi</a>
                                <a href="{{ route('student.certificates') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Chứng chỉ</a>
                            </div>
                        </div>
                    @endif

                    <div>
                        <button type="button" x-on:click="toggleMenu('mobile-support')" class="flex w-full cursor-pointer items-center justify-between rounded-lg px-3 py-3 text-left transition-colors duration-150 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:hover:bg-slate-800" :aria-expanded="isOpen('mobile-support') ? 'true' : 'false'" aria-controls="mobile-support-menu">
                            <span class="flex items-center gap-2">Hỗ trợ @if(($unreadStudyGroupCount ?? 0) > 0)<span class="inline-flex min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] leading-4 text-white">{{ $unreadStudyGroupCount > 99 ? '99+' : $unreadStudyGroupCount }}</span>@endif</span>
                            <svg class="h-5 w-5 transition-transform duration-200" :class="isOpen('mobile-support') ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div id="mobile-support-menu" x-show="isOpen('mobile-support')" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="ml-3 mt-1 space-y-1 border-l border-slate-200 pl-3 dark:border-slate-700">
                            <a href="{{ route('study-groups.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Nhóm học tập</a>
                            @if($user->isStudent() || $user->isInstructor())
                                <a href="{{ route('support.tickets.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Ticket hỗ trợ</a>
                            @endif
                            @if($user->isStudent())
                                <a href="{{ route('student.vouchers.index') }}" x-on:click="closeMobile()" class="block rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-100 dark:hover:bg-slate-800">Voucher của tôi</a>
                            @endif
                        </div>
                    </div>
                @endauth
            </nav>

            <div class="mt-auto shrink-0 border-t border-slate-200 p-5 dark:border-slate-800">
                @auth
                    <div class="mb-3 flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800/70">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white text-sm font-bold text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            @if($user->avatar)
                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}</span>
                            <span class="block truncate text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</span>
                        </span>
                    </div>
                    @if($user->isStudent())
                        <a href="{{ route('student.dashboard') }}" x-on:click="mobileOpen = false" class="mb-2 flex items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-200 dark:hover:bg-slate-800">Tổng quan</a>
                        <a href="{{ route('student.orders') }}" x-on:click="mobileOpen = false" class="mb-2 flex items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-200 dark:hover:bg-slate-800">Đơn hàng</a>
                        <a href="{{ route('student.profile') }}" x-on:click="mobileOpen = false" class="mb-2 flex items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-200 dark:hover:bg-slate-800">Hồ sơ</a>
                    @else
                        <a href="{{ $user->dashboardUrl() }}" x-on:click="mobileOpen = false" class="mb-2 flex items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] dark:text-slate-200 dark:hover:bg-slate-800">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full cursor-pointer items-center rounded-lg px-3 py-2.5 text-left text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 dark:text-rose-400 dark:hover:bg-rose-950/30">Đăng xuất</button>
                    </form>
                @else
                    <div class="grid gap-3">
                        <a href="{{ route('login') }}" x-on:click="mobileOpen = false" class="ui-button-secondary w-full">Đăng nhập</a>
                        <a href="{{ route('register') }}" x-on:click="mobileOpen = false" class="ui-button-primary w-full">Đăng ký</a>
                    </div>
                @endauth
            </div>
        </aside>
    </div>
</header>
