<x-student-layout title="Tổng quan" page-title="Xin chào, {{ Auth::user()->name }}! 👋" breadcrumb="Theo dõi tiến độ học tập của bạn">

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
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
<div class="bg-gradient-to-r from-indigo-600 to-violet-600 rounded-2xl p-6 sm:p-8 text-white mb-8 shadow-lg">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold">Tiến độ học tập trung bình</h2>
            <p class="text-indigo-100 text-sm mt-1">Tiếp tục học để hoàn thành mục tiêu của bạn</p>
        </div>
        <div class="text-4xl font-bold">{{ number_format($avgProgress, 0) }}%</div>
    </div>
    <div class="mt-4 bg-white/20 rounded-full h-3">
        <div class="bg-white rounded-full h-3 transition-all" style="width: {{ min(100, $avgProgress) }}%"></div>
    </div>
</div>

{{-- Recent courses --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="flex items-center justify-between p-6 border-b border-slate-100">
        <h2 class="text-lg font-bold text-slate-900">Khóa học gần đây</h2>
        <a href="{{ route('student.courses') }}" class="text-sm text-indigo-600 hover:underline font-medium">Xem tất cả →</a>
    </div>
    @if($enrollments->isEmpty())
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p class="text-slate-600 font-medium">Bạn chưa đăng ký khóa học nào</p>
            <a href="{{ route('home') }}#courses" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm font-medium hover:bg-indigo-700 transition">Khám phá khóa học</a>
        </div>
    @else
        <div class="divide-y divide-slate-100">
            @foreach($enrollments as $enrollment)
                <div class="p-5 flex items-center gap-4 hover:bg-slate-50 transition">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center text-white font-bold shrink-0">
                        {{ strtoupper(substr($enrollment->course->title, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-slate-900 truncate">{{ $enrollment->course->title }}</h3>
                        <p class="text-sm text-slate-500">{{ $enrollment->course->instructor?->name }}</p>
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 bg-slate-100 rounded-full h-2 max-w-xs">
                                <div class="bg-indigo-500 rounded-full h-2" style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                            <span class="text-xs text-slate-500 font-medium">{{ number_format($enrollment->progress_percent, 0) }}%</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

</x-student-layout>
