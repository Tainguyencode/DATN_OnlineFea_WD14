<x-instructor-layout :title="$course->title" page-title="Học viên" :breadcrumb="$course->title">

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="p-5 border-b border-slate-100">
        <h2 class="font-bold text-slate-900">{{ $course->title }}</h2>
        <p class="text-sm text-slate-500">{{ $enrollments->total() }} học viên</p>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Học viên</th>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Email</th>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Tiến độ</th>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Ngày đăng ký</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($enrollments as $enrollment)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                            </div>
                            {{ $enrollment->user->name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $enrollment->user->email }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-20 bg-slate-100 rounded-full h-2">
                                <div class="bg-emerald-500 rounded-full h-2" style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                            <span class="text-xs">{{ number_format($enrollment->progress_percent, 0) }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ $enrollment->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-slate-500">Chưa có học viên.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t">{{ $enrollments->links() }}</div>
</div>

</x-instructor-layout>
