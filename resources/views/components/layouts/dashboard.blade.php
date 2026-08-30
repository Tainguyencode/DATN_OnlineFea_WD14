@props([
    'role' => 'student',
    'roleLabel' => 'Hoc vien',
    'accent' => 'indigo',
    'menu' => [],
    'title' => 'Dashboard',
    'pageTitle' => 'Dashboard',
    'pageTitleClass' => 'text-base sm:text-lg font-semibold leading-tight text-slate-900 truncate',
    'breadcrumb' => null,
])

@php
    $accents = [
        'indigo' => ['bg' => 'bg-indigo-600', 'hover' => 'hover:bg-indigo-700', 'light' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'ring' => 'ring-indigo-500', 'gradient' => 'from-indigo-600 to-violet-600', 'sidebar' => 'bg-slate-900'],
        'emerald' => ['bg' => 'bg-emerald-600', 'hover' => 'hover:bg-emerald-700', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'ring' => 'ring-emerald-500', 'gradient' => 'from-emerald-600 to-teal-600', 'sidebar' => 'bg-slate-900'],
        'rose' => ['bg' => 'bg-rose-600', 'hover' => 'hover:bg-rose-700', 'light' => 'bg-rose-50', 'text' => 'text-rose-500', 'ring' => 'ring-rose-500', 'gradient' => 'from-rose-600 to-orange-600', 'sidebar' => 'bg-slate-950'],
        'blue' => ['bg' => 'bg-blue-600', 'hover' => 'hover:bg-blue-700', 'light' => 'bg-blue-50', 'text' => 'text-blue-600', 'ring' => 'ring-blue-500', 'gradient' => 'from-blue-500 to-blue-700', 'sidebar' => 'bg-white'],
    ];
    $c = $accents[$accent] ?? $accents['indigo'];
    $isMenuItemActive = static function (array $item): bool {
        if (! isset($item['route'])) {
            return false;
        }

        $activePatterns = $item['active'] ?? [$item['route'], $item['route'].'.*'];

        return collect((array) $activePatterns)->contains(fn ($pattern) => request()->routeIs($pattern));
    };
    $mobileMenu = collect($menu)
        ->flatMap(fn (array $item) => $item['children'] ?? [$item])
        ->take(4);
