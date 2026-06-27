<x-instructor-layout title="Tổng quan" page-title="Dashboard Giảng viên" breadcrumb="Quản lý khóa học và học viên">

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <x-stat-card label="Tổng khóa học" :value="$stats['courses']" color="emerald"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>' />
    <x-stat-card label="Đã xuất bản" :value="$stats['published']" color="blue"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
    <x-stat-card label="Học viên" :value="$stats['students']" color="purple"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' />
    <x-stat-card label="Doanh thu" :value="number_format($stats['revenue'], 0, ',', '.') . 'đ'" color="amber"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-bold text-slate-900">Khóa học gần đây</h2>
            <a href="{{ route('instructor.courses.index') }}" class="text-sm text-emerald-600 hover:underline">Xem tất cả</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentCourses as $course)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                    <div>
                        <h3 class="font-medium text-slate-900">{{ $course->title }}</h3>
                        <p class="text-xs text-slate-500">{{ $course->enrollments_count }} học viên · {{ $course->category?->name }}</p>
                    </div>
                    @php
                        $statusColors = ['draft' => 'bg-slate-100 text-slate-600', 'pending' => 'bg-amber-100 text-amber-700', 'published' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700'];
                    @endphp
                    <span class="text-xs px-2 py-1 rounded-full font-medium {{ $statusColors[$course->status] ?? '' }}">{{ ucfirst($course->status) }}</span>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-sm">Chưa có khóa học. <a href="{{ route('instructor.courses.create') }}" class="text-emerald-600 hover:underline">Tạo ngay</a></div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">Học viên mới đăng ký</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($recentStudents as $enrollment)
                <div class="p-4 flex items-center gap-3 hover:bg-slate-50">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-slate-900 text-sm">{{ $enrollment->user->name }}</div>
                        <div class="text-xs text-slate-500">{{ $enrollment->course->title }}</div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-sm">Chưa có học viên đăng ký.</div>
            @endforelse
        </div>
    </div>
</div>

</x-instructor-layout>
