<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Website học online FEA</title>
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-slate-900 font-sans min-h-screen flex flex-col items-center justify-center relative">
        <div class="relative text-center max-w-xl px-6">
            <span class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-100 text-[#0056D2] text-xs font-semibold px-4 py-1.5 rounded-full mb-6 uppercase tracking-wider">
                Website học online FEA
            </span>
            
            <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight tracking-tight mb-4 text-slate-950">
                Chào mừng bạn đến với Website học online FEA
            </h1>
            
            <p class="text-slate-600 text-base sm:text-lg mb-8 leading-relaxed">
                Hệ thống quản lý học tập thông minh & hỗ trợ thực hiện đồ án tốt nghiệp trực quan, hiện đại.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('home') }}" class="bg-[#0056D2] hover:bg-[#0046B8] text-white font-semibold px-7 py-3 rounded-xl transition">
                    Vào trang chủ
                </a>
                @guest
                    <a href="{{ route('login') }}" class="bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-semibold px-7 py-3 rounded-xl transition">
                        Đăng nhập
                    </a>
                @else
                    <a href="{{ auth()->user()->dashboardUrl() }}" class="bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-semibold px-7 py-3 rounded-xl transition">
                        Bảng điều khiển
                    </a>
                @endguest
            </div>
        </div>

        <footer class="absolute bottom-6 text-slate-500 text-xs tracking-wider">
            &copy; {{ date('Y') }} Website học online FEA. All rights reserved.
        </footer>
    </body>
</html>
