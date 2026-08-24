<x-admin-layout title="Lịch sử trao thưởng TOP Tháng" page-title="Lịch sử trao thưởng TOP Bảng Xếp Hạng Tháng" breadcrumb="Mã giảm giá">

<div class="mx-auto max-w-6xl space-y-6">

    {{-- Tabs Navigation --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-white p-3 rounded-lg shadow-sm">
        <a href="{{ route('admin.coupons.reward_config') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
            <span>🏆</span> Cấu hình thưởng TOP tháng
        </a>
        <a href="{{ route('admin.coupons.reward_history') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-bold text-white shadow-sm">
            <span>📜</span> Lịch sử thưởng TOP
        </a>
        <a href="{{ route('admin.coupons.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 transition ml-auto">
            <span>🎫</span> Quản lý Voucher công khai
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Danh sách nhật ký trao thưởng tự động hàng tháng</h2>
            <span class="text-xs text-slate-500">Tự động chốt và ghi nhận qua Artisan Command</span>
        </div>

        @if($rewards->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <div class="text-3xl mb-2">📜</div>
                <p class="font-bold text-sm text-slate-700">Chưa có lịch sử trao thưởng TOP tháng nào</p>
                <p class="text-xs mt-1">Khi tiến trình chạy vào đầu tháng mới, kết quả trao thưởng cho TOP 1, TOP 2, TOP 3 sẽ xuất hiện ở đây.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase text-slate-700 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Kỳ (Tháng)</th>
                            <th class="px-5 py-3.5">Học viên</th>
                            <th class="px-5 py-3.5 text-center">Thành tích</th>
                            <th class="px-5 py-3.5">Mã Voucher</th>
                            <th class="px-5 py-3.5 text-right">Giá trị</th>
                            <th class="px-5 py-3.5 text-center">Thời gian trao</th>
                            <th class="px-5 py-3.5 text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rewards as $item)
                            @php
                                $user = $item->user;
                                $coupon = $item->coupon;
                                $userCoupon = $item->userCoupon;

                                $periodObj = \Illuminate\Support\Carbon::createFromFormat('Y-m', $item->period_key);
                                $periodLabel = $periodObj ? $periodObj->format('m/Y') : $item->period_key;

                                $isUsed = $userCoupon && $userCoupon->used_at !== null;
                                $isExpired = !$isUsed && $coupon && $coupon->expires_at && $coupon->expires_at->isPast();

                                $rankLabel = match((int) $item->rank) {
                                    1 => '🥇 TOP 1',
                                    2 => '🥈 TOP 2',
                                    3 => '🥉 TOP 3',
                                    default => 'TOP ' . $item->rank,
                                };

                                $rankBg = match((int) $item->rank) {
                                    1 => 'bg-amber-100 text-amber-900 border-amber-300',
                                    2 => 'bg-slate-200 text-slate-900 border-slate-300',
                                    3 => 'bg-amber-800/10 text-amber-800 border-amber-700/30',
                                    default => 'bg-slate-100 text-slate-700',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                {{-- Kỳ --}}
                                <td class="px-5 py-4 font-mono font-bold text-slate-900">
                                    {{ $periodLabel }}
                                </td>

                                {{-- Học viên --}}
                                <td class="px-5 py-4">
                                    @if($user)
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-9 w-9 rounded-full object-cover border border-slate-200">
                                            <div>
                                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Học viên đã xóa</span>
                                    @endif
                                </td>

                                {{-- Thành tích --}}
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-black shadow-2xs {{ $rankBg }}">
                                        {{ $rankLabel }}
                                    </span>
                                </td>

                                {{-- Voucher Code --}}
                                <td class="px-5 py-4 font-mono font-bold text-slate-900">
                                    @if($coupon)
                                        <span class="bg-slate-100 border border-slate-200 px-2 py-1 rounded text-xs select-all">
                                            {{ $coupon->code }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Mã đã xóa</span>
                                    @endif
                                </td>

                                {{-- Giá trị --}}
                                <td class="px-5 py-4 text-right font-extrabold text-rose-600">
                                    @if($item->discount_type === 'percent')
                                        {{ (int) $item->discount_value }}%
                                    @else
                                        {{ number_format($item->discount_value, 0, ',', '.') }}đ
                                    @endif
                                </td>

                                {{-- Thời gian --}}
                                <td class="px-5 py-4 text-center text-xs font-medium text-slate-500">
                                    {{ $item->granted_at ? $item->granted_at->format('d/m/Y H:i') : '--' }}
                                </td>

                                {{-- Trạng thái --}}
                                <td class="px-5 py-4 text-center">
                                    @if($isUsed)
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 border border-slate-200">
                                            Đã dùng
                                        </span>
                                    @elseif($isExpired)
                                        <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 border border-rose-200">
                                            Hết hạn
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                            Chưa dùng
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $rewards->links() }}
            </div>
        @endif
    </div>

</div>

</x-admin-layout>
