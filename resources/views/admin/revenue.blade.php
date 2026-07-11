<x-admin-layout title="Doanh thu" page-title="Thống kê doanh thu">

<form method="GET" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="start_date">Từ ngày</label>
            <input id="start_date" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                   class="w-full h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="end_date">Đến ngày</label>
            <input id="end_date" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                   class="w-full h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="month">Tháng</label>
            <select id="month" name="month" class="w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                <option value="">Tất cả các tháng</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected(($filters['month'] ?? '') == $m)>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="year">Năm</label>
            <select id="year" name="year" class="w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                <option value="">Tất cả các năm</option>
                @for ($y = 2024; $y <= 2030; $y++)
                    <option value="{{ $y }}" @selected(($filters['year'] ?? '') == $y)>Năm {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 h-11 inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 cursor-pointer focus:outline-none focus-visible:ring-4 focus-visible:ring-rose-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .8 1.6L14 13.667V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 8 17v-3.333L3.2 4.6A1 1 0 0 1 3 4Z"/></svg>
                Lọc
            </button>
            <a href="{{ route('admin.revenue') }}" class="h-11 inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-600 transition-colors duration-200 hover:bg-slate-50 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
                Xóa lọc
            </a>
        </div>
    </div>
</form>

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
