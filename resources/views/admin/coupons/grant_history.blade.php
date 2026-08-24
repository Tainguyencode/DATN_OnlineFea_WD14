<x-admin-layout title="Lịch sử tặng Voucher" page-title="Lịch sử Admin tặng Voucher" breadcrumb="Danh sách mã giảm giá đã tặng cho học viên">

<div class="space-y-5">
    <!-- Header Action bar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <span>📜</span> Lịch sử Admin tặng Voucher
            </h2>
            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Theo dõi các lượt voucher được Admin cấp trực tiếp cho Học viên</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.coupons.grant') }}"
               class="inline-flex h-10 items-center justify-center rounded-lg bg-rose-600 px-4 text-xs font-bold text-white shadow-sm transition hover:bg-rose-700">
                Tặng mã giảm giá
            </a>
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 transition hover:bg-slate-50">
                Quản lý mã giảm giá
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 dark:bg-slate-800/50 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Học viên</th>
                        <th class="px-5 py-3.5">Voucher / Giá trị</th>
                        <th class="px-5 py-3.5">Mã voucher</th>
                        <th class="px-5 py-3.5">Lý do tặng</th>
                        <th class="px-5 py-3.5">Người tặng</th>
                        <th class="px-5 py-3.5">Thời gian tặng</th>
                        <th class="px-5 py-3.5 text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($grants as $grant)
                        @php
                            $coupon = $grant->coupon;
                            $user = $grant->user;
                            $grantedBy = $grant->grantedBy;
                            $isUsed = $grant->used_at !== null;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition">
                            <!-- Student -->
                            <td class="px-5 py-4">
                                @if($user)
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                @else
                                    <span class="text-xs text-slate-400">N/A</span>
                                @endif
                            </td>

                            <!-- Coupon details -->
                            <td class="px-5 py-4">
                                @if($coupon)
                                    <div class="font-bold text-indigo-600 dark:text-indigo-400">
                                        @if($coupon->type === 'percent')
                                            Giảm {{ (int) $coupon->value }}%
                                        @else
                                            Giảm {{ number_format($coupon->value, 0, ',', '.') }}đ
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        Đơn từ {{ number_format($coupon->min_order_amount, 0, ',', '.') }}đ
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Đã xóa</span>
                                @endif
                            </td>

                            <!-- Code -->
                            <td class="px-5 py-4">
                                @if($coupon)
                                    <span class="font-mono text-xs font-black tracking-wider text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-700">
                                        {{ $coupon->code }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">N/A</span>
                                @endif
                            </td>

                            <!-- Reason -->
                            <td class="px-5 py-4 max-w-xs">
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                    {{ $grant->reason ?? 'Không có' }}
                                </span>
                            </td>

                            <!-- Granted By -->
                            <td class="px-5 py-4">
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ $grantedBy->name ?? 'Admin' }}
                                </span>
                            </td>

                            <!-- Granted At -->
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-600 dark:text-slate-400">
                                {{ $grant->granted_at ? $grant->granted_at->format('d/m/Y H:i') : ($grant->created_at ? $grant->created_at->format('d/m/Y H:i') : '-') }}
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if($isUsed)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        ⚫ Đã dùng
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        🟢 Chưa dùng
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500 dark:text-slate-400">
                                Chưa có lịch sử tặng voucher nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($grants->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $grants->links() }}
            </div>
        @endif
    </div>
</div>

</x-admin-layout>
