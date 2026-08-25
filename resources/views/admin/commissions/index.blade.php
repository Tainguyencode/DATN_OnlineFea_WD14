<x-admin-layout title="Quản lý chiết khấu" pageTitle="Quản lý tỷ lệ chiết khấu nền tảng">
    <div x-data="{
        editModalOpen: false,
        instructorId: null,
        instructorName: '',
        currentRate: '',
        isCustom: false,
        defaultRate: {{ $stats['default_rate'] }},
        openModal(id, name, rate, custom) {
            this.instructorId = id;
            this.instructorName = name;
            this.currentRate = rate !== null && rate !== '' ? rate : '';
            this.isCustom = custom;
            this.editModalOpen = true;
        }
    }" class="space-y-6">

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="rounded-lg bg-rose-500/10 p-2 text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">Đã có lỗi xảy ra:</h4>
                        <ul class="mt-1 list-inside list-disc text-sm space-y-1 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Stat 1: Total Platform Commission --}}
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Lợi nhuận chiết khấu nền tảng</span>
                    <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['total_commission'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-medium text-slate-500">Từ tổng doanh số {{ number_format($stats['total_gross'], 0, ',', '.') }}đ</p>
                </div>
            </div>

            {{-- Stat 2: Total Instructor Earnings --}}
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Thu nhập chi trả Giảng viên</span>
                    <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['total_instructor_earnings'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-medium text-slate-500">Chi trả đối soát thực tế</p>
                </div>
            </div>

            {{-- Stat 3: Default System Rate --}}
            <div class="relative overflow-hidden rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50/70 to-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Tỷ lệ chiết khấu mặc định</span>
                    <div class="rounded-xl bg-rose-500/10 p-2.5 text-rose-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline justify-between">
                    <span class="text-3xl font-black text-rose-600">{{ number_format($stats['default_rate'], 1) }}%</span>
                    <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-700">Hệ thống</span>
                </div>
            </div>

            {{-- Stat 4: Instructor Rate Counts --}}
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng số Giảng viên</span>
                    <div class="rounded-xl bg-violet-50 p-2.5 text-violet-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['total_instructors']) }}</span>
                    <p class="mt-1 text-xs font-medium text-slate-500">
                        <strong class="font-bold text-violet-600">{{ $stats['custom_rate_count'] }}</strong> giảng viên dùng tỷ lệ riêng
                    </p>
                </div>
            </div>
        </div>

        {{-- Configuration Section: System Default Rate --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Tỷ lệ Chiết khấu Mặc định Toàn Hệ thống
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">Tỷ lệ này sẽ áp dụng cho tất cả giảng viên chưa thiết lập mức chiết khấu tùy chỉnh riêng.</p>
                </div>

                <form method="POST" action="{{ route('admin.commissions.update-default') }}" class="flex items-center gap-3">
                    @csrf
                    <div class="relative w-36">
                        <input
                            type="number"
                            name="default_commission_rate"
                            step="0.01"
                            min="0"
                            max="100"
                            value="{{ old('default_commission_rate', $stats['default_rate']) }}"
                            required
                            class="w-full rounded-xl border-slate-300 py-2.5 pl-4 pr-8 text-sm font-bold text-slate-900 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                        />
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 font-bold text-slate-400">%</span>
                    </div>

                    <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-rose-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                        Lưu tỷ lệ
                    </button>
                </form>
            </div>
        </div>

        {{-- Instructors Commission List & Filter --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 p-5 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Danh sách Chiết khấu theo Giảng viên</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Quản lý tỷ lệ chiết khấu chi tiết và thông tin tài khoản ngân hàng đối soát</p>
                </div>

                {{-- Filters & Search --}}
                <form method="GET" action="{{ route('admin.commissions.index') }}" class="mt-4 sm:mt-0 flex flex-wrap items-center gap-3">
                    {{-- Search --}}
                    <div class="relative min-w-64">
                        <input
                            type="text"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="Tìm kiếm giảng viên..."
                            class="w-full rounded-xl border-slate-300 py-2 pl-9 pr-4 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:border-rose-500 focus:ring-rose-500"
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    {{-- Filter Tabs --}}
                    <select name="rate_filter" onchange="this.form.submit()" class="rounded-xl border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700 focus:border-rose-500 focus:ring-rose-500">
                        <option value="">Tất cả tỷ lệ</option>
                        <option value="custom" {{ $filters['rate_filter'] === 'custom' ? 'selected' : '' }}>Tỷ lệ riêng (Custom)</option>
                        <option value="default" {{ $filters['rate_filter'] === 'default' ? 'selected' : '' }}>Tỷ lệ mặc định (System)</option>
                    </select>

                    <button type="submit" class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        Lọc
                    </button>
                    @if ($filters['search'] || $filters['rate_filter'])
                        <a href="{{ route('admin.commissions.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">Xóa lọc</a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Giảng viên</th>
                            <th class="px-4 py-3.5 text-center">Tỷ lệ chiết khấu</th>
                            <th class="px-4 py-3.5 text-right">Tổng doanh số</th>
                            <th class="px-4 py-3.5 text-right">Chiết khấu nền tảng</th>
                            <th class="px-4 py-3.5 text-right">Thực nhận GV</th>
                            <th class="px-5 py-3.5">Tài khoản Ngân hàng</th>
                            <th class="px-5 py-3.5 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($instructors as $instructor)
                            @php
                                $rate = $instructor->getCommissionRate();
                                $hasCustom = $instructor->commission_rate !== null;
                                $salesInfo = $instructorSalesData[$instructor->id] ?? ['total_sales' => 0, 'total_commission' => 0, 'total_earning' => 0, 'total_orders' => 0];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                {{-- User Info --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 shrink-0 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($instructor->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $instructor->id) }}" class="font-bold text-slate-900 hover:text-rose-600 transition">
                                                {{ $instructor->name }}
                                            </a>
                                            <p class="text-slate-500 font-normal">{{ $instructor->email }}</p>
                                            <span class="inline-block mt-0.5 text-[11px] font-semibold text-slate-400">
                                                {{ $instructor->courses_count }} khóa học
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Rate Badge --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="inline-flex flex-col items-center gap-1">
                                        <span class="text-sm font-black {{ $hasCustom ? 'text-violet-600' : 'text-slate-700' }}">
                                            {{ number_format($rate, 1) }}%
                                        </span>
                                        @if ($hasCustom)
                                            <span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-bold text-violet-700">Tỷ lệ riêng</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Mặc định</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Sales Amount --}}
                                <td class="px-4 py-4 text-right font-bold text-slate-900">
                                    {{ number_format($salesInfo['total_sales'], 0, ',', '.') }}đ
                                </td>

                                {{-- Commission Earned --}}
                                <td class="px-4 py-4 text-right font-bold text-emerald-600">
                                    +{{ number_format($salesInfo['total_commission'], 0, ',', '.') }}đ
                                </td>

                                {{-- Instructor Earning --}}
                                <td class="px-4 py-4 text-right font-bold text-blue-600">
                                    {{ number_format($salesInfo['total_earning'], 0, ',', '.') }}đ
                                </td>

                                {{-- Bank Info --}}
                                <td class="px-5 py-4">
                                    @if ($instructor->bank_name || $instructor->bank_account_number)
                                        <div class="text-xs">
                                            <p class="font-bold text-slate-900">{{ $instructor->bank_name }}</p>
                                            <p class="font-mono text-slate-700">{{ $instructor->bank_account_number }}</p>
                                            <p class="text-slate-500 uppercase text-[11px]">{{ $instructor->bank_account_name }}</p>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Chưa cập nhật</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4 text-center">
                                    <button
                                        type="button"
                                        @click="openModal({{ $instructor->id }}, '{{ addslashes($instructor->name) }}', '{{ $instructor->commission_rate }}', {{ $hasCustom ? 'true' : 'false' }})"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 hover:border-slate-400 focus:outline-none transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        Sửa tỷ lệ
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Không tìm thấy giảng viên nào</p>
                                    <p class="mt-1 text-xs text-slate-500">Thử thay đổi từ khóa hoặc bộ lọc tìm kiếm.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($instructors->hasPages())
                <div class="border-t border-slate-200 p-4">
                    {{ $instructors->links() }}
                </div>
            @endif
        </div>

        {{-- Alpine.js Edit Instructor Commission Modal --}}
        <div
            x-show="editModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                {{-- Backdrop --}}
                <div
                    x-show="editModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="editModalOpen = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                {{-- Modal Box --}}
                <div
                    x-show="editModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-900" id="modal-title">
                            Điều chỉnh tỷ lệ chiết khấu
                        </h3>
                        <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="'/admin/commissions/instructors/' + instructorId" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Giảng viên</label>
                            <p class="mt-1 font-bold text-slate-900 text-sm" x-text="instructorName"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">
                                Tỷ lệ chiết khấu tùy chỉnh (%)
                            </label>
                            <p class="text-[11px] text-slate-500 mb-2">Để trống nếu muốn sử dụng Tỷ lệ mặc định của hệ thống (<span class="font-bold text-rose-600" x-text="defaultRate + '%'"></span>).</p>

                            <div class="relative">
                                <input
                                    type="number"
                                    name="commission_rate"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    x-model="currentRate"
                                    placeholder="Ví dụ: 15.00"
                                    class="w-full rounded-xl border-slate-300 py-2.5 pl-4 pr-8 text-sm font-bold text-slate-900 shadow-sm focus:border-rose-500 focus:ring-rose-500"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 font-bold text-slate-400">%</span>
                            </div>
                        </div>

                        {{-- Button to Reset to Default --}}
                        <div x-show="isCustom" class="pt-1">
                            <button
                                type="button"
                                @click="currentRate = ''"
                                class="text-xs font-bold text-rose-600 hover:text-rose-800 transition underline cursor-pointer"
                            >
                                Quay về Tỷ lệ mặc định ({{ $stats['default_rate'] }}%)
                            </button>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 mt-6">
                            <button
                                type="button"
                                @click="editModalOpen = false"
                                class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition"
                            >
                                Hủy
                            </button>
                            <button
                                type="submit"
                                class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700 transition"
                            >
                                Cập nhật tỷ lệ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
