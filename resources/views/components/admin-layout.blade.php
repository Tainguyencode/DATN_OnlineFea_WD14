@props([
    'title' => 'Admin Dashboard',
    'pageTitle' => 'Admin Dashboard',
    'breadcrumb' => null,
])

@php
    $menu = [
        [
            'route' => 'admin.dashboard',
            'label' => 'Tổng quan',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'
        ],
        [
            'route' => 'admin.users',
            'label' => 'Người dùng',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'
        ],
        [
<<<<<<< HEAD
            'route' => 'admin.courses.index',
            'active' => ['admin.courses.index', 'admin.courses.show', 'admin.courses.students'],
            'label' => 'Khóa học',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>'
=======
            'route' => 'admin.roles.index',
            'label' => 'Vai trò',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 4h10a2 2 0 012 2v14l-7-3-7 3V6a2 2 0 012-2z"/></svg>'
>>>>>>> origin/TuanTu_Dev
        ],
        [
            'route' => 'admin.courses.pending',
            'active' => ['admin.courses.pending', 'admin.courses.review'],
            'label' => 'Duyệt khóa học',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        ],
        [
            'route' => 'admin.revenue',
            'label' => 'Doanh thu',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2"/></svg>'
        ],
        [
            'route' => 'admin.activity-logs',
            'label' => 'Nhật ký',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
        ],
        [
            'route' => 'admin.homepage',
            'label' => 'Trang chủ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
        ],
        [
            'route' => 'admin.profile',
            'label' => 'Hồ sơ',
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - Website học online FEA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f5f7fb] font-sans text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[252px_1fr]">
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-[252px] border-r border-slate-200 bg-white lg:flex lg:flex-col">
            <div class="flex h-16 items-center gap-3 border-b border-slate-100 px-5">
                <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-10 w-auto object-contain">
                <div>
                    <div class="text-sm font-extrabold leading-tight text-slate-900">FEA Admin</div>
                    <div class="text-[11px] font-medium text-slate-400">Learning Analytics</div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-5">
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Main</p>
                <div class="mt-3 space-y-1">
                    @foreach($menu as $item)
                        @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition duration-200
                                  {{ $active ? 'bg-red-50 text-red-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span class="h-5 w-5 shrink-0">{!! $item['icon'] !!}</span>
                            <span>{{ $item['label'] }}</span>
                            @if($active)
                                <span class="ml-auto h-1.5 w-1.5 rounded-full bg-red-500"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </nav>

            <div class="border-t border-slate-100 p-4">
                <div class="mb-3 flex items-center gap-3 rounded-2xl bg-slate-50 p-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-sm font-bold text-red-600">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-bold text-slate-900">{{ Auth::user()->name }}</div>
                        <div class="truncate text-xs text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition duration-200 hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        <div class="lg:col-start-2">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white">
                <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-extrabold text-slate-900">{{ $pageTitle }}</h1>
                        @if($breadcrumb)
                            <p class="mt-0.5 hidden text-xs text-slate-500 sm:block">{{ $breadcrumb }}</p>
                        @endif
                    </div>

                    <div class="hidden min-w-0 max-w-sm flex-1 md:block">
                        <label class="relative block">
                            <span class="sr-only">Tìm kiếm</span>
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                            <input type="search" placeholder="Search..." class="h-10 w-full rounded-full border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm outline-none transition duration-200 focus:border-red-300 focus:bg-white focus:ring-4 focus:ring-red-100">
                        </label>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('home') }}" class="hidden rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50 sm:inline-flex">Trang chủ</a>
                        <button type="button" class="relative rounded-xl border border-slate-200 bg-white p-2 text-slate-500 transition hover:bg-slate-50 hover:text-red-600" aria-label="Thông báo">
                            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red-500"></span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>
                        </button>
                        <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold text-slate-700">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            @if(session('success'))
                <div class="ui-alert-success mx-4 mt-4 sm:mx-6 lg:mx-8">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="ui-alert-error mx-4 mt-4 sm:mx-6 lg:mx-8">{{ session('error') }}</div>
            @endif

            <main class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-40 flex gap-2 overflow-x-auto border-t border-slate-200 bg-white px-3 py-2 lg:hidden">
        @foreach($menu as $item)
            <a href="{{ route($item['route']) }}" class="flex min-w-20 flex-col items-center gap-0.5 rounded-xl px-3 py-1 text-xs {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'bg-red-50 font-bold text-red-600' : 'text-slate-500' }}">
                <span class="h-5 w-5">{!! $item['icon'] !!}</span>
                <span class="max-w-[64px] truncate">{{ Str::before($item['label'], ' ') }}</span>
            </a>
        @endforeach
    </nav>
    <div class="h-16 lg:hidden"></div>
</body>
</html>
