@props([
    'role' => 'student',
    'roleLabel' => 'Học viên',
    'accent' => 'indigo',
    'menu' => [],
    'title' => 'Dashboard',
    'pageTitle' => 'Dashboard',
    'breadcrumb' => null,
])

@php
    $accents = [
        'indigo' => ['text' => 'text-[#0056D2] dark:text-blue-300', 'light' => 'bg-blue-50 dark:bg-blue-950/40', 'sidebar' => 'bg-slate-900'],
        'emerald' => ['text' => 'text-emerald-600 dark:text-emerald-400', 'light' => 'bg-emerald-50 dark:bg-emerald-900/40', 'sidebar' => 'bg-slate-900'],
        'rose' => ['text' => 'text-red-600 dark:text-red-400', 'light' => 'bg-red-50 dark:bg-red-900/40', 'sidebar' => 'bg-slate-900'],
    ];
    $c = $accents[$accent] ?? $accents['indigo'];
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
<<<<<<< HEAD
<body class="bg-[#f3f6fb] text-slate-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden lg:flex lg:flex-col w-60 bg-[#0f172a] text-white fixed inset-y-0 z-30 shadow-[12px_0_32px_rgba(15,23,42,0.12)]">
            <div class="h-16 px-5 border-b border-white/5 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $c['gradient'] }} flex shrink-0 items-center justify-center font-bold text-sm shadow-lg shadow-black/20">EP</div>
                    <div class="min-w-0">
                        <div class="font-semibold text-base leading-tight truncate">EduPlatform</div>
                        <div class="text-xs text-slate-400">{{ $roleLabel }}</div>
=======
<body class="bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-200">
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 z-30 hidden w-64 flex-col text-white lg:flex {{ $c['sidebar'] }}">
            <div class="border-b border-white/10 p-5">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-10 w-auto object-contain">
                    <div>
                        <div class="text-base font-bold leading-tight">FEA Learning</div>
                        <div class="text-xs text-white/60">{{ $roleLabel }}</div>
>>>>>>> origin/TuanTu_Dev
                    </div>
                </a>
            </div>

<<<<<<< HEAD
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
=======
            <nav class="flex-1 space-y-1 overflow-y-auto p-4">
