<x-admin-layout title="Tổng quan" page-title="Bảng điều khiển Admin" breadcrumb="Quản trị hệ thống EduPlatform">

<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <x-stat-card label="Người dùng" :value="$stats['users']" color="rose"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>' />
    <x-stat-card label="Khóa học" :value="$stats['courses']" color="indigo"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' />
    <x-stat-card label="Chờ duyệt" :value="$stats['pending']" color="amber"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
    <x-stat-card label="Doanh thu" :value="number_format($stats['revenue'], 0, ',', '.') . 'đ'" color="emerald"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>' />
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex justify-between">
            <h2 class="font-bold text-slate-900">Khóa học chờ duyệt</h2>
            <a href="{{ route('admin.courses.pending') }}" class="text-sm text-rose-500 hover:underline">Xem tất cả</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($pendingCourses as $course)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <h3 class="font-medium text-slate-900 text-sm">{{ $course->title }}</h3>
                        <p class="text-xs text-slate-500">{{ $course->instructor?->name }} · {{ $course->category?->name }}</p>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.courses.approve', $course) }}">@csrf
                            <button class="text-xs bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-lg font-medium hover:bg-emerald-200">Duyệt</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-sm">Không có khóa học chờ duyệt.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div class="p-5 border-b border-slate-100 flex justify-between">
            <h2 class="font-bold text-slate-900">Hoạt động gần đây</h2>
            <a href="{{ route('admin.activity-logs') }}" class="text-sm text-rose-500 hover:underline">Xem tất cả</a>
        </div>
        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            @foreach($recentLogs as $log)
                <div class="p-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-900">{{ $log->user?->name ?? 'Hệ thống' }}</span>
                        <span class="text-slate-400">·</span>
                        <span class="text-rose-500 font-mono text-xs">{{ $log->action }}</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

</x-admin-layout>
