<x-instructor-layout title="Doanh thu" page-title="Báo cáo doanh thu">

<form method="GET" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm mb-6">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="start_date">Từ ngày</label>
            <input id="start_date" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                   class="w-full h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-emerald-300 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="end_date">Đến ngày</label>
            <input id="end_date" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                   class="w-full h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-emerald-300 focus:bg-white focus:ring-4 focus:ring-emerald-100">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="month">Tháng</label>
            <select id="month" name="month" class="w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100">
                <option value="">Tất cả các tháng</option>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected(($filters['month'] ?? '') == $m)>Tháng {{ $m }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="year">Năm</label>
            <select id="year" name="year" class="w-full h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-emerald-300 focus:ring-4 focus:ring-emerald-100">
                <option value="">Tất cả các năm</option>
                @for ($y = 2024; $y <= 2030; $y++)
                    <option value="{{ $y }}" @selected(($filters['year'] ?? '') == $y)>Năm {{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 h-11 inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 cursor-pointer focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .8 1.6L14 13.667V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 8 17v-3.333L3.2 4.6A1 1 0 0 1 3 4Z"/></svg>
                Lọc
            </button>
            <a href="{{ route('instructor.revenue') }}" class="h-11 inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 text-sm font-bold text-slate-600 transition-colors duration-200 hover:bg-slate-50 cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
                Xóa lọc
            </a>
        </div>
    </div>
</form>

<div class="bg-emerald-600 rounded-xl p-8 text-white mb-8 shadow-sm">
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
