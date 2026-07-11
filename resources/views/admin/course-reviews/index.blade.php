<x-admin-layout title="Kiểm duyệt khóa học">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Kiểm duyệt khóa học</h1>
            <p class="mt-1 text-sm text-slate-500">Danh sách khóa học chờ admin xem xét</p>
        </div>
        <form method="GET" class="flex gap-2">
            <select name="status" class="rounded-lg border border-slate-200 px-3 py-2 text-sm" onchange="this.form.submit()">
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Khóa học</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Giảng viên</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Danh mục</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Ngày gửi</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Lần gửi</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($courses as $course)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $course->title }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $course->instructor?->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $course->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $course->submitted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $course->submission_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.course-reviews.show', $course) }}" class="text-sm font-semibold text-blue-600 hover:underline">Xem chi tiết</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">Không có khóa học nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($courses->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">{{ $courses->links() }}</div>
        @endif
    </div>
</x-admin-layout>
