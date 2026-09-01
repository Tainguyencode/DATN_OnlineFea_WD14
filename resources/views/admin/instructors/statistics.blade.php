<x-admin-layout title="Thống kê giảng viên" page-title="Thống kê giảng viên" breadcrumb="Quản lý người dùng / Thống kê giảng viên">
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">Thống kê giảng viên</h2>
                <p class="mt-1 text-sm text-slate-500">Theo dõi tình hình hồ sơ và đăng ký giảng viên.</p>
            </div>
            <a href="{{ route('admin.instructors.applications.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">Quản lý giảng viên</a>
        </div>

        <form method="GET" action="{{ route('admin.instructors.statistics') }}" class="grid grid-cols-1 items-end gap-4 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-2 lg:grid-cols-6 dark:border-slate-800 dark:bg-slate-900">
            <div class="space-y-1"><label for="date_from" class="text-xs font-bold uppercase tracking-wider text-slate-500">Từ ngày</label><input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">@error('date_from')<p class="text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
            <div class="space-y-1"><label for="date_to" class="text-xs font-bold uppercase tracking-wider text-slate-500">Đến ngày</label><input type="date" id="date_to" name="date_to" value="{{ $dateTo }}" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">@error('date_to')<p class="text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
            <div class="space-y-1"><label for="month" class="text-xs font-bold uppercase tracking-wider text-slate-500">Theo tháng</label><input type="month" id="month" name="month" value="{{ $month }}" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">@error('month')<p class="text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
            <div class="space-y-1"><label for="week" class="text-xs font-bold uppercase tracking-wider text-slate-500">Theo tuần</label><input type="week" id="week" name="week" value="{{ $week }}" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">@error('week')<p class="text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror</div>
            <div class="space-y-1"><label for="growth_year" class="text-xs font-bold uppercase tracking-wider text-slate-500">Xem theo năm</label><select id="growth_year" name="growth_year" class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">@foreach($growthYears as $year)<option value="{{ $year }}" @selected($growthYear === $year)>{{ $year }}</option>@endforeach</select></div>
            <div class="flex gap-2"><button class="flex-1 rounded-xl bg-[#0056D2] py-2 text-xs font-bold text-white hover:bg-blue-700">Áp dụng</button><a href="{{ route('admin.instructors.statistics') }}" class="flex-1 rounded-xl border border-slate-300 py-2 text-center text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300">Làm mới</a></div>
        </form>

        <div class="grid gap-5 xl:grid-cols-[minmax(0,1.55fr)_minmax(320px,.75fr)]">
            <section class="rounded-2xl border border-blue-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between"><div><h3 class="text-base font-black text-slate-950 dark:text-white">Biểu đồ tăng trưởng giảng viên</h3><p class="mt-1 text-xs text-slate-400">Dữ liệu đăng ký mới và phê duyệt theo 12 tháng của năm {{ $growthYear }}</p></div><div class="flex gap-2"><div class="rounded-xl bg-blue-50 px-3 py-2 dark:bg-blue-500/10"><p class="text-[10px] font-semibold text-slate-400">Năm {{ $growthYear }}</p><p class="mt-0.5 text-sm font-black text-slate-900 dark:text-white">+{{ $yearRegistered }} giảng viên</p></div><div class="rounded-xl px-3 py-2 {{ $growthRate >= 0 ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-red-50 dark:bg-red-500/10' }}"><p class="text-[10px] font-semibold text-slate-400">So với năm {{ $growthYear - 1 }}</p><p class="mt-0.5 text-sm font-black {{ $growthRate >= 0 ? 'text-emerald-600 dark:text-emerald-300' : 'text-red-600 dark:text-red-300' }}">{{ $growthRate >= 0 ? '↑' : '↓' }} {{ number_format(abs($growthRate), 1, ',', '.') }}%</p></div></div></div>
                <div class="mt-5 h-[300px]"><canvas id="instructorGrowthChart" role="img" aria-label="Biểu đồ tăng trưởng giảng viên theo tháng"></canvas></div>
            </section>
            <aside class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                @foreach([['Tổng giảng viên', $counts['all'], 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300'], ['Đang chờ duyệt', $counts['pending'], 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300'], ['Có cập nhật mới', $counts['new_updates'], 'bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300'], ['Đã phê duyệt', $counts['approved'], 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300'], ['Đã từ chối', $counts['rejected'], 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300']] as $card)
                    <div class="flex items-center gap-3 rounded-2xl border border-blue-100 bg-white px-4 py-3.5 shadow-sm dark:border-slate-800 dark:bg-slate-900"><span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $card[2] }} text-sm font-black">{{ $card[1] }}</span><span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $card[0] }}</span></div>
                @endforeach
            </aside>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('instructorGrowthChart');
            if (!window.Chart || !canvas) return;
            const dark = document.documentElement.classList.contains('dark');
            const text = dark ? '#cbd5e1' : '#64748b'; const grid = dark ? 'rgba(148,163,184,.12)' : 'rgba(148,163,184,.18)'; const growthData = @json($growthData);
            new Chart(canvas, { type: 'bar', data: { labels: growthData.map(item => item.label), datasets: [{ type: 'bar', label: 'Đăng ký mới', data: growthData.map(item => item.registered), backgroundColor: '#3b82f6', borderRadius: 6, maxBarThickness: 28 }, { type: 'bar', label: 'Được phê duyệt', data: growthData.map(item => item.approved), backgroundColor: '#22c55e', borderRadius: 6, maxBarThickness: 28 }, { type: 'line', label: 'Tổng tích lũy', data: growthData.map(item => item.cumulative), borderColor: '#1e3a8a', borderWidth: 2, pointRadius: 3, tension: .35, yAxisID: 'y1' }] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { labels: { color: text, usePointStyle: true, boxWidth: 9, font: { size: 10 } } }, tooltip: { callbacks: { title: items => growthData[items[0].dataIndex]?.full_label ?? items[0].label } } }, scales: { x: { ticks: { color: text }, grid: { display: false } }, y: { beginAtZero: true, ticks: { color: text, precision: 0 }, grid: { color: grid } }, y1: { beginAtZero: true, position: 'right', ticks: { color: text, precision: 0 }, grid: { display: false } } } } });
        });
    </script>
</x-admin-layout>
