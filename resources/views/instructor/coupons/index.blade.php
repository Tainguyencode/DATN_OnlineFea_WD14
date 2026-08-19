<x-instructor-layout title="Mã giảm giá cá nhân" pageTitle="Quản lý mã giảm giá" breadcrumb="Mã giảm giá">
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Danh sách mã giảm giá của bạn</h2>
                <p class="mt-1 text-sm text-slate-500">Tạo mã ưu đãi để khuyến khích học viên mua khóa học. Tiền giảm giá sẽ trừ trực tiếp vào phần thu nhập của bạn.</p>
            </div>
            <a href="{{ route('instructor.coupons.create') }}"
               class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-emerald-600/30 transition hover:bg-emerald-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tạo mã mới
            </a>
        </div>

        <!-- Stat Cards -->
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng số mã</div>
                <div class="mt-2 text-2xl font-black text-slate-900 dark:text-white">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Đang hoạt động</div>
                <div class="mt-2 text-2xl font-black text-emerald-600">{{ number_format($stats['active']) }}</div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Tạm dừng</div>
                <div class="mt-2 text-2xl font-black text-slate-400">{{ number_format($stats['inactive']) }}</div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 dark:bg-slate-800/50 dark:text-slate-400">
                        <tr>
                            <th class="px-6 py-4 font-bold">Mã giảm giá</th>
                            <th class="px-6 py-4 font-bold">Loại & Giá trị</th>
                            <th class="px-6 py-4 font-bold">Phạm vi áp dụng</th>
                            <th class="px-6 py-4 font-bold">Đã dùng / Tối đa</th>
                            <th class="px-6 py-4 font-bold">Thời hạn</th>
                            <th class="px-6 py-4 font-bold">Trạng thái</th>
                            <th class="px-6 py-4 text-right font-bold">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition duration-150">
                                <td class="px-6 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $coupon->code }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800 dark:text-slate-200">
                                    @if($coupon->type === 'percent' || $coupon->type === 'percentage')
                                        Giảm {{ (float)$coupon->value }}%
                                    @else
                                        Giảm {{ number_format($coupon->value, 0, ',', '.') }}đ
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if($coupon->course)
                                        <span class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                                            {{ Str::limit($coupon->course->title, 25) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                                            Tất cả khóa học của tôi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-medium">
                                    {{ number_format($coupon->used_count) }} / {{ $coupon->max_uses ? number_format($coupon->max_uses) : '∞' }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if($coupon->expires_at)
                                        @if($coupon->expires_at->isPast())
                                            <span class="text-rose-600 font-bold">Hết hạn ({{ $coupon->expires_at->format('d/m/Y') }})</span>
                                        @else
                                            <span>Hạn: {{ $coupon->expires_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    @else
                                        <span class="text-slate-400">Không giới hạn</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($coupon->is_active && ($coupon->expires_at === null || $coupon->expires_at->isFuture()))
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Đang chạy
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                            Tạm dừng
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('instructor.coupons.edit', $coupon) }}"
                                           class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('instructor.coupons.toggle-status', $coupon) }}" class="inline-flex">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800">
                                                {{ $coupon->is_active ? 'Tắt' : 'Bật' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('instructor.coupons.destroy', $coupon) }}" class="inline-flex" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900/60 dark:hover:bg-rose-950/50">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    Chưa có mã giảm giá nào. Hãy bấm <strong>Tạo mã mới</strong> để bắt đầu!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($coupons->hasPages())
                <div class="border-t border-slate-100 px-6 py-4 dark:border-slate-800">
                    {{ $coupons->links() }}
                </div>
            @endif
        </div>
    </div>
</x-instructor-layout>
