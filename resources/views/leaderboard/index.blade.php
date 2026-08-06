@extends('layouts.app')

@section('title', 'Bảng xếp hạng học viên xuất sắc - Website học online FEA')

@section('content')
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen py-10">
    <div class="ui-container max-w-7xl">
        
        {{-- Header & Title --}}
        <div class="mb-8 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center justify-center sm:justify-start gap-2">
                    <span>🏆</span> Bảng Xếp Hạng Học Viên
                </h1>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    Nơi vinh danh các học viên có thành tích xuất sắc và tích cực nhất trên hệ thống.
                </p>
            </div>
            
            {{-- Quick Info Card / Badges explanation --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs text-slate-600 dark:text-slate-400 shadow-sm flex items-center gap-4 max-w-md">
                <div>📚 <strong>+10</strong> Bài học</div>
                <div>📝 <strong>+30</strong> Chương</div>
                <div>🎓 <strong>+100</strong> Khóa học</div>
                <div>🎯 <strong>+20/+40</strong> Quiz</div>
            </div>
        </div>

        {{-- Filters & Search Section --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm mb-6">
            <form method="GET" action="{{ route('leaderboard') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                
                {{-- Name Search --}}
                <div class="md:col-span-4 relative">
                    <label for="search" class="sr-only">Tìm kiếm học viên</label>
                    <div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Tìm tên học viên..." class="h-10 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 pl-10 pr-4 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-[#0056D2]">
                </div>

                {{-- Period Filter --}}
                <div class="md:col-span-3">
                    <label for="period" class="sr-only">Chu kỳ</label>
                    <select name="period" id="period" onchange="this.form.submit()" class="h-10 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-[#0056D2]">
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Tất cả thời gian</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Tháng này</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Năm này</option>
                    </select>
                </div>

                {{-- Course Filter --}}
                <div class="md:col-span-3">
                    <label for="course_id" class="sr-only">Khóa học</label>
                    <select name="course_id" id="course_id" onchange="this.form.submit()" class="h-10 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 text-sm text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-[#0056D2]">
                        <option value="">Tất cả khóa học</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ (int)$courseId === (int)$course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Reset / Submit Buttons --}}
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit" class="h-10 flex-1 rounded-lg bg-[#0056D2] hover:bg-[#0046B8] text-white text-sm font-bold transition flex items-center justify-center cursor-pointer">
                        Lọc
                    </button>
                    @if($search || $courseId || $period !== 'all')
                        <a href="{{ route('leaderboard') }}" class="h-10 px-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold transition flex items-center justify-center cursor-pointer" title="Xóa bộ lọc">
                            ✕
                        </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- Current Logged-In User Personal Rank Banner --}}
        @if($currentUserData)
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 dark:from-blue-900 dark:to-indigo-950 text-white rounded-2xl p-6 shadow-md mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-3xl font-extrabold shadow-inner border border-white/30 shrink-0">
                        #{{ $currentUserData['rank'] }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs uppercase tracking-wider text-blue-200 font-bold">Thứ hạng của bạn</div>
                        <div class="text-xl font-black truncate max-w-xs md:max-w-md">{{ $currentUserData['user']->name }}</div>
                        <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-blue-100">
                            <span>🎓 Khóa đã xong: <strong>{{ $currentUserData['completed_courses_count'] }}</strong></span>
                            <span>🎯 Điểm Quiz TB: <strong>{{ round($currentUserData['avg_quiz_score'], 1) }}%</strong></span>
                        </div>
                    </div>
                </div>

                {{-- Badges Earned --}}
                <div class="flex items-center gap-4 shrink-0">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs text-blue-200">Tổng điểm tích lũy</div>
                        <div class="text-2xl font-black text-amber-300">{{ $currentUserData['total_points'] }} pts</div>
                    </div>
                    <div class="h-px w-8 bg-white/20 hidden sm:block"></div>
                    <div class="flex items-center gap-2">
                        @forelse($currentUserData['badges'] as $badge)
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-amber-400 text-white shadow-md border-2 border-white cursor-pointer" title="{{ $badge->name }}: {{ $badge->description }}">
                                🎖️
                            </span>
                        @empty
                            <span class="text-xs text-blue-200 italic">Chưa có huy hiệu</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- Leaderboard Table --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            @if($leaderboard->isEmpty())
                <div class="py-16 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-3xl">
                        👥
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Chưa có dữ liệu xếp hạng</h3>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Không tìm thấy học viên nào phù hợp với bộ lọc hiện tại.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-xs font-bold uppercase text-slate-700 dark:text-slate-300">
                                <th scope="col" class="px-6 py-4 text-center w-20">Thứ Hạng</th>
                                <th scope="col" class="px-6 py-4">Học Viên</th>
                                <th scope="col" class="px-6 py-4 text-center">Tổng Điểm</th>
                                <th scope="col" class="px-6 py-4 text-center">Khóa Hoàn Thành</th>
                                <th scope="col" class="px-6 py-4 text-center">Quiz TB</th>
                                <th scope="col" class="px-6 py-4">Huy Hiệu</th>
                            </tr>
                        </thead>
                        <tr class="h-1 bg-transparent"></tr>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($leaderboard as $index => $student)
                                @php
                                    // Calculate pagination absolute index
                                    $absoluteIndex = ($leaderboard->currentPage() - 1) * $leaderboard->perPage() + $index;
                                    $isTop3 = $absoluteIndex < 3;
                                    
                                    // Medals for top 3
                                    $medal = '';
                                    $rowBg = 'bg-white dark:bg-slate-900';
                                    if ($isTop3) {
                                        if ($absoluteIndex == 0) {
                                            $medal = '🥇';
                                            $rowBg = 'bg-amber-50/30 dark:bg-amber-950/10';
                                        } elseif ($absoluteIndex == 1) {
                                            $medal = '🥈';
                                            $rowBg = 'bg-slate-100/30 dark:bg-slate-800/10';
                                        } else {
                                            $medal = '🥉';
                                            $rowBg = 'bg-orange-50/30 dark:bg-orange-950/10';
                                        }
                                    }
                                @endphp
                                <tr class="{{ $rowBg }} hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                    {{-- Rank --}}
                                    <td class="px-6 py-4 text-center font-bold">
                                        @if($isTop3)
                                            <span class="text-2xl">{{ $medal }}</span>
                                        @else
                                            <span class="text-slate-500 dark:text-slate-400">#{{ $student->rank }}</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Student Info --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $student->avatarUrl() }}" alt="Avatar" class="h-10 w-10 rounded-full border border-slate-200 dark:border-slate-700 object-cover shrink-0">
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white truncate max-w-[150px] sm:max-w-xs">{{ $student->name }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">Tham gia: {{ $student->created_at->format('m/Y') }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Total Points --}}
                                    <td class="px-6 py-4 text-center font-extrabold text-indigo-600 dark:text-indigo-400 text-base">
                                        {{ $student->total_points }}
                                    </td>

                                    {{-- Completed Courses --}}
                                    <td class="px-6 py-4 text-center font-semibold text-slate-900 dark:text-white">
                                        {{ $student->completed_courses_count }}
                                    </td>

                                    {{-- Avg Quiz Score --}}
                                    <td class="px-6 py-4 text-center font-semibold text-slate-900 dark:text-white">
                                        {{ round($student->avg_quiz_score, 1) }}%
                                    </td>

                                    {{-- Badges --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @forelse($student->badges as $badge)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 dark:bg-amber-950/30 px-2.5 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-900/50 cursor-pointer" title="{{ $badge->description }}">
                                                    <span>🎖️</span> {{ $badge->name }}
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
@endsection
