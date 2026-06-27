<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Welcome to Fea</title>
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-900 text-white font-sans min-h-screen flex flex-col items-center justify-center relative overflow-hidden">
        <!-- Glowing background blobs -->
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-indigo-600/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl"></div>

        <div class="relative text-center max-w-xl px-6">
            <span class="inline-flex items-center gap-1.5 bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 text-xs font-semibold px-4 py-1.5 rounded-full mb-6 uppercase tracking-wider">
                ⚡ Fea LMS Platform
            </span>
            
            <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-200 to-slate-400">
                Chào mừng bạn đến với Fea
            </h1>
            
            <p class="text-slate-300 text-base sm:text-lg mb-8 font-light leading-relaxed">
                Hệ thống quản lý học tập thông minh & hỗ trợ thực hiện đồ án tốt nghiệp trực quan, hiện đại.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('home') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-7 py-3 rounded-xl transition shadow-lg shadow-indigo-600/35 hover:scale-[1.02]">
                    Vào trang chủ
                </a>
                @guest
                    <a href="{{ route('login') }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-semibold px-7 py-3 rounded-xl transition hover:scale-[1.02]">
                        Đăng nhập
                    </a>
                @else
                    <a href="{{ auth()->user()->dashboardUrl() }}" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white font-semibold px-7 py-3 rounded-xl transition hover:scale-[1.02]">
                        Bảng điều khiển
                    </a>
                @endguest
            </div>
        </div>

        <footer class="absolute bottom-6 text-slate-500 text-xs tracking-wider">
            &copy; {{ date('Y') }} Fea Platform. All rights reserved.
        </footer>
    </body>
</html>
