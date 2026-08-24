@extends('layouts.app')

@section('title', 'Bảng xếp hạng học viên xuất sắc - FEA Online')

@section('content')
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen py-8">
    <div class="ui-container max-w-7xl">

        {{-- Header Title & Countdown Banner --}}
        <div class="mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span>🏆</span> Bảng Xếp Hạng Học Viên
                </h1>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Vinh danh học viên xuất sắc và tích cực nhất trên hệ thống theo {{ $period === 'month' ? 'tháng' : 'tuần' }}.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                @if($period === 'month')
                    <a href="#monthly-rewards-section" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold transition">
                        Cơ cấu giải thưởng
                    </a>
                @endif

                {{-- Live Countdown Card --}}
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl px-5 py-3 shadow-md flex items-center gap-4">
                    <div class="text-2xl">⏳</div>
                    <div>
                        <div class="text-xs font-medium text-blue-100 uppercase tracking-wider">
                            Thời gian còn lại của {{ $period === 'month' ? 'tháng' : 'tuần' }}
                        </div>
                        <div id="leaderboard-countdown" class="text-lg font-black tracking-wider text-amber-300 font-mono">
                            -- ngày --:--:--
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Navigation Tabs & Search --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            {{-- Tabs: TUẦN NÀY | THÁNG NÀY --}}
            <div class="inline-flex rounded-xl bg-slate-200/80 dark:bg-slate-800/80 p-1.5 shadow-inner self-start">
                <a href="{{ route('leaderboard', ['period' => 'week', 'search' => $search]) }}" 
                   class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center gap-2 {{ $period === 'week' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>📅</span> TUẦN NÀY
                </a>
                <a href="{{ route('leaderboard', ['period' => 'month', 'search' => $search]) }}" 
                   class="px-6 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 flex items-center gap-2 {{ $period === 'month' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-md' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                    <span>🗓️</span> THÁNG NÀY
                </a>
            </div>

            {{-- Search Box --}}
            <form method="GET" action="{{ route('leaderboard') }}" class="flex items-center gap-2 w-full sm:w-auto">
                <input type="hidden" name="period" value="{{ $period }}">
                <div class="relative w-full sm:w-64">
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Tìm tên học viên..." 
                           class="w-full h-10 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 pl-10 pr-4 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-blue-600">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
                </div>
                <button type="submit" class="h-10 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition cursor-pointer">
                    Tìm
                </button>
                @if($search)
                    <a href="{{ route('leaderboard', ['period' => $period]) }}" class="h-10 px-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-bold flex items-center justify-center">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        {{-- TOP 3 Monthly Rewards Showcase Banner (Only on Month view) --}}
        @if($period === 'month')
            <div id="monthly-rewards-section" class="mb-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            Cơ Cấu Giải Thưởng Top 3 Tháng
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Vinh danh và tự động cấp thưởng vào cuối mỗi tháng cho 3 học viên có XP cao nhất</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 px-3 py-1.5 rounded-lg border border-blue-100 dark:border-blue-900/50 self-start sm:self-auto">
                        Tự động trao thưởng qua hệ thống
                    </span>
                </div>

                {{-- Grid 3 Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- TOP 1 Card --}}
                    <div class="bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 rounded-xl p-5 transition hover:shadow-xs">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-extrabold text-amber-900 dark:text-amber-300 bg-amber-200/80 dark:bg-amber-900/60 px-2.5 py-0.5 rounded-md">
                                TOP 1
                            </span>
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400">Quán Quân</span>
                        </div>
                        <div class="text-base font-extrabold text-slate-900 dark:text-white mb-3">
                            {{ $monthlyRewards[1]['voucher'] }}
                        </div>
                        <ul class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>{{ $monthlyRewards[1]['xp'] }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                <span>{{ $monthlyRewards[1]['badge'] }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- TOP 2 Card --}}
                    <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-xl p-5 transition hover:shadow-xs">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300 bg-slate-200 dark:bg-slate-700 px-2.5 py-0.5 rounded-md">
                                TOP 2
                            </span>
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">Á Quân</span>
                        </div>
                        <div class="text-base font-extrabold text-slate-900 dark:text-white mb-3">
                            {{ $monthlyRewards[2]['voucher'] }}
                        </div>
                        <ul class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>{{ $monthlyRewards[2]['xp'] }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                <span>{{ $monthlyRewards[2]['badge'] }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- TOP 3 Card --}}
                    <div class="bg-amber-900/5 dark:bg-amber-950/10 border border-amber-700/20 dark:border-amber-900/30 rounded-xl p-5 transition hover:shadow-xs">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-extrabold text-amber-900 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/60 px-2.5 py-0.5 rounded-md">
                                TOP 3
                            </span>
                            <span class="text-xs font-bold text-amber-800 dark:text-amber-500">Tinh Anh</span>
                        </div>
                        <div class="text-base font-extrabold text-slate-900 dark:text-white mb-3">
                            {{ $monthlyRewards[3]['voucher'] }}
                        </div>
                        <ul class="space-y-1.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-700"></span>
                                <span>{{ $monthlyRewards[3]['xp'] }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-700"></span>
                                <span>{{ $monthlyRewards[3]['badge'] }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- TOP 1, 2, 3 Podium Section (Only on page 1 without active search) --}}
        @if(count($top3) >= 1 && $leaderboard->currentPage() === 1 && !$search)
            <div class="mb-10">
                <h2 class="text-center text-xs uppercase font-extrabold tracking-widest text-slate-400 dark:text-slate-500 mb-6">
                    🌟 VINH DANH TOP 3 XUẤT SẮC NHẤT {{ $period === 'month' ? 'THÁNG' : 'TUẦN' }} 🌟
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end max-w-4xl mx-auto">
                    {{-- TOP 2 (Left) --}}
                    @if(isset($top3[1]))
                        @php $st2 = $top3[1]; @endphp
                        <div class="order-2 md:order-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-md text-center relative transform transition hover:-translate-y-1">
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-slate-300 dark:bg-slate-700 text-slate-900 dark:text-white px-3 py-0.5 rounded-full text-xs font-black shadow-sm">
                                🥈 TOP 2
                            </div>
                            <img src="{{ $st2->avatarUrl() }}" alt="{{ $st2->name }}" class="h-20 w-20 rounded-full border-4 border-slate-300 mx-auto object-cover shadow-md mt-2">
                            <h3 class="mt-3 font-bold text-slate-900 dark:text-white text-base truncate">{{ $st2->name }}</h3>
                            <div class="mt-1 text-2xl font-black text-blue-600 dark:text-blue-400">
                                {{ $st2->period_xp }} <span class="text-xs text-slate-500 font-semibold">XP</span>
                            </div>
                            <div class="mt-2 flex items-center justify-center gap-3 text-xs text-slate-500">
                                <span>🎓 {{ $st2->completed_courses_count }} khóa</span>
                                <span>🔥 {{ $st2->streak_days }}d streak</span>
                            </div>
                            @if($period === 'month')
                                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                                    <span class="inline-flex items-center text-[11px] font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700">
                                        Thưởng tháng: {{ $monthlyRewards[2]['voucher'] }} + 500 XP
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- TOP 1 (Center Highlight) --}}
                    @if(isset($top3[0]))
                        @php $st1 = $top3[0]; @endphp
                        <div class="order-1 md:order-2 bg-gradient-to-b from-amber-500/10 to-yellow-500/5 bg-white dark:bg-slate-900 border-2 border-amber-400 rounded-2xl p-6 shadow-xl text-center relative transform md:-translate-y-4 transition hover:-translate-y-5">
                            <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-amber-400 to-yellow-500 text-white px-4 py-1 rounded-full text-xs font-black shadow-lg flex items-center gap-1">
                                👑 TOP 1 BÁ VƯƠNG
                            </div>
                            <div class="relative inline-block mt-2">
                                <img src="{{ $st1->avatarUrl() }}" alt="{{ $st1->name }}" class="h-24 w-24 rounded-full border-4 border-amber-400 mx-auto object-cover shadow-lg">
                                <span class="absolute -bottom-2 -right-2 text-2xl">🥇</span>
                            </div>
                            <h3 class="mt-3 font-extrabold text-slate-900 dark:text-white text-lg truncate">{{ $st1->name }}</h3>
                            <div class="mt-1 text-3xl font-black text-amber-500">
                                {{ $st1->period_xp }} <span class="text-xs text-slate-500 font-semibold">XP</span>
                            </div>
                            <div class="mt-2 flex items-center justify-center gap-3 text-xs text-slate-600 dark:text-slate-400 font-medium">
                                <span>🎓 {{ $st1->completed_courses_count }} khóa hoàn thành</span>
                                <span>🔥 {{ $st1->streak_days }}d streak</span>
                            </div>
                            @if($period === 'month')
                                <div class="mt-3 pt-3 border-t border-amber-200 dark:border-amber-900/50">
                                    <span class="inline-flex items-center text-[11px] font-extrabold text-amber-900 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 px-3 py-1 rounded-full border border-amber-300 dark:border-amber-800">
                                        Thưởng tháng: {{ $monthlyRewards[1]['voucher'] }} + 1.000 XP
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- TOP 3 (Right) --}}
                    @if(isset($top3[2]))
                        @php $st3 = $top3[2]; @endphp
                        <div class="order-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-md text-center relative transform transition hover:-translate-y-1">
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-amber-700 text-white px-3 py-0.5 rounded-full text-xs font-black shadow-sm">
                                🥉 TOP 3
                            </div>
                            <img src="{{ $st3->avatarUrl() }}" alt="{{ $st3->name }}" class="h-20 w-20 rounded-full border-4 border-amber-700/60 mx-auto object-cover shadow-md mt-2">
                            <h3 class="mt-3 font-bold text-slate-900 dark:text-white text-base truncate">{{ $st3->name }}</h3>
                            <div class="mt-1 text-2xl font-black text-amber-700 dark:text-amber-500">
                                {{ $st3->period_xp }} <span class="text-xs text-slate-500 font-semibold">XP</span>
                            </div>
                            <div class="mt-2 flex items-center justify-center gap-3 text-xs text-slate-500">
                                <span>🎓 {{ $st3->completed_courses_count }} khóa</span>
                                <span>🔥 {{ $st3->streak_days }}d streak</span>
                            </div>
                            @if($period === 'month')
                                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                                    <span class="inline-flex items-center text-[11px] font-semibold text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 rounded-full border border-amber-200 dark:border-amber-900/50">
                                        Thưởng tháng: {{ $monthlyRewards[3]['voucher'] }} + 300 XP
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Current Logged-In Student Personal Achievements Banner --}}
        @if($currentUserData)
            <div class="mb-8 bg-gradient-to-r from-blue-600 via-indigo-700 to-purple-800 text-white rounded-2xl p-6 shadow-xl">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    {{-- Student Rank & Info --}}
                    <div class="flex items-center gap-4">
                        <img src="{{ $currentUserData['user']->avatarUrl() }}" alt="Avatar" class="h-16 w-16 rounded-full border-2 border-white/50 object-cover shadow-md shrink-0">
                        <div class="min-w-0">
                            <div class="text-xs font-bold uppercase tracking-wider text-blue-100">Thành Tích Của Bạn</div>
                            <h2 class="text-xl font-black text-white truncate">{{ $currentUserData['user']->name }}</h2>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-blue-100">
                                <span>Hạng Tuần: <strong class="text-amber-300">#{{ $currentUserData['weekly_rank'] }}</strong></span>
                                <span>•</span>
                                <span>Hạng Tháng: <strong class="text-amber-300">#{{ $currentUserData['monthly_rank'] }}</strong></span>
                                <span>•</span>
                                <span>Streak: <strong class="text-amber-300">🔥 {{ $currentUserData['streak_days'] }} ngày</strong></span>
                            </div>
                        </div>
                    </div>

                    {{-- XP Stats Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center bg-white/10 backdrop-blur-md rounded-xl p-3 border border-white/15">
                        <div class="px-3 py-1">
                            <div class="text-[11px] text-blue-100 font-medium uppercase">XP Tuần</div>
                            <div class="text-lg font-black text-amber-300">{{ $currentUserData['weekly_xp'] }}</div>
                        </div>
                        <div class="px-3 py-1 border-l border-white/15">
                            <div class="text-[11px] text-blue-100 font-medium uppercase">XP Tháng</div>
                            <div class="text-lg font-black text-amber-300">{{ $currentUserData['monthly_xp'] }}</div>
                        </div>
                        <div class="px-3 py-1 border-l border-white/15">
                            <div class="text-[11px] text-blue-100 font-medium uppercase">XP Tổng</div>
                            <div class="text-lg font-black text-white">{{ $currentUserData['total_xp'] }}</div>
                        </div>
                        <div class="px-3 py-1 border-l border-white/15">
                            <div class="text-[11px] text-blue-100 font-medium uppercase">Khóa Học</div>
                            <div class="text-lg font-black text-white">{{ $currentUserData['completed_courses'] }}</div>
                        </div>
                    </div>
                </div>

                {{-- Badges row --}}
                @if(count($currentUserData['badges']) > 0)
                    <div class="mt-4 pt-4 border-t border-white/15 flex items-center gap-2 flex-wrap text-xs">
                        <span class="text-blue-100 font-semibold">Huy hiệu đã đạt:</span>
                        @foreach($currentUserData['badges'] as $badge)
                            <span class="inline-flex items-center gap-1 bg-white/20 px-2.5 py-1 rounded-full text-white font-bold border border-white/30" title="{{ $badge->description }}">
                                🎖️ {{ $badge->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Leaderboard Table --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-base">
                    Bảng Xếp Hạng {{ $period === 'month' ? 'Tháng này' : 'Tuần này' }}
                </h3>
                <span class="text-xs text-slate-500 font-medium">Cập nhật thời gian thực</span>
            </div>

            @if($leaderboard->isEmpty())
                <div class="py-16 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-2xl">
                        👥
                    </div>
                    <h4 class="text-base font-bold text-slate-900 dark:text-white">Chưa có dữ liệu xếp hạng</h4>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Hãy là học viên đầu tiên tích lũy XP trong {{ $period === 'month' ? 'tháng' : 'tuần' }} này!</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-200 dark:border-slate-800 text-xs font-bold uppercase text-slate-700 dark:text-slate-300">
                                <th scope="col" class="px-6 py-3.5 text-center w-20">Hạng</th>
                                <th scope="col" class="px-6 py-3.5">Học Viên</th>
                                <th scope="col" class="px-6 py-3.5 text-center">XP {{ $period === 'month' ? 'Tháng' : 'Tuần' }}</th>
                                <th scope="col" class="px-6 py-3.5 text-center">XP Tổng</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Khóa Học</th>
                                <th scope="col" class="px-6 py-3.5 text-center">Streak</th>
                                <th scope="col" class="px-6 py-3.5">Huy Hiệu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($leaderboard as $index => $student)
                                @php
                                    $absoluteRank = ($leaderboard->currentPage() - 1) * $leaderboard->perPage() + $index + 1;
                                    $isTop1 = $absoluteRank === 1;
                                    $isTop2 = $absoluteRank === 2;
                                    $isTop3 = $absoluteRank === 3;
                                    $isSelf = auth()->check() && (int) auth()->id() === (int) $student->id;

                                    $rowBg = 'bg-white dark:bg-slate-900';
                                    if ($isSelf) {
                                        $rowBg = 'bg-blue-50/70 dark:bg-blue-950/30 font-semibold';
                                    } elseif ($isTop1) {
                                        $rowBg = 'bg-amber-50/40 dark:bg-amber-950/20';
                                    } elseif ($isTop2) {
                                        $rowBg = 'bg-slate-100/40 dark:bg-slate-800/20';
                                    } elseif ($isTop3) {
                                        $rowBg = 'bg-orange-50/40 dark:bg-orange-950/20';
                                    }
                                @endphp
                                <tr class="{{ $rowBg }} hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                    {{-- Rank --}}
                                    <td class="px-6 py-4 text-center font-black">
                                        @if($isTop1)
                                            <span class="text-2xl">🥇</span>
                                        @elseif($isTop2)
                                            <span class="text-2xl">🥈</span>
                                        @elseif($isTop3)
                                            <span class="text-2xl">🥉</span>
                                        @else
                                            <span class="text-slate-500 dark:text-slate-400">#{{ $absoluteRank }}</span>
                                        @endif
                                    </td>

                                    {{-- Student Info --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $student->avatarUrl() }}" alt="{{ $student->name }}" class="h-10 w-10 rounded-full border border-slate-200 dark:border-slate-700 object-cover shrink-0">
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white truncate flex items-center gap-1.5">
                                                    <span>{{ $student->name }}</span>
                                                    @if($isSelf)
                                                        <span class="bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">Bạn</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">Tham gia: {{ $student->created_at->format('m/Y') }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Period XP --}}
                                    <td class="px-6 py-4 text-center font-extrabold text-blue-600 dark:text-blue-400 text-base">
                                        {{ $student->period_xp }} XP
                                    </td>

                                    {{-- Total XP --}}
                                    <td class="px-6 py-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                        {{ $student->total_xp }}
                                    </td>

                                    {{-- Completed Courses --}}
                                    <td class="px-6 py-4 text-center font-semibold text-slate-900 dark:text-white">
                                        {{ $student->completed_courses_count }}
                                    </td>

                                    {{-- Streak --}}
                                    <td class="px-6 py-4 text-center font-bold text-amber-600 dark:text-amber-400">
                                        🔥 {{ $student->streak_days }}d
                                    </td>

                                    {{-- Badges --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1 flex-wrap">
                                            @forelse($student->badges as $badge)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-950/40 px-2 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50" title="{{ $badge->description }}">
                                                    🎖️ {{ $badge->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-slate-400 italic">Chưa có</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800">
                    {{ $leaderboard->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

{{-- Live Countdown JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const targetDate = new Date("{{ $countdownTarget->toIso8601String() }}").getTime();
        const countdownEl = document.getElementById('leaderboard-countdown');

        function updateCountdown() {
            const now = new Date().getTime();
            const diff = targetDate - now;

            if (diff <= 0) {
                if (countdownEl) countdownEl.innerHTML = "Đang chốt chu kỳ...";
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const daysStr = days > 0 ? `${days}d ` : '';
            const hoursStr = String(hours).padStart(2, '0');
            const minsStr = String(minutes).padStart(2, '0');
            const secsStr = String(seconds).padStart(2, '0');

            if (countdownEl) {
                countdownEl.innerHTML = `${daysStr}${hoursStr}:${minsStr}:${secsStr}`;
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
</script>
@endsection
