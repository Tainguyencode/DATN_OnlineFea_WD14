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
<body class="bg-[#f3f6fb] text-slate-900 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden lg:flex lg:flex-col w-60 bg-[#0f172a] text-white fixed inset-y-0 z-30 shadow-[12px_0_32px_rgba(15,23,42,0.12)]">
            <div class="h-16 px-5 border-b border-white/5 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $c['gradient'] }} flex shrink-0 items-center justify-center font-bold text-sm shadow-lg shadow-black/20">EP</div>
                    <div class="min-w-0">
                        <div class="font-semibold text-base leading-tight truncate">EduPlatform</div>
                        <div class="text-xs text-slate-400">{{ $roleLabel }}</div>
                    </div>
                </a>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
                @foreach($menu as $item)
                    @php
                        $activePatterns = $item['active'] ?? [$item['route'], $item['route'].'.*'];
                        $active = collect((array) $activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
                    @endphp
                    <a href="{{ route($item['route']) }}"
                       class="group flex items-center gap-3 px-3.5 py-2.5 rounded-[11px] text-sm font-medium transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30
                              {{ $active ? 'bg-white/10 text-white shadow-[inset_0_0_0_1px_rgba(255,255,255,0.08)]' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center text-slate-400 transition-colors duration-200 group-hover:text-white {{ $active ? 'text-white' : '' }}">
                            {!! $item['icon'] !!}
                        </span>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="p-3 border-t border-white/5">
                <div class="flex items-center gap-3 rounded-xl bg-white/[0.04] px-3 py-2 mb-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br {{ $c['gradient'] }} flex shrink-0 items-center justify-center text-xs font-bold shadow-sm shadow-black/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex min-h-9 items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-300 transition-colors duration-200 hover:bg-white/5 hover:text-red-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/25">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

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
                </div>
            </header>

            @if(session('success'))
                <div class="mx-4 sm:mx-6 xl:mx-7 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mx-4 sm:mx-6 xl:mx-7 mt-4 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl">{{ session('error') }}</div>
            @endif

            <main class="min-h-[calc(100vh-4rem)] p-4 sm:p-6 xl:p-7">
                {{ $slot }}
            </main>
        </div>
    </div>

    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur border-t border-slate-200/70 shadow-[0_-8px_24px_rgba(15,23,42,0.08)] z-30 flex justify-around py-2">
        @foreach(array_slice($menu, 0, 4) as $item)
            @php
                $activePatterns = $item['active'] ?? [$item['route'], $item['route'].'.*'];
                $active = collect((array) $activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
            @endphp
            <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs {{ $active ? $c['text'] : 'text-slate-400' }}">
                <span class="w-5 h-5">{!! $item['icon'] !!}</span>
                <span class="truncate max-w-[60px]">{{ Str::before($item['label'], ' ') }}</span>
            </a>
        @endforeach
    </nav>
    <div class="h-16 lg:hidden"></div>
</body>
</html>