>>>>>>> origin/TuanTu_Dev
                @foreach($menu as $item)
                    @php
                        $activePatterns = $item['active'] ?? [$item['route'], $item['route'].'.*'];
                        $active = collect((array) $activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                    @endphp
                    <a href="{{ route($item['route']) }}"
<<<<<<< HEAD
                       class="group flex items-center gap-3 px-3.5 py-2.5 rounded-[11px] text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30
                              {{ $active ? 'bg-white/10 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,0.08)]' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center text-slate-400 transition-colors duration-200 group-hover:text-white {{ $active ? 'text-white' : '' }}">
                            {!! $item['icon'] !!}
                        </span>
                        <span class="truncate">{{ $item['label'] }}</span>
=======
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition duration-200
                              {{ $active ? 'bg-white text-slate-900' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                        {!! $item['icon'] !!}
                        {{ $item['label'] }}
>>>>>>> origin/TuanTu_Dev
                    </a>
                @endforeach
            </nav>

<<<<<<< HEAD
            <div class="p-3 border-t border-white/5">
                <div class="flex items-center gap-3 rounded-xl bg-white/[0.04] px-3 py-2 mb-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $c['gradient'] }} flex shrink-0 items-center justify-center text-xs font-bold shadow-sm shadow-black/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</div>
=======
            <div class="border-t border-white/10 p-4">
                <div class="mb-3 flex items-center gap-3 px-3 py-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-bold text-slate-900">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold">{{ Auth::user()->name }}</div>
                        <div class="truncate text-xs text-white/55">{{ Auth::user()->email }}</div>
>>>>>>> origin/TuanTu_Dev
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
<<<<<<< HEAD
                    <button type="submit" class="w-full flex min-h-9 items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-300 transition-colors duration-200 hover:bg-white/5 hover:text-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/25">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
=======
                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-4 py-2 text-sm text-white/70 transition duration-200 hover:bg-white/10 hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
>>>>>>> origin/TuanTu_Dev
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

<<<<<<< HEAD
        <div class="flex-1 lg:ml-60">
            <header class="bg-white/95 backdrop-blur border-b border-slate-200/70 shadow-[0_1px_10px_rgba(15,23,42,0.03)] sticky top-0 z-20">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 xl:px-7">
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-lg font-semibold leading-tight text-slate-900 truncate">{{ $pageTitle }}</h1>
                        @if($breadcrumb)
                            <p class="text-xs text-slate-500 mt-1 truncate">{{ $breadcrumb }}</p>
                        @endif
                    </div>
                    <a href="{{ route('home') }}" class="hidden sm:inline-flex h-9 items-center gap-2 rounded-full border border-slate-200/70 bg-white px-3 text-sm font-medium text-slate-600 transition-colors duration-200 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Trang chủ
                    </a>
=======
        <div class="flex-1 lg:ml-64">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex h-16 items-center justify-between px-4 sm:px-8">
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white">{{ $pageTitle }}</h1>
                        @if($breadcrumb)
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ $breadcrumb }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="rounded-lg p-2 text-slate-500 transition duration-200 hover:bg-slate-50 hover:text-[#0056D2] dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-blue-300" aria-label="Thông báo">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>
                        </button>
                        <a href="{{ route('home') }}" class="hidden items-center gap-1 text-sm font-semibold text-[#0056D2] transition duration-200 hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200 sm:inline-flex">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/></svg>
                            Trang chủ
                        </a>
                    </div>
>>>>>>> origin/TuanTu_Dev
                </div>
            </header>

            @if(session('success'))
<<<<<<< HEAD
                <div class="mx-4 sm:mx-6 xl:mx-7 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
=======
                <div class="ui-alert-success mx-4 mt-4 flex items-center gap-2 sm:mx-8">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
>>>>>>> origin/TuanTu_Dev
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
<<<<<<< HEAD
                <div class="mx-4 sm:mx-6 xl:mx-7 mt-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div>
=======
                <div class="ui-alert-error mx-4 mt-4 sm:mx-8">{{ session('error') }}</div>
>>>>>>> origin/TuanTu_Dev
            @endif

            <main class="min-h-[calc(100vh-4rem)] p-4 sm:p-6 xl:p-7">
                {{ $slot }}
            </main>
        </div>
    </div>

<<<<<<< HEAD
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-slate-200/70 shadow-[0_-8px_24px_rgba(15,23,42,0.08)] z-30 flex justify-around py-2">
        @foreach(array_slice($menu, 0, 4) as $item)
            @php
                $activePatterns = $item['active'] ?? [$item['route'], $item['route'].'.*'];
                $active = collect((array) $activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
            @endphp
            <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs {{ $active ? $c['text'] : 'text-slate-400' }}">
                <span class="w-5 h-5">{!! $item['icon'] !!}</span>
=======
    <nav class="fixed inset-x-0 bottom-0 z-30 flex gap-2 overflow-x-auto border-t border-slate-200 bg-white px-3 py-2 dark:border-slate-800 dark:bg-slate-900 lg:hidden">
        @foreach($menu as $item)
            <a href="{{ route($item['route']) }}" class="flex min-w-20 flex-col items-center gap-0.5 rounded-lg px-3 py-1 text-xs {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? $c['text'].' bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400' }}">
                <span class="h-5 w-5">{!! $item['icon'] !!}</span>
>>>>>>> origin/TuanTu_Dev
                <span class="truncate max-w-[60px]">{{ Str::before($item['label'], ' ') }}</span>
            </a>
        @endforeach
    </nav>
    <div class="h-16 lg:hidden"></div>
</body>
</html>
