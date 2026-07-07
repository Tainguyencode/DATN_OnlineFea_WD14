<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Website học online FEA')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ mobileMenu: false }" class="flex min-h-screen flex-col bg-white font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-200">
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="ui-container">
            <div class="flex h-16 items-center gap-4">
                <button type="button" x-on:click="mobileMenu = true" class="inline-flex rounded-md p-2 text-slate-900 transition duration-200 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-800 lg:hidden" aria-label="Mở menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>

                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2 text-lg font-extrabold text-slate-900 dark:text-white">
                    <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-10 w-auto object-contain">
                    <span class="hidden sm:inline">FEA Learning</span>
                </a>

                <nav class="hidden items-center gap-5 text-sm font-semibold text-slate-700 dark:text-slate-300 lg:flex">
                    <a href="{{ route('home') }}#categories" class="transition duration-200 hover:text-[#0056D2] dark:hover:text-blue-300">Khám phá</a>
                    <a href="{{ route('courses.index') }}" class="transition duration-200 hover:text-[#0056D2] dark:hover:text-blue-300">Khóa học</a>
                    <a href="{{ route('home') }}#paths" class="transition duration-200 hover:text-[#0056D2] dark:hover:text-blue-300">Lộ trình</a>
                    <a href="{{ route('home') }}#instructors" class="transition duration-200 hover:text-[#0056D2] dark:hover:text-blue-300">Giảng viên</a>
                    <a href="{{ route('home') }}#business" class="transition duration-200 hover:text-[#0056D2] dark:hover:text-blue-300">Doanh nghiệp</a>
                    <a href="{{ route('home') }}#faq" class="transition duration-200 hover:text-[#0056D2] dark:hover:text-blue-300">FAQ</a>
                </nav>

                <form method="GET" action="{{ route('home') }}" class="hidden min-w-0 flex-1 items-center lg:flex">
                    <label class="relative w-full">
                        <span class="sr-only">Tìm kiếm khóa học</span>
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm khóa học, kỹ năng hoặc giảng viên" class="h-11 w-full rounded-full border border-slate-300 bg-white pl-12 pr-4 text-sm text-slate-900 outline-none transition duration-200 focus:border-[#0056D2] focus:ring-2 focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </label>
                </form>

                <div class="ml-auto flex shrink-0 items-center gap-2">
                    <!-- Nút chuyển chế độ Sáng/Tối -->
                    <button onclick="toggleTheme()" class="rounded-lg p-2 text-slate-600 transition duration-200 hover:bg-slate-50 hover:text-[#0056D2] dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300 cursor-pointer" aria-label="Đổi giao diện">
                        <svg class="hidden dark:block h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                        <svg class="block dark:hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                    @auth
                        <x-notifications.bell
                            :recent-notifications="$recentNotifications ?? collect()"
                            :unread-count="$unreadNotificationCount ?? 0"
                        />
                        @if(Auth::user()->isStudent())
                            <a href="{{ route('student.cart') }}" class="hidden rounded-lg p-2 text-slate-600 transition duration-200 hover:bg-slate-50 hover:text-[#0056D2] dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-300 sm:inline-flex" aria-label="Giỏ hàng">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 5h13M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm8 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                            </a>
                        @endif
                        <a href="{{ auth()->user()->dashboardUrl() }}" class="hidden text-sm font-semibold text-[#0056D2] transition duration-200 hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200 sm:inline-flex">Dashboard</a>
                        <a href="{{ auth()->user()->dashboardUrl() }}" class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-slate-300 bg-slate-50 text-sm font-bold text-slate-900 dark:border-slate-700 dark:bg-slate-800 dark:text-white" aria-label="Tài khoản">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 sm:inline-flex">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-[#0056D2] px-4 py-2 text-sm font-medium text-white transition duration-200 hover:bg-[#0046B8]">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-800 lg:hidden">
            <form method="GET" action="{{ route('home') }}">
                <label class="relative block">
                    <span class="sr-only">Tìm kiếm khóa học</span>
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm khóa học" class="h-11 w-full rounded-full border border-slate-300 bg-white pl-12 pr-4 text-sm text-slate-900 outline-none focus:border-[#0056D2] focus:ring-2 focus:ring-[#0056D2] dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                </label>
            </form>
        </div>
    </header>

    <div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div class="absolute inset-0 bg-black/40" x-on:click="mobileMenu = false"></div>
        <aside class="absolute inset-y-0 left-0 flex w-80 max-w-[85vw] flex-col bg-white shadow-md dark:bg-slate-900" x-transition>
            <div class="flex h-16 items-center justify-between border-b border-slate-200 px-5 dark:border-slate-800">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-extrabold text-slate-900 dark:text-white">
                    <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-9 w-auto">
                    FEA Learning
                </a>
                <button type="button" x-on:click="mobileMenu = false" class="rounded-md p-2 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Đóng menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="flex flex-col gap-1 p-5 text-base font-semibold text-slate-700 dark:text-slate-300">
                <a href="{{ route('home') }}#categories" class="rounded-lg px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800">Khám phá</a>
                <a href="{{ route('courses.index') }}" class="rounded-lg px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800">Khóa học</a>
                <a href="{{ route('home') }}#categories" class="rounded-lg px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800">Danh mục</a>
                <a href="{{ route('home') }}#paths" class="rounded-lg px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800">Lộ trình</a>
                <a href="{{ route('home') }}#instructors" class="rounded-lg px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800">Giảng viên</a>
                <a href="{{ route('home') }}#business" class="rounded-lg px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800">Doanh nghiệp</a>
                <a href="{{ route('home') }}#faq" class="rounded-lg px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800">FAQ</a>
            </nav>
            <div class="mt-auto border-t border-slate-200 p-5 dark:border-slate-800">
                @auth
                    <a href="{{ auth()->user()->dashboardUrl() }}" class="ui-button-primary w-full">Vào Dashboard</a>
                @else
                    <div class="grid gap-3">
                        <a href="{{ route('login') }}" class="ui-button-secondary w-full">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="ui-button-primary w-full">Đăng ký</a>
                    </div>
                @endauth
            </div>
        </aside>
    </div>

    @if(session('success'))
        <div class="ui-alert-success mx-auto mt-4 w-[calc(100%-2rem)] max-w-7xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="ui-alert-error mx-auto mt-4 w-[calc(100%-2rem)] max-w-7xl">
            {{ session('error') }}
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="mt-auto border-t border-slate-800 bg-slate-900 py-12 text-sm text-white">
        <div class="ui-container">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="mb-4 flex items-center gap-3 text-lg font-extrabold">
                        <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-10 w-auto object-contain">
                        FEA Learning
                    </div>
                    <p class="leading-6 text-slate-400">Nền tảng học trực tuyến giúp học viên, giảng viên và nhà quản trị vận hành khóa học chuyên nghiệp.</p>
                </div>
                <div>
                    <h4 class="mb-4 font-bold">Giới thiệu</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li><a href="{{ route('courses.index') }}" class="hover:text-white">Khóa học</a></li>
                        <li><a href="{{ route('home') }}#categories" class="hover:text-white">Danh mục</a></li>
                        <li><a href="{{ route('home') }}#paths" class="hover:text-white">Lộ trình học</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-bold">Hỗ trợ</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li>Email: support@fea-lms.vn</li>
                        <li>Hotline: 1900 88xx</li>
                        <li><a href="{{ route('home') }}#faq" class="hover:text-white">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-bold">Điều khoản</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li>Chính sách bảo mật</li>
                        <li>Điều khoản sử dụng</li>
                        <li>Mạng xã hội: Facebook, YouTube, LinkedIn</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-800 pt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Website học online FEA. All rights reserved.
            </div>
        </div>
    </footer>

    <button onclick="toggleChat()" class="fixed bottom-6 right-6 z-40 rounded-full border border-slate-200 bg-white p-4 text-slate-900 shadow-md transition duration-200 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-white dark:hover:bg-slate-800" aria-label="Open AI Assistant">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-5l-5 5v-5Z" />
        </svg>
    </button>

    <div id="ai-chat-drawer" class="fixed right-0 top-0 z-50 flex h-screen w-full translate-x-full transform flex-col border-l border-slate-200 bg-white shadow-md transition-transform duration-200 ease-in-out dark:border-slate-800 dark:bg-slate-900 sm:w-[440px]">
        <div class="flex items-center justify-between border-b border-slate-200 p-4 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#0056D2] text-sm font-bold text-white">AI</div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Trợ lý học tập FEA</h3>
                    <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Đang hoạt động</span>
                </div>
            </div>
            <button onclick="toggleChat()" class="rounded-lg p-2 text-slate-500 transition duration-200 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="chat-messages" class="chat-scroll flex-1 overflow-y-auto p-4">
            <div class="mb-4 flex justify-start">
                <div class="max-w-[85%] rounded-lg rounded-tl-none bg-slate-100 px-4 py-2.5 text-sm leading-relaxed text-slate-800 dark:bg-slate-800 dark:text-slate-200">
                    Xin chào! Mình là FEA AI Assistant.
                    <br><br>
                    Mình ở đây để tư vấn lộ trình học tập, cung cấp tài liệu nghiên cứu và hướng dẫn bạn hoàn thành đồ án tốt nghiệp.
                </div>
            </div>
        </div>

        <div class="flex flex-wrap justify-center gap-2 border-t border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950">
            <button onclick="sendPresetMessage('Lộ trình học tập')" class="rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition duration-200 hover:border-[#0056D2] dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Lộ trình học</button>
            <button onclick="sendPresetMessage('Đồ án tốt nghiệp')" class="rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition duration-200 hover:border-[#0056D2] dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Làm đồ án</button>
            <button onclick="sendPresetMessage('Quyền của giảng viên')" class="rounded-full border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 transition duration-200 hover:border-[#0056D2] dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Giảng viên</button>
        </div>

        <div class="flex items-center gap-2 border-t border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
            <input type="text" id="chat-input" placeholder="Hỏi AI Trợ lý tại đây..." onkeydown="if(event.key === 'Enter') sendChatMessage()"
                   class="ui-input flex-1">
            <button onclick="sendChatMessage()" class="ui-button-primary h-[50px] px-4">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14 5 7 7m0 0-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>
</body>
</html>
