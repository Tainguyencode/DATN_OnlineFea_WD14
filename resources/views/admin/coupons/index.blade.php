<x-admin-layout title="Mã giảm giá" page-title="Mã giảm giá" :breadcrumb="$stats['total'].' mã giảm giá'">

<div class="space-y-5">
    <section class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Tổng mã giảm giá</span>
            <strong class="mt-2 block text-2xl font-bold text-slate-950">{{ number_format($stats['total']) }}</strong>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Đang kích hoạt</span>
            <strong class="mt-2 block text-2xl font-bold text-emerald-600">{{ number_format($stats['active']) }}</strong>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Đang tạm tắt</span>
            <strong class="mt-2 block text-2xl font-bold text-slate-500">{{ number_format($stats['inactive']) }}</strong>
        </div>
    </section>

    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <form method="GET" class="flex-1 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-3 lg:grid-cols-[minmax(220px,1.5fr)_minmax(180px,1fr)_auto]">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm theo mã giảm giá (VD: WELCOME)..."
                       class="h-11 rounded-lg border border-slate-200 bg-slate-50 px-3 text-sm text-slate-700 outline-none transition-colors duration-200 placeholder:text-slate-400 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-100">

                <select name="status" class="h-11 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 outline-none transition-colors duration-200 focus:border-rose-300 focus:ring-4 focus:ring-rose-100">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" @selected($status === 'active')>Đang bật</option>
                    <option value="inactive" @selected($status === 'inactive')>Đang tắt</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-rose-600 px-4 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700">Lọc</button>
                    <a href="{{ route('admin.coupons.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-200 px-3 text-sm font-bold text-slate-600 transition-colors duration-200 hover:bg-slate-50">Xóa</a>
                </div>
            </div>
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.coupons.reward_config') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 shadow-sm">
                🏆 Cấu hình thưởng TOP
            </a>
            <a href="{{ route('admin.coupons.reward_history') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                📜 Lịch sử thưởng TOP
            </a>
            <a href="{{ route('admin.coupons.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800">
                Thêm mã giảm giá
            </a>
        </div>
    </div>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto p-3 sm:p-4">
            <table class="w-full min-w-[960px] text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="rounded-l-lg px-4 py-3 text-left font-semibold text-slate-600">Mã</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Người tạo</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Loại giảm</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Giá trị giảm</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Đơn tối thiểu</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Đã dùng / Tối đa</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Thời gian hiệu lực</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600 whitespace-nowrap">Trạng thái</th>
                        <th class="rounded-r-lg px-4 py-3 text-right font-semibold text-slate-600">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $coupon)
                        <tr class="transition-colors duration-150 hover:bg-slate-50/80">
                            <td class="px-4 py-3 align-middle font-mono font-bold text-slate-950">
                                <div class="flex items-center gap-1.5">
                                    <span class="bg-slate-100 border border-slate-200 text-slate-800 px-2 py-1 rounded text-xs">
                                        {{ $coupon->code }}
                                    </span>
                                    @if($coupon->is_private)
                                        <span class="inline-flex items-center rounded-md bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-200" title="Voucher riêng tư dành cho học viên">
                                            🔒 Riêng tư
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-slate-50 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500 border border-slate-200">
                                            🌐 Công khai
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle text-xs font-semibold">
                                @if($coupon->isInstructorCoupon())
                                    <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-1 font-bold text-emerald-700">
                                        Giảng viên: {{ $coupon->instructor?->name ?? 'GV #'.$coupon->instructor_id }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2 py-1 font-bold text-indigo-700">
                                        Sàn (Admin)
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle text-slate-600">
                                @if($coupon->type === 'percent')
                                    Giảm theo %
                                @else
                                    Số tiền cố định
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle text-right font-bold text-slate-950">
                                @if($coupon->type === 'percent')
                                    {{ number_format($coupon->value, 0) }}%
                                @else
                                    {{ number_format($coupon->value, 0, ',', '.') }}đ
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle text-right text-slate-700">
                                {{ number_format($coupon->min_order_amount, 0, ',', '.') }}đ
                            </td>
                            <td class="px-4 py-3 text-center align-middle font-semibold text-slate-900">
                                {{ $coupon->used_count }} / {{ $coupon->max_uses ?? 'Vô hạn' }}
                            </td>
                            <td class="px-4 py-3 text-center align-middle text-xs text-slate-500">
                                @if($coupon->starts_at || $coupon->expires_at)
                                    <div>Bắt đầu: {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y H:i') : 'N/A' }}</div>
                                    <div class="mt-1">Hết hạn: {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'N/A' }}</div>
                                @else
                                    Không giới hạn thời gian
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center align-middle whitespace-nowrap">
                                @php
                                    $isValid = $coupon->isValid();
                                @endphp
                                @if(! $coupon->is_active)
                                    <span class="status-badge status-inactive">Đã tắt</span>
                                @elseif($coupon->starts_at && $coupon->starts_at->isFuture())
                                    <span class="status-badge status-pending">Chờ kích hoạt</span>
                                @elseif($coupon->expires_at && $coupon->expires_at->isPast())
                                    <span class="status-badge status-danger">Hết hạn</span>
                                @elseif($coupon->max_uses && $coupon->used_count >= $coupon->max_uses)
                                    <span class="status-badge status-danger">Hết lượt dùng</span>
                                @else
                                    <span class="status-badge status-active">Hoạt động</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle whitespace-nowrap">
                                <div class="flex flex-row flex-nowrap items-center justify-end gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" title="Sửa" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" class="inline-flex">
                                        @csrf
                                        <button type="submit" title="{{ $coupon->is_active ? 'Tắt mã' : 'Bật mã' }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-100 bg-amber-50 text-amber-700 transition-colors duration-200 hover:bg-amber-100">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.828A5 5 0 1116.24 7.76M12 12V3"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="inline-flex" onsubmit="return confirm('Xóa mã giảm giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-700 transition-colors duration-200 hover:bg-rose-100">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-14 text-center">
                                <h3 class="text-base font-bold text-slate-950">Chưa có mã giảm giá nào</h3>
                                <p class="mt-1 text-sm text-slate-500">Tạo mã giảm giá để thu hút học viên mua khóa học.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 bg-slate-50/40 px-5 py-4">{{ $coupons->links() }}</div>
    </section>
</div>

</x-admin-layout>
