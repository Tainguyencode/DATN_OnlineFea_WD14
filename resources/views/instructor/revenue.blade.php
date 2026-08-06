<x-instructor-layout title="Doanh thu" page-title="Báo cáo doanh thu & Lịch sử mua khóa học">

    <div x-data="{
        showPurchaseModal: false,
        activePurchase: null,
        searchQuery: '',
        copied: false,

        openPurchaseDetail(item) {
            this.activePurchase = item;
            this.showPurchaseModal = true;
        },
        copyCode(code) {
            if (navigator.clipboard && code) {
                navigator.clipboard.writeText(code);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        }
    }" class="space-y-6">

        {{-- Filter Form --}}
        <form method="GET" class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="start_date">Từ ngày</label>
                    <input id="start_date" type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                           class="w-full h-11 rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="end_date">Đến ngày</label>
                    <input id="end_date" type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                           class="w-full h-11 rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-200">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="month">Tháng</label>
                    <select id="month" name="month" class="w-full h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        <option value="">Tất cả các tháng</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected(($filters['month'] ?? '') == $m)>Tháng {{ $m }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2" for="year">Năm</label>
                    <select id="year" name="year" class="w-full h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        <option value="">Tất cả các năm</option>
                        @for ($y = 2024; $y <= 2030; $y++)
                            <option value="{{ $y }}" @selected(($filters['year'] ?? '') == $y)>Năm {{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 h-11 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-sm font-bold text-white transition hover:bg-emerald-700 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .8 1.6L14 13.667V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 8 17v-3.333L3.2 4.6A1 1 0 0 1 3 4Z"/></svg>
                        Lọc
                    </button>
                    <a href="{{ route('instructor.revenue') }}" class="h-11 inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50 cursor-pointer">
                        Xóa lọc
                    </a>
                </div>
            </div>
        </form>

        {{-- Stat Cards --}}
        <div class="grid gap-6 sm:grid-cols-3">
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Doanh thu gộp (Gross)</p>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalGross, 0, ',', '.') }}đ</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Chiết khấu nền tảng ({{ auth()->user()->getCommissionRate() }}%)</p>
                <p class="text-2xl font-black text-rose-600 mt-2">-{{ number_format($totalCommission, 0, ',', '.') }}đ</p>
            </div>
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-6 text-white shadow-md">
                <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Thực nhận (Net Earnings)</p>
                <p class="text-3xl font-black mt-2">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
            </div>
        </div>

        {{-- Course Revenue Summary Table --}}
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-900 text-base">Tổng quan Doanh thu theo Khóa học</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="text-left px-6 py-3.5">Khóa học</th>
                            <th class="text-center px-6 py-3.5">Lượt bán</th>
                            <th class="text-right px-6 py-3.5">Doanh thu gộp</th>
                            <th class="text-right px-6 py-3.5">Chiết khấu</th>
                            <th class="text-right px-6 py-3.5">Thực nhận</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($courseRevenue as $row)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $row->course?->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700">
                                    <span class="inline-block bg-slate-100 border border-slate-200 rounded-lg px-2.5 py-0.5 text-xs">
                                        {{ $row->sales }} lượt
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-slate-700 font-semibold">{{ number_format($row->gross ?? $row->total, 0, ',', '.') }}đ</td>
                                <td class="px-6 py-4 text-right text-rose-600 font-semibold">-{{ number_format($row->commission ?? 0, 0, ',', '.') }}đ</td>
                                <td class="px-6 py-4 text-right font-black text-emerald-600">{{ number_format($row->total, 0, ',', '.') }}đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Chưa có doanh thu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Detailed Student Purchases Table --}}
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        🛒 Lịch sử Học viên Mua khóa học & Giao dịch
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                            {{ $studentPurchases->count() }} giao dịch
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Theo dõi lịch sử đơn hàng thanh toán của học viên trong hệ thống</p>
                </div>

                {{-- Real-time Quick Filter Input --}}
                <div class="relative w-72">
                    <input
                        type="text"
                        x-model="searchQuery"
                        placeholder="Tìm kiếm mã đơn, tên học viên..."
                        class="w-full text-xs font-medium rounded-xl border-slate-300 py-2.5 pl-9 pr-3 text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-3.5">Mã Đơn hàng</th>
                            <th class="px-6 py-3.5">Học viên mua</th>
                            <th class="px-6 py-3.5">Khóa học</th>
                            <th class="px-6 py-3.5 text-right">Giá mua</th>
                            <th class="px-6 py-3.5 text-right">Thực nhận</th>
                            <th class="px-6 py-3.5 text-center">Cổng thanh toán</th>
                            <th class="px-6 py-3.5">Thời gian mua</th>
                            <th class="px-6 py-3.5 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($studentPurchases as $item)
                            <tr
                                x-show="!searchQuery || '{{ strtolower($item->order_code . ' ' . ($item->user?->name ?? '') . ' ' . ($item->user?->email ?? '') . ' ' . $item->course_title) }}'.includes(searchQuery.toLowerCase())"
                                class="hover:bg-slate-50/80 transition"
                            >
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                    {{ $item->order_code }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 bg-emerald-100 text-emerald-800 font-black rounded-xl flex items-center justify-center text-xs shrink-0 shadow-xs">
                                            {{ strtoupper(substr($item->user?->name ?? 'H', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block text-sm">{{ $item->user?->name ?? 'Học viên' }}</span>
                                            <span class="text-[11px] text-slate-500 font-mono">{{ $item->user?->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 max-w-[220px] truncate">
                                    {{ $item->course_title }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900">
                                    {{ number_format($item->price, 0, ',', '.') }}đ
                                </td>
                                <td class="px-6 py-4 text-right font-black text-emerald-600">
                                    +{{ number_format($item->instructor_earning, 0, ',', '.') }}đ
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-[11px] font-bold text-slate-800 shadow-2xs">
                                        💳 {{ $item->payment_method }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 font-medium">
                                    {{ $item->purchased_at ? $item->purchased_at->format('H:i - d/m/Y') : '' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        type="button"
                                        @click="openPurchaseDetail({{ json_encode([
                                            'order_code' => $item->order_code,
                                            'student_name' => $item->user?->name ?? 'Học viên',
                                            'student_email' => $item->user?->email ?? '',
                                            'course_title' => $item->course_title,
                                            'price' => number_format($item->price, 0, ',', '.') . 'đ',
                                            'commission' => number_format($item->commission_amount, 0, ',', '.') . 'đ (' . auth()->user()->getCommissionRate() . '%)',
                                            'earning' => number_format($item->instructor_earning, 0, ',', '.') . 'đ',
                                            'payment_method' => $item->payment_method,
                                            'purchased_at' => $item->purchased_at ? $item->purchased_at->format('H:i:s - d/m/Y') : '',
                                        ]) }})"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem chi tiết
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                                    </svg>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Chưa có giao dịch mua nào</p>
                                    <p class="mt-1 text-xs text-slate-400">Các lượt học viên thanh toán khóa học sẽ xuất hiện ở đây.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Chi tiết Đơn hàng Học viên mua --}}
        <div
            x-show="showPurchaseModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="purchase-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="showPurchaseModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="showPurchaseModal = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                <div
                    x-show="showPurchaseModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-base shadow-xs">
                                🛍️
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900" id="purchase-modal-title">
                                    Chi tiết Đơn hàng & Thanh toán
                                </h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-slate-500 font-mono" x-text="activePurchase ? activePurchase.order_code : ''"></span>
                                    <button
                                        type="button"
                                        @click="copyCode(activePurchase?.order_code)"
                                        class="text-[10px] font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded cursor-pointer"
                                    >
                                        <span x-text="copied ? 'Đã sao chép!' : 'Sao chép'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="showPurchaseModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <template x-if="activePurchase">
                        <div class="mt-5 space-y-4">
                            {{-- Earning Banner --}}
                            <div class="rounded-2xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-800 p-5 text-white shadow-md text-center">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-100 block">Thu nhập thực nhận của bạn</span>
                                <span class="text-3xl font-black mt-1 block tracking-tight" x-text="'+' + activePurchase.earning"></span>
                                <span class="text-[11px] text-emerald-200 mt-1 block">Số tiền đã tự động cộng vào Ví dư khả dụng</span>
                            </div>

                            {{-- Purchase Specs --}}
                            <div class="rounded-2xl border border-slate-200 divide-y divide-slate-100 bg-slate-50/50 text-xs">
                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Tên học viên:</span>
                                    <span class="font-bold text-slate-900 text-sm" x-text="activePurchase.student_name"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Email tài khoản:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="activePurchase.student_email"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Khóa học đăng ký:</span>
                                    <span class="font-bold text-slate-900 truncate max-w-[200px]" x-text="activePurchase.course_title"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Giá niêm yết (Gross):</span>
                                    <span class="font-bold text-slate-900 text-sm" x-text="activePurchase.price"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Chiết khấu nền tảng:</span>
                                    <span class="font-bold text-rose-600" x-text="'-' + activePurchase.commission"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Cổng thanh toán:</span>
                                    <span class="font-mono font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded shadow-2xs" x-text="activePurchase.payment_method"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Thời gian thanh toán:</span>
                                    <span class="text-slate-700 font-medium" x-text="activePurchase.purchased_at"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="mt-6 flex justify-end">
                        <button
                            type="button"
                            @click="showPurchaseModal = false"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition cursor-pointer"
                        >
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-instructor-layout>
