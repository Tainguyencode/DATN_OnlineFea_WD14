<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Fea - Nền Tảng Học Tập Thông Minh')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 dark:bg-[#0a0a0a] text-slate-800 dark:text-slate-200 antialiased min-h-screen flex flex-col transition-colors duration-300">
    
    <!-- Sticky Glass Navbar -->
    <nav class="bg-white/80 dark:bg-[#161615]/80 border-b border-slate-200/80 dark:border-slate-800/80 sticky top-0 z-50 backdrop-blur-md shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-bold text-xl text-indigo-600 dark:text-indigo-400">
                    <span class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-purple-600 text-white rounded-xl flex items-center justify-center font-extrabold shadow-md shadow-indigo-200 dark:shadow-none">F</span>
                    <span class="tracking-tight">Fea</span>
                </a>

                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600 dark:text-slate-400">
                    <a href="{{ route('home') }}#courses" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Khóa học</a>
                    <a href="{{ route('home') }}#categories" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Danh mục</a>
                    <a href="{{ route('home') }}#paths" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Lộ trình</a>
                    <a href="{{ route('home') }}#faq" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">FAQ</a>
                </div>

                <!-- Auth & Action Elements -->
                <div class="flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleTheme()" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition" aria-label="Toggle Theme">
                        <!-- Sun Icon -->
                        <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <!-- Moon Icon -->
                        <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    @auth
                        <a href="{{ auth()->user()->dashboardUrl() }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline transition hidden sm:inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/></svg>
                            Dashboard
                        </a>
                        <span class="hidden sm:inline text-sm text-slate-600 dark:text-slate-400 font-medium">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-slate-600 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 font-semibold transition cursor-pointer">Đăng xuất</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition px-3 py-2">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-200 dark:shadow-none">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="bg-emerald-50 dark:bg-emerald-950/30 border-b border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 text-sm py-3 px-4 text-center">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5 font-bold text-xl text-white mb-4">
                        <span class="w-10 h-10 bg-gradient-to-tr from-indigo-500 to-purple-500 text-white rounded-xl flex items-center justify-center font-extrabold text-sm">F</span>
                        Fea
                    </div>
                    <p class="text-sm leading-relaxed max-w-md">Fea - Nền tảng học tập thông minh trực quan phục vụ quản lý khóa học và đề tài đồ án tốt nghiệp cho giảng viên và sinh viên.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Liên kết</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}#courses" class="hover:text-white transition">Khóa học</a></li>
                        <li><a href="{{ route('home') }}#faq" class="hover:text-white transition">Câu hỏi thường gặp</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Đăng ký thành viên</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Hỗ trợ</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li>Email: support@fea-lms.vn</li>
                        <li>Hotline: 1900 88xx</li>
                        <li>Phòng Lab: Lập trình Thông Minh</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-8 pt-8 text-sm text-center">
                &copy; {{ date('Y') }} Fea. Đồ án tốt nghiệp.
            </div>
        </div>
    </footer>

    <!-- Floating Chatbot Toggle Button -->
    <button onclick="toggleChat()" class="fixed bottom-6 right-6 z-40 bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 rounded-full shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer" aria-label="Open AI Assistant">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </button>

    <!-- AI Chatbot Drawer (Slides from Right) -->
    <div id="ai-chat-drawer" class="fixed top-0 right-0 z-50 h-screen w-full sm:w-[440px] bg-white dark:bg-[#161615] shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out border-l border-slate-200 dark:border-slate-800 flex flex-col">
        <!-- Chat Header -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-indigo-600/10 to-purple-600/10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-purple-600 text-white rounded-full flex items-center justify-center font-bold text-lg shadow-sm">AI</div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-slate-100 text-sm">Trợ lý học tập Fea</h3>
                    <span class="text-xs text-emerald-500 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span>
                        Đang hoạt động
                    </span>
                </div>
            </div>
            <button onclick="toggleChat()" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Chat Messages Container -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 chat-scroll">
            <!-- Assistant Greeting -->
            <div class="flex justify-start mb-4">
                <div class="bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-2xl rounded-tl-none px-4 py-2.5 max-w-[85%] text-sm shadow-sm leading-relaxed">
                    Xin chào! Mình là **Fea AI Assistant** 🤖.
                    <br><br>
                    Mình ở đây để tư vấn lộ trình học tập, cung cấp tài liệu nghiên cứu và hướng dẫn bạn hoàn thành đồ án tốt nghiệp.
                </div>
            </div>
        </div>

        <!-- Preset Prompts / Quick Links -->
        <div class="p-3 bg-slate-50 dark:bg-[#161615] border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-2 justify-center">
            <button onclick="sendPresetMessage('Lộ trình học tập')" class="text-xs bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 transition cursor-pointer font-medium">🎯 Lộ trình học</button>
            <button onclick="sendPresetMessage('Đồ án tốt nghiệp')" class="text-xs bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 transition cursor-pointer font-medium">🎓 Làm đồ án</button>
            <button onclick="sendPresetMessage('Quyền của giảng viên')" class="text-xs bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 hover:border-indigo-400 dark:hover:border-indigo-500 transition cursor-pointer font-medium">👨‍🏫 Giảng viên</button>
        </div>

        <!-- Chat Input Form -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 flex gap-2 items-center bg-white dark:bg-[#161615]">
            <input type="text" id="chat-input" placeholder="Hỏi AI Trợ lý tại đây..." onkeydown="if(event.key === 'Enter') sendChatMessage()"
                   class="flex-1 px-4 py-2.5 text-sm bg-slate-100 dark:bg-slate-800 border border-transparent focus:border-indigo-500 dark:focus:border-indigo-400 rounded-xl outline-none text-slate-900 dark:text-white transition">
            <button onclick="sendChatMessage()" class="bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded-xl shadow-md transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </div>

</body>
</html>
