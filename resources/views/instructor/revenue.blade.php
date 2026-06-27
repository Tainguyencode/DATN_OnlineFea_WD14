<x-instructor-layout title="Doanh thu" page-title="Báo cáo doanh thu">

<div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 text-white mb-8 shadow-lg">
    <p class="text-emerald-100 text-sm">Tổng doanh thu</p>
    <p class="text-4xl font-bold mt-2">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="p-5 border-b border-slate-100">
        <h2 class="font-bold text-slate-900">Doanh thu theo khóa học</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Khóa học</th>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Lượt bán</th>
                <th class="text-right px-6 py-3 font-semibold text-slate-600">Doanh thu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($courseRevenue as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-medium">{{ $row->course?->title ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $row->sales }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-emerald-600">{{ number_format($row->total, 0, ',', '.') }}đ</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">Chưa có doanh thu.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

</x-instructor-layout>
