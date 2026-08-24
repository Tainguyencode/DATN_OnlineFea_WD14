<x-admin-layout title="Cấu hình phần thưởng TOP Tháng" page-title="Cấu hình phần thưởng TOP Bảng Xếp Hạng Tháng" breadcrumb="Mã giảm giá">

<div class="mx-auto max-w-5xl space-y-6">

    {{-- Tabs Navigation --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-white p-3 rounded-lg shadow-sm">
        <a href="{{ route('admin.coupons.reward_config') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-bold text-white shadow-sm">
            <span>🏆</span> Cấu hình thưởng TOP tháng
        </a>
        <a href="{{ route('admin.coupons.reward_history') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">
            <span>📜</span> Lịch sử thưởng TOP
        </a>
        <a href="{{ route('admin.coupons.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50 transition ml-auto">
            <span>🎫</span> Quản lý Voucher công khai
        </a>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <p class="font-bold">Vui lòng kiểm tra lại thông tin cấu hình.</p>
            <ul class="mt-1.5 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.coupons.reward_config.store') }}" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🏆</span> Phần thưởng tự động cho Học viên đạt TOP Bảng Xếp Hạng Tháng
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Hệ thống sẽ tự động chốt TOP vào cuối tháng, tạo mã Voucher riêng biệt cho từng học viên đạt TOP 1, TOP 2, TOP 3 và tự động thêm vào Kho Voucher.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                {{-- TOP 1 --}}
                <div class="rounded-xl border-2 border-amber-300 bg-amber-50/40 p-5 space-y-4 relative">
                    <div class="flex items-center justify-between border-b border-amber-200/80 pb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-500 text-slate-950 px-3 py-1 text-xs font-black uppercase">
                            🥇 TOP 1 BÁ VƯƠNG
                        </span>
                        <span class="text-xs font-bold text-amber-800">Quán Quân</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Mã Giảm Giá" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-slate-600 font-semibold cursor-not-allowed">
                        </div>

                        <div>
                            <label for="top1_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top1_type" name="top1_type" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="fixed" @selected(old('top1_type', $configs[1]['type']) === 'fixed')>Giảm số tiền cố định (VNĐ)</option>
                                <option value="percent" @selected(old('top1_type', $configs[1]['type']) === 'percent')>Giảm theo phần trăm (%)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top1_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top1_value" name="top1_value" value="{{ old('top1_value', $configs[1]['value']) }}"
                                   placeholder="VD: 200000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top1_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn sử dụng (Số ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top1_expiry_days" name="top1_expiry_days" value="{{ old('top1_expiry_days', $configs[1]['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- TOP 2 --}}
                <div class="rounded-xl border border-slate-300 bg-slate-50/60 p-5 space-y-4 relative">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-300 text-slate-900 px-3 py-1 text-xs font-black uppercase">
                            🥈 TOP 2 Á QUÂN
                        </span>
                        <span class="text-xs font-bold text-slate-600">Á Quân</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Mã Giảm Giá" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-slate-600 font-semibold cursor-not-allowed">
                        </div>

                        <div>
                            <label for="top2_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top2_type" name="top2_type" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="fixed" @selected(old('top2_type', $configs[2]['type']) === 'fixed')>Giảm số tiền cố định (VNĐ)</option>
                                <option value="percent" @selected(old('top2_type', $configs[2]['type']) === 'percent')>Giảm theo phần trăm (%)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top2_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top2_value" name="top2_value" value="{{ old('top2_value', $configs[2]['value']) }}"
                                   placeholder="VD: 150000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top2_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn sử dụng (Số ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top2_expiry_days" name="top2_expiry_days" value="{{ old('top2_expiry_days', $configs[2]['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- TOP 3 --}}
                <div class="rounded-xl border border-amber-700/30 bg-amber-900/5 p-5 space-y-4 relative">
                    <div class="flex items-center justify-between border-b border-amber-700/20 pb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-800 text-white px-3 py-1 text-xs font-black uppercase">
                            🥉 TOP 3 TINH ANH
                        </span>
                        <span class="text-xs font-bold text-amber-800">Top 3</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Mã Giảm Giá" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-slate-600 font-semibold cursor-not-allowed">
                        </div>

                        <div>
                            <label for="top3_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top3_type" name="top3_type" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="fixed" @selected(old('top3_type', $configs[3]['type']) === 'fixed')>Giảm số tiền cố định (VNĐ)</option>
                                <option value="percent" @selected(old('top3_type', $configs[3]['type']) === 'percent')>Giảm theo phần trăm (%)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top3_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top3_value" name="top3_value" value="{{ old('top3_value', $configs[3]['value']) }}"
                                   placeholder="VD: 50000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top3_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn sử dụng (Số ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top3_expiry_days" name="top3_expiry_days" value="{{ old('top3_expiry_days', $configs[3]['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700 shadow-sm cursor-pointer">
                    💾 Lưu cấu hình phần thưởng
                </button>
            </div>
        </div>
    </form>

    {{-- Manual Trigger Action Box --}}
    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">⚡ Trao thưởng thủ công cho tháng vừa qua</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tiến trình này sẽ tự động chạy vào lúc 00:05 ngày 1 hàng tháng qua Cronjob. Bạn cũng có thể kích hoạt thủ công bên dưới để test.</p>
            </div>

            <form method="POST" action="{{ route('admin.coupons.reward_run_now') }}" class="flex items-center gap-2 shrink-0">
                @csrf
                <input type="month" name="period" value="{{ now()->subMonth()->format('Y-m') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 outline-none">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition cursor-pointer">
                    Kích hoạt ngay
                </button>
            </form>
        </div>
    </div>

</div>

</x-admin-layout>