@endphp

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - EduPlatform</title>
    @include('partials.theme-init', ['useSystemPreference' => $role !== 'admin'])
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/learning-player.js'])
    <style>
        /* Custom scrollbar cho Sidebar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        .sidebar-scrollbar {
            scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
            scrollbar-width: thin;
        }

        /* CSS dùng chung cho Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: fit-content;
            min-width: unset;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
            flex-wrap: nowrap;
            vertical-align: middle;
            flex-shrink: 0;
        }

        .status-badge::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex: 0 0 auto;
        }

        /* Trạng thái hoạt động / Đang bật (Xanh lá) */
        .status-active,
        .status-approved,
        .status-success {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .status-active::before,
        .status-approved::before,
        .status-success::before {
            background-color: #10b981;
        }

        /* Trạng thái chờ / Đang xử lý (Vàng/Cam) */
        .status-pending,
        .status-warning {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }
        .status-pending::before,
        .status-warning::before {
            background-color: #f59e0b;
        }

        /* Trạng thái không hoạt động / Đã tắt (Xám) */
        .status-inactive,
        .status-disabled {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .status-inactive::before,
        .status-disabled::before {
            background-color: #64748b;
        }

        /* Trạng thái nguy hiểm / Hủy / Từ chối (Đỏ) */
        .status-danger,
        .status-rejected,
        .status-canceled,
        .status-expired {
            background-color: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }
        .status-danger::before,
        .status-rejected::before,
        .status-canceled::before,
        .status-expired::before {
            background-color: #f43f5e;
        }

        /* Trạng thái thông tin (Xanh dương) */
        .status-info {
            background-color: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .status-info::before {
            background-color: #0ea5e9;
        }
    </style>
</head>
<body @class([
    'instructor-shell' => $role === 'instructor',
    'admin-shell' => $role === 'admin',
    'bg-[#f5f8fc] text-slate-900 antialiased',
])>
    <div class="flex min-h-screen">
        <aside class="fixed inset-y-0 z-30 hidden w-64 border-r border-blue-100 bg-white text-slate-800 shadow-[10px_0_30px_rgba(37,99,235,0.05)] dark:border-blue-950/50 dark:bg-[#0b1220] dark:text-white lg:flex lg:flex-col">
            <div class="flex h-20 items-center border-b border-blue-100 px-5 dark:border-white/5">
                <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0">
                    <div class="flex h-10 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $c['gradient'] }} text-[11px] font-black tracking-wide text-white shadow-lg shadow-blue-500/20">FEA</div>
                    <div class="min-w-0">
                        <div class="truncate text-base font-extrabold leading-tight text-slate-950 dark:text-white">OnlineFEA</div>
                        <div class="mt-0.5 text-[11px] font-medium text-slate-400">{{ $roleLabel }}</div>
                    </div>
                </a>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto sidebar-scrollbar">
                @foreach($menu as $item)
                    @php
                        $children = $item['children'] ?? [];
                        $childrenActive = collect($children)->contains(fn (array $child) => $isMenuItemActive($child));
                        $active = $isMenuItemActive($item) || $childrenActive;
                    @endphp
                    @if($children)
                        <div x-data="{ open: @json($childrenActive) }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="group flex w-full cursor-pointer items-center gap-3 rounded-xl px-3.5 py-2.5 text-left text-sm font-semibold transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 {{ $active ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300' : 'text-slate-500 hover:bg-blue-50 hover:text-blue-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}"
                                :aria-expanded="open ? 'true' : 'false'"
                            >
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center text-slate-400 transition-colors duration-200 group-hover:text-blue-600 dark:group-hover:text-white {{ $active ? 'text-blue-600 dark:text-blue-300' : '' }}">
                                    {!! $item['icon'] !!}
                                </span>
                                <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                                @php
                                    $totalChildrenBadge = collect($children)->sum(fn($c) => (int)($c['badge'] ?? 0));
                                @endphp
                                @if($totalChildrenBadge > 0)
                                    <span x-show="!open" class="inline-flex items-center justify-center rounded-full bg-amber-500 px-2 py-0.5 text-[11px] font-black text-white leading-none shadow-sm mr-1.5">
                                        {{ $totalChildrenBadge }}
                                    </span>
                                @endif
                                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200 ease-out motion-reduce:transition-none" :class="open ? 'rotate-90 text-blue-600 dark:text-blue-300' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                class="ml-2 mt-1 space-y-0.5"
                            >
                                @foreach($children as $child)
                                    @php $childActive = $isMenuItemActive($child); @endphp
                                    <a href="{{ route($child['route']) }}"
                                       class="group flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium leading-5 transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 {{ $childActive ? 'bg-blue-600 text-white shadow-[0_7px_18px_rgba(37,99,235,0.2)]' : 'text-slate-500 hover:bg-blue-50 hover:text-blue-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                                        <span class="flex h-5 w-5 shrink-0 items-center justify-center text-slate-400 transition-colors duration-200 group-hover:text-blue-600 dark:group-hover:text-white {{ $childActive ? 'text-white group-hover:text-white' : '' }}">
                                            {!! $child['icon'] !!}
                                        </span>
                                        <span class="min-w-0 truncate">{{ $child['label'] }}</span>
                                        @if(!empty($child['badge']))
                                            <span class="ml-auto inline-flex items-center justify-center rounded-full {{ $child['badge_color'] ?? 'bg-amber-500' }} px-2 py-0.5 text-[11px] font-black text-white leading-none shadow-sm">
                                                {{ $child['badge'] }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}"
                           class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-semibold transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50
                                  {{ $active ? 'bg-blue-600 text-white shadow-[0_7px_18px_rgba(37,99,235,0.2)]' : 'text-slate-500 hover:bg-blue-50 hover:text-blue-700 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white' }}">
                            <span class="flex h-5 w-5 shrink-0 items-center justify-center text-slate-400 transition-colors duration-200 group-hover:text-blue-600 dark:group-hover:text-white {{ $active ? 'text-white group-hover:text-white' : '' }}">
                                {!! $item['icon'] !!}
                            </span>
                            <span class="truncate">{{ $item['label'] }}</span>
                            @if(!empty($item['badge']))
                                <span class="ml-auto inline-flex items-center justify-center rounded-full {{ $item['badge_color'] ?? 'bg-amber-500' }} px-2 py-0.5 text-[11px] font-black text-white leading-none shadow-sm">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="border-t border-blue-100 p-3 dark:border-white/5">
                <div class="mb-2 flex items-center gap-3 rounded-xl bg-blue-50 px-3 py-2.5 dark:bg-white/[0.04]">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br {{ $c['gradient'] }} text-xs font-bold text-white shadow-sm shadow-blue-500/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="truncate text-sm font-bold text-slate-800 dark:text-white">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex min-h-9 w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-500 transition-colors duration-200 hover:bg-red-50 hover:text-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50 dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-red-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1 lg:ml-64">
            <header class="sticky top-0 z-20 border-b border-blue-100 bg-white/95 shadow-[0_1px_10px_rgba(37,99,235,0.03)] backdrop-blur dark:border-slate-800 dark:bg-slate-900/95">
                <div class="flex h-20 items-center justify-between px-4 sm:px-6 xl:px-8">
                    <div class="min-w-0">
                        <h1 class="{{ $pageTitleClass }} text-slate-950 dark:text-white">{{ $pageTitle }}</h1>
                        @if($breadcrumb)
                            <p class="text-xs text-slate-500 mt-1 truncate">{{ $breadcrumb }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Nút chuyển chế độ Sáng/Tối -->
                        <button onclick="toggleTheme()" class="cursor-pointer rounded-xl p-2.5 text-slate-500 transition duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-blue-300" aria-label="Đổi giao diện">
                            <svg class="hidden dark:block h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                            <svg class="block dark:hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </button>
                        {{-- Icon Nhóm học tập --}}
                        <a href="{{ route('study-groups.index') }}" 
                           class="relative flex cursor-pointer items-center justify-center rounded-xl p-2.5 text-slate-500 transition duration-200 hover:bg-blue-50 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-blue-300"
                           title="Nhóm học tập"
                           aria-label="Nhóm học tập">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @if(($unreadStudyGroupCount ?? 0) > 0)
                                <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold text-white leading-none animate-pulse">
                                    {{ $unreadStudyGroupCount > 99 ? '99+' : $unreadStudyGroupCount }}
                                </span>
                            @endif
                        </a>
                        <x-notifications.bell
                            :recent-notifications="$recentNotifications ?? collect()"
                            :unread-count="$unreadNotificationCount ?? 0"
                        />
                        <a href="{{ route('home') }}" class="hidden items-center gap-1.5 rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 transition duration-200 hover:border-blue-200 hover:bg-blue-100 dark:border-blue-900/50 dark:bg-blue-500/10 dark:text-blue-300 sm:inline-flex">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 12 2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3m-6 0a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1m-6 0h6"/></svg>
                            Trang chủ
                        </a>
                    </div>
                </div>
            </header>

            <x-toast-container />

            <main class="min-h-[calc(100vh-5rem)] p-4 sm:p-6 xl:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-30 flex justify-around border-t border-blue-100 bg-white/95 py-2 shadow-[0_-8px_24px_rgba(37,99,235,0.08)] backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 lg:hidden">
        @foreach($mobileMenu as $item)
            @php $active = $isMenuItemActive($item); @endphp
            <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-0.5 px-3 py-1 text-xs {{ $active ? $c['text'] : 'text-slate-400' }}">
                <span class="flex h-5 w-5 items-center justify-center">
                    @if(isset($item['icon']))
                        {!! $item['icon'] !!}
                    @else
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    @endif
                </span>
                <span class="truncate max-w-[60px]">{{ Str::before($item['label'], ' ') }}</span>
            </a>
        @endforeach
    </nav>
    <div class="h-16 lg:hidden"></div>
</body>
</html>
