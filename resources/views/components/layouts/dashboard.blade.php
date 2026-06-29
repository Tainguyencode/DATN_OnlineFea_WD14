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
        'indigo' => ['bg' => 'bg-indigo-600', 'hover' => 'hover:bg-indigo-700', 'light' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'ring' => 'ring-indigo-500', 'gradient' => 'from-indigo-600 to-violet-600', 'sidebar' => 'bg-slate-900'],
        'emerald' => ['bg' => 'bg-emerald-600', 'hover' => 'hover:bg-emerald-700', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-500', 'gradient' => 'from-emerald-600 to-teal-600', 'sidebar' => 'bg-slate-900'],
        'rose' => ['bg' => 'bg-rose-600', 'hover' => 'hover:bg-rose-700', 'light' => 'bg-rose-50', 'text' => 'text-rose-500', 'ring' => 'ring-rose-500', 'gradient' => 'from-rose-600 to-orange-600', 'sidebar' => 'bg-slate-950'],
    ];
    $c = $accents[$accent] ?? $accents['indigo'];
@endphp

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - EduPlatform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden lg:flex lg:flex-col w-64 {{ $c['sidebar'] }} text-white fixed inset-y-0 z-30">
            <div class="p-6 border-b border-white/10">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $c['gradient'] }} flex items-center justify-center font-bold text-sm shadow-lg">EP</div>
                    <div>
                        <div class="font-bold text-lg leading-tight">EduPlatform</div>
                        <div class="text-xs text-slate-400">{{ $roleLabel }}</div>
                    </div>
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                @foreach($menu as $item)
                    @php $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all
                              {{ $active ? 'bg-white/15 text-white shadow-sm' : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">
                        {!! $item['icon'] !!}
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 px-3 py-2 mb-3">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $c['gradient'] }} flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-slate-400 hover:text-red-400 transition rounded-lg hover:bg-white/5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 lg:ml-64">
            <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
                <div class="flex items-center justify-between px-4 sm:px-8 h-16">
                    <div>
                        <h1 class="text-lg font-bold text-slate-900">{{ $pageTitle }}</h1>
                        @if($breadcrumb)
                            <p class="text-xs text-slate-500 mt-0.5">{{ $breadcrumb }}</p>
                        @endif
                    </div>
                    <a href="{{ route('home') }}" class="text-sm text-slate-500 {{ $c['text'] }} hover:opacity-80 transition hidden sm:inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Trang chủ
                    </a>
                </div>
            </header>

            @if(session('success'))
                <div class="mx-4 sm:mx-8 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mx-4 sm:mx-8 mt-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div>
            @endif

            <main class="p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-slate-200 z-30 flex justify-around py-2">
        @foreach(array_slice($menu, 0, 4) as $item)
            <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? $c['text'] : 'text-slate-400' }}">
                <span class="w-5 h-5">{!! $item['icon'] !!}</span>
                <span class="truncate max-w-[60px]">{{ Str::before($item['label'], ' ') }}</span>
            </a>
        @endforeach
    </nav>
    <div class="h-16 lg:hidden"></div>
</body>
</html>
