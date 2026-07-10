<x-student-layout title="Tổng quan" page-title="Xin chào, {{ Auth::user()->name }}!" breadcrumb="Theo dõi tiến độ học tập của bạn">

<div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <x-stat-card label="Khóa đã đăng ký" :value="$stats['enrolled']" color="indigo"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' />
    <x-stat-card label="Đang học" :value="$stats['in_progress']" color="amber"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>' />
    <x-stat-card label="Hoàn thành" :value="$stats['completed']" color="emerald"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
    <x-stat-card label="Chứng chỉ" :value="$stats['certificates']" color="purple"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>' />
</div>

{{-- Progress overview --}}
<div class="mb-8 rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Tiến độ học tập trung bình</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tiếp tục học để hoàn thành mục tiêu của bạn</p>
        </div>
        <div class="text-4xl font-bold text-[#0056D2] dark:text-blue-300">{{ number_format($avgProgress, 0) }}%</div>
    </div>
    <progress class="mt-4 h-3 w-full overflow-hidden rounded-full [&::-moz-progress-bar]:bg-[#0056D2] [&::-webkit-progress-bar]:bg-slate-200 [&::-webkit-progress-value]:bg-[#0056D2] dark:[&::-webkit-progress-bar]:bg-slate-800" max="100" value="{{ min(100, $avgProgress) }}"></progress>
</div>

{{-- Recent courses --}}
<div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
    <div class="flex items-center justify-between border-b border-slate-200 p-6 dark:border-slate-800">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Khóa học gần đây</h2>
        <a href="{{ route('student.courses') }}" class="text-sm font-semibold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300">Xem tất cả</a>
    </div>
    @if($enrollments->isEmpty())
        <div class="p-12 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/40">
                <svg class="h-8 w-8 text-[#0056D2] dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p class="font-medium text-slate-500 dark:text-slate-400">Bạn chưa đăng ký khóa học nào</p>
            <a href="{{ route('home') }}#courses" class="ui-button-primary mt-4">Khám phá khóa học</a>
        </div>
    @else
        <div class="divide-y divide-slate-200 dark:divide-slate-800">
            @foreach($enrollments as $enrollment)
                <div class="flex items-center gap-4 p-5 transition duration-200 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-blue-50 font-bold text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-300">
                        {{ strtoupper(substr($enrollment->course->title, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate font-semibold text-slate-900 dark:text-white">{{ $enrollment->course->title }}</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $enrollment->course->instructor?->name }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            <progress class="h-2 max-w-xs flex-1 overflow-hidden rounded-full [&::-moz-progress-bar]:bg-[#0056D2] [&::-webkit-progress-bar]:bg-slate-200 [&::-webkit-progress-value]:bg-[#0056D2] dark:[&::-webkit-progress-bar]:bg-slate-800" max="100" value="{{ $enrollment->progress_percent }}"></progress>
                            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ number_format($enrollment->progress_percent, 0) }}%</span>
                        </div>
                    </div>
                    @if($enrollment->course->learningEntryUrl())
                        <a href="{{ $enrollment->course->learningEntryUrl() }}" class="ui-button-primary shrink-0 px-4 py-2 text-sm">
                            Vào học
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

</x-student-layout>
