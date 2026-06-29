<x-instructor-layout title="Khóa học" page-title="Quản lý khóa học">

<div class="flex justify-between items-center mb-6">
    <p class="text-slate-500 text-sm">{{ $courses->total() }} khóa học</p>
    <a href="{{ route('instructor.courses.create') }}" class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tạo khóa học mới
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Khóa học</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Danh mục</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Học viên</th>
                <th class="text-left px-6 py-4 font-semibold text-slate-600">Trạng thái</th>
                <th class="text-right px-6 py-4 font-semibold text-slate-600">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($courses as $course)
                @php
                    $statusMap = ['draft' => ['Nháp', 'bg-slate-100 text-slate-600'], 'pending' => ['Chờ duyệt', 'bg-amber-100 text-amber-700'], 'published' => ['Đã xuất bản', 'bg-emerald-100 text-emerald-700'], 'rejected' => ['Từ chối', 'bg-red-100 text-red-700']];
                    [$statusLabel, $statusClass] = $statusMap[$course->status] ?? ['', ''];
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-medium text-slate-900">{{ $course->title }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $course->category?->name }}</td>
                    <td class="px-6 py-4">{{ $course->enrollments_count }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('instructor.courses.edit', $course) }}" class="text-emerald-600 hover:underline text-xs font-medium">Sửa</a>
                        <a href="{{ route('instructor.courses.students', $course) }}" class="text-[#0056D2] hover:underline text-xs font-medium">Học viên</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Chưa có khóa học nào.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-slate-100">{{ $courses->links() }}</div>
</div>

</x-instructor-layout>
