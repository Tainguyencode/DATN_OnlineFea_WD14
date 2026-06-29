<x-admin-layout title="Doanh thu" page-title="Thống kê doanh thu">

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    <x-stat-card label="Tổng doanh thu" :value="number_format($totalRevenue, 0, ',', '.') . 'đ'" color="emerald"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
    <x-stat-card label="Tổng đơn hàng" :value="$totalOrders" color="indigo"
        icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>' />
</div>

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    <div class="p-5 border-b border-slate-100">
        <h2 class="font-bold text-slate-900">Doanh thu theo tháng</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Tháng</th>
                <th class="text-left px-6 py-3 font-semibold text-slate-600">Số đơn</th>
                <th class="text-right px-6 py-3 font-semibold text-slate-600">Doanh thu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($monthly as $row)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-medium">{{ $row->month }}</td>
                    <td class="px-6 py-4">{{ $row->count }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-emerald-600">{{ number_format($row->total, 0, ',', '.') }}đ</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-6 py-12 text-center text-slate-500">Chưa có dữ liệu.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

</x-admin-layout>
