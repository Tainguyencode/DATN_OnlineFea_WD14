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

    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-200">
    <x-public.header />

    <x-toast-container />

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="mt-auto border-t border-slate-700 bg-slate-800 py-12 text-sm text-white">
        <div class="ui-container">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="mb-4 flex items-center gap-3 text-lg font-extrabold text-white">
                        <div class="flex items-center justify-center rounded-lg bg-white p-1.5 shadow-sm">
                            <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-10 w-auto object-contain">
                        </div>
                        FEA Learning
                    </div>
                    <p class="leading-6 text-slate-300">Nền tảng học trực tuyến giúp học viên, giảng viên và nhà quản trị vận hành khóa học chuyên nghiệp.</p>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-white">Giới thiệu</h4>
                    <ul class="space-y-3 text-slate-300">
                        <li><a href="{{ route('courses.index') }}" class="transition-colors hover:text-white">Khóa học</a></li>
                        <li><a href="{{ route('home') }}#categories" class="transition-colors hover:text-white">Danh mục</a></li>
                        <li><a href="{{ route('learning-paths.index') }}" class="transition-colors hover:text-white">Lộ trình học</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-white">Hỗ trợ</h4>
                    <ul class="space-y-3 text-slate-300">
                        <li>Email: support@fea-lms.vn</li>
                        <li>Hotline: 1900 88xx</li>
                        <li><a href="mailto:support@fea-lms.vn" class="transition-colors hover:text-white">Liên hệ hỗ trợ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="mb-4 font-bold text-white">Điều khoản</h4>
                    <ul class="space-y-3 text-slate-300">
                        <li>Chính sách bảo mật</li>
                        <li>Điều khoản sử dụng</li>
                        <li>Mạng xã hội: Facebook, YouTube, LinkedIn</li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-700 pt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Website học online FEA. All rights reserved.
            </div>
        </div>
    </footer>

    @if(request()->routeIs('learning-paths.*'))
        <x-learning-path.floating-ai :learning-path="$learningPath ?? null" />
    @endif

    <x-session-invalidation-monitor />
</body>
</html>
