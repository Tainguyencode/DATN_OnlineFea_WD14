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
<body class="bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-200">
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 z-30 hidden w-64 flex-col text-white lg:flex {{ $c['sidebar'] }}">
            <div class="border-b border-white/10 p-5">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/fea-logo.png') }}" alt="Website học online FEA" class="h-10 w-auto object-contain">
                    <div>
                        <div class="text-base font-bold leading-tight">FEA Learning</div>
                        <div class="text-xs text-white/60">{{ $roleLabel }}</div>
                    </div>
                </a>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                @foreach($menu as $item)
                    @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-semibold transition duration-200
                              {{ $active ? 'bg-white text-slate-900' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                        {!! $item['icon'] !!}
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="mb-3 flex items-center gap-3 px-3 py-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-bold text-slate-900">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold">{{ Auth::user()->name }}</div>
                        <div class="truncate text-xs text-white/55">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-4 py-2 text-sm text-white/70 transition duration-200 hover:bg-white/10 hover:text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 16 4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1"/></svg>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

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
                </div>
            </header>

            @if(session('success'))
                <div class="ui-alert-success mx-4 mt-4 flex items-center gap-2 sm:mx-8">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="ui-alert-error mx-4 mt-4 sm:mx-8">{{ session('error') }}</div>
            @endif

            <main class="p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-30 flex gap-2 overflow-x-auto border-t border-slate-200 bg-white px-3 py-2 dark:border-slate-800 dark:bg-slate-900 lg:hidden">
        @foreach($menu as $item)
            <a href="{{ route($item['route']) }}" class="flex min-w-20 flex-col items-center gap-0.5 rounded-lg px-3 py-1 text-xs {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? $c['text'].' bg-slate-100 dark:bg-slate-800 font-bold' : 'text-slate-500 dark:text-slate-400' }}">
                <span class="h-5 w-5">{!! $item['icon'] !!}</span>
                <span class="truncate max-w-[60px]">{{ Str::before($item['label'], ' ') }}</span>
            </a>
        @endforeach
    </nav>
    <div class="h-16 lg:hidden"></div>
</body>
</html>
