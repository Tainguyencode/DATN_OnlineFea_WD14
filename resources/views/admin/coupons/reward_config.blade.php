<x-admin-layout title="Cấu hình phần thưởng TOP Bảng Xếp Hạng" page-title="Cấu hình phần thưởng TOP Bảng Xếp Hạng (Tháng & Tuần)" breadcrumb="Mã giảm giá">

<div class="mx-auto max-w-6xl space-y-6">

    {{-- Tabs Navigation --}}
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-white p-3 rounded-lg shadow-sm">
        <a href="{{ route('admin.coupons.reward_config') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-bold text-white shadow-sm">
            <span>🏆</span> Cấu hình thưởng TOP (Tháng & Tuần)
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

        {{-- MONTHLY REWARDS CONFIG --}}
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🏆</span> 1. Phần thưởng tự động cho TOP Bảng Xếp Hạng THÁNG
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Hệ thống sẽ tự động chốt TOP vào 00:05 ngày 1 hàng tháng, tạo mã Voucher riêng biệt cho từng học viên đạt TOP và tự động gửi vào Kho Voucher.
                </p>
            </div>

            <div class="grid gap-4 grid-cols-1 md:grid-cols-3 lg:grid-cols-5">
                {{-- TOP 1 --}}
                <div class="rounded-xl border-2 border-amber-300 bg-amber-50/40 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-amber-200/80 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-500 text-slate-950 px-2 py-0.5 text-[10px] font-black uppercase">
                            🥇 TOP 1 THÁNG
                        </span>
                        <span class="text-[11px] font-bold text-amber-800">Quán Quân</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="top1_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top1_type" name="top1_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('top1_type', $configs[1]['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('top1_type', $configs[1]['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top1_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top1_value" name="top1_value" value="{{ old('top1_value', $configs[1]['value']) }}"
                                   placeholder="VD: 40" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top1_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top1_expiry_days" name="top1_expiry_days" value="{{ old('top1_expiry_days', $configs[1]['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- TOP 2 --}}
                <div class="rounded-xl border border-slate-300 bg-slate-50/60 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-300 text-slate-900 px-2 py-0.5 text-[10px] font-black uppercase">
                            🥈 TOP 2 THÁNG
                        </span>
                        <span class="text-[11px] font-bold text-slate-600">Á Quân</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="top2_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top2_type" name="top2_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('top2_type', $configs[2]['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('top2_type', $configs[2]['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top2_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top2_value" name="top2_value" value="{{ old('top2_value', $configs[2]['value']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top2_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top2_expiry_days" name="top2_expiry_days" value="{{ old('top2_expiry_days', $configs[2]['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- TOP 3 --}}
                <div class="rounded-xl border border-amber-700/30 bg-amber-900/5 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-amber-700/20 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-800 text-white px-2 py-0.5 text-[10px] font-black uppercase">
                            🥉 TOP 3 THÁNG
                        </span>
                        <span class="text-[11px] font-bold text-amber-800">Top 3</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="top3_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top3_type" name="top3_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('top3_type', $configs[3]['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('top3_type', $configs[3]['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top3_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top3_value" name="top3_value" value="{{ old('top3_value', $configs[3]['value']) }}"
                                   placeholder="VD: 20" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top3_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top3_expiry_days" name="top3_expiry_days" value="{{ old('top3_expiry_days', $configs[3]['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- TOP 4 - TOP 9 --}}
                <div class="rounded-xl border border-indigo-200 bg-indigo-50/40 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-indigo-200 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-600 text-white px-2 py-0.5 text-[10px] font-black uppercase">
                            🎖️ TOP 4 - TOP 9
                        </span>
                        <span class="text-[11px] font-bold text-indigo-700">Khuyến Khích</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="top4_9_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top4_9_type" name="top4_9_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('top4_9_type', $configs['4_9']['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('top4_9_type', $configs['4_9']['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top4_9_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top4_9_value" name="top4_9_value" value="{{ old('top4_9_value', $configs['4_9']['value']) }}"
                                   placeholder="VD: 15" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top4_9_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top4_9_expiry_days" name="top4_9_expiry_days" value="{{ old('top4_9_expiry_days', $configs['4_9']['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- TOP 10 - TOP 50 --}}
                <div class="rounded-xl border border-blue-200 bg-blue-50/40 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-blue-200 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-blue-600 text-white px-2 py-0.5 text-[10px] font-black uppercase">
                            🏅 TOP 10 - TOP 50
                        </span>
                        <span class="text-[11px] font-bold text-blue-700">Tích Cực</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="top10_50_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="top10_50_type" name="top10_50_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('top10_50_type', $configs['10_50']['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('top10_50_type', $configs['10_50']['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="top10_50_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="top10_50_value" name="top10_50_value" value="{{ old('top10_50_value', $configs['10_50']['value']) }}"
                                   placeholder="VD: 10" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="top10_50_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="top10_50_expiry_days" name="top10_50_expiry_days" value="{{ old('top10_50_expiry_days', $configs['10_50']['expiry_days']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- WEEKLY REWARDS CONFIG --}}
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>⚡</span> 2. Phần thưởng tự động cho TOP Bảng Xếp Hạng TUẦN
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Hệ thống sẽ tự động chốt TOP Tuần vào 00:05 Thứ Hai hàng tuần, trao thưởng cho học viên đạt TOP 1, TOP 2, TOP 3 và TOP 4 - 10.
                </p>
            </div>

            <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
                {{-- WEEKLY TOP 1 --}}
                <div class="rounded-xl border-2 border-amber-300 bg-amber-50/40 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-amber-200/80 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-500 text-slate-950 px-2 py-0.5 text-[10px] font-black uppercase">
                            🥇 TOP 1 TUẦN
                        </span>
                        <span class="text-[11px] font-bold text-amber-800">Quán Quân Tuần</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="weekly_top1_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="weekly_top1_type" name="weekly_top1_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('weekly_top1_type', $weeklyConfigs[1]['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('weekly_top1_type', $weeklyConfigs[1]['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="weekly_top1_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="weekly_top1_value" name="weekly_top1_value" value="{{ old('weekly_top1_value', $weeklyConfigs[1]['value']) }}"
                                   placeholder="VD: 30" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="weekly_top1_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="weekly_top1_expiry_days" name="weekly_top1_expiry_days" value="{{ old('weekly_top1_expiry_days', $weeklyConfigs[1]['expiry_days']) }}"
                                   placeholder="VD: 7" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- WEEKLY TOP 2 --}}
                <div class="rounded-xl border border-slate-300 bg-slate-50/60 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-300 text-slate-900 px-2 py-0.5 text-[10px] font-black uppercase">
                            🥈 TOP 2 TUẦN
                        </span>
                        <span class="text-[11px] font-bold text-slate-600">Á Quân Tuần</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="weekly_top2_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="weekly_top2_type" name="weekly_top2_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('weekly_top2_type', $weeklyConfigs[2]['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('weekly_top2_type', $weeklyConfigs[2]['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="weekly_top2_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="weekly_top2_value" name="weekly_top2_value" value="{{ old('weekly_top2_value', $weeklyConfigs[2]['value']) }}"
                                   placeholder="VD: 20" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="weekly_top2_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="weekly_top2_expiry_days" name="weekly_top2_expiry_days" value="{{ old('weekly_top2_expiry_days', $weeklyConfigs[2]['expiry_days']) }}"
                                   placeholder="VD: 7" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- WEEKLY TOP 3 --}}
                <div class="rounded-xl border border-amber-700/30 bg-amber-900/5 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-amber-700/20 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-800 text-white px-2 py-0.5 text-[10px] font-black uppercase">
                            🥉 TOP 3 TUẦN
                        </span>
                        <span class="text-[11px] font-bold text-amber-800">Top 3 Tuần</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="weekly_top3_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="weekly_top3_type" name="weekly_top3_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('weekly_top3_type', $weeklyConfigs[3]['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('weekly_top3_type', $weeklyConfigs[3]['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="weekly_top3_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="weekly_top3_value" name="weekly_top3_value" value="{{ old('weekly_top3_value', $weeklyConfigs[3]['value']) }}"
                                   placeholder="VD: 15" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="weekly_top3_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="weekly_top3_expiry_days" name="weekly_top3_expiry_days" value="{{ old('weekly_top3_expiry_days', $weeklyConfigs[3]['expiry_days']) }}"
                                   placeholder="VD: 7" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>

                {{-- WEEKLY TOP 4 - 10 --}}
                <div class="rounded-xl border border-indigo-200 bg-indigo-50/40 p-4 space-y-3 relative">
                    <div class="flex items-center justify-between border-b border-indigo-200 pb-2.5">
                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-600 text-white px-2 py-0.5 text-[10px] font-black uppercase">
                            🎖️ TOP 4 - TOP 10
                        </span>
                        <span class="text-[11px] font-bold text-indigo-700">Khuyến Khích Tuần</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Loại thưởng</label>
                            <input type="text" readonly value="Voucher Riêng" class="w-full rounded-lg border border-slate-200 bg-slate-100 px-2.5 py-1.5 text-slate-600 font-semibold cursor-not-allowed text-xs">
                        </div>

                        <div>
                            <label for="weekly_top4_10_type" class="block font-bold text-slate-700 mb-1">Loại giảm giá <span class="text-rose-500">*</span></label>
                            <select id="weekly_top4_10_type" name="weekly_top4_10_type" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-900 outline-none focus:border-rose-400">
                                <option value="percent" @selected(old('weekly_top4_10_type', $weeklyConfigs['4_10']['type']) === 'percent')>Giảm phần trăm (%)</option>
                                <option value="fixed" @selected(old('weekly_top4_10_type', $weeklyConfigs['4_10']['type']) === 'fixed')>Giảm số tiền (VNĐ)</option>
                            </select>
                        </div>

                        <div>
                            <label for="weekly_top4_10_value" class="block font-bold text-slate-700 mb-1">Giá trị giảm <span class="text-rose-500">*</span></label>
                            <input type="number" step="any" id="weekly_top4_10_value" name="weekly_top4_10_value" value="{{ old('weekly_top4_10_value', $weeklyConfigs['4_10']['value']) }}"
                                   placeholder="VD: 10" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-900 outline-none focus:border-rose-400">
                        </div>

                        <div>
                            <label for="weekly_top4_10_expiry_days" class="block font-bold text-slate-700 mb-1">Hạn dùng (Ngày) <span class="text-rose-500">*</span></label>
                            <input type="number" min="1" id="weekly_top4_10_expiry_days" name="weekly_top4_10_expiry_days" value="{{ old('weekly_top4_10_expiry_days', $weeklyConfigs['4_10']['expiry_days']) }}"
                                   placeholder="VD: 7" class="w-full rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-900 outline-none focus:border-rose-400">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-rose-700 shadow-sm cursor-pointer">
                    💾 Lưu cấu hình phần thưởng (Tháng & Tuần)
                </button>
            </div>
        </div>
    </form>

    {{-- Manual Trigger Action Box (Month & Week) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Trao thưởng THÁNG thủ công --}}
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-sm space-y-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                    <span>🏆</span> Trao thưởng THÁNG thủ công
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Tự động chạy vào 00:05 ngày 1 hàng tháng qua Cronjob. Chọn tháng để chạy lại thủ công.</p>
            </div>

            <form method="POST" action="{{ route('admin.coupons.reward_run_now') }}" class="flex items-center gap-2">
                @csrf
                <input type="month" name="period" value="{{ now()->subMonth()->format('Y-m') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 outline-none">
                <button type="submit" class="shrink-0 rounded-lg bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition cursor-pointer">
                    Kích hoạt ngay
                </button>
            </form>
        </div>

        {{-- Trao thưởng TUẦN thủ công --}}
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-sm space-y-3">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                    <span>⚡</span> Trao thưởng TUẦN thủ công
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Tự động chạy vào 00:05 Thứ Hai hàng tuần. Nhập mã tuần (VD: {{ now()->subWeek()->format('o-\WW') }}) để kích hoạt.</p>
            </div>

            <form method="POST" action="{{ route('admin.coupons.reward_weekly_run_now') }}" class="flex items-center gap-2">
                @csrf
                <input type="text" name="period" value="{{ now()->subWeek()->format('o-\WW') }}" placeholder="VD: 2026-W34" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-800 outline-none font-mono">
                <button type="submit" class="shrink-0 rounded-lg bg-rose-600 px-4 py-2 text-xs font-bold text-white hover:bg-rose-700 transition cursor-pointer">
                    Kích hoạt ngay
                </button>
            </form>
        </div>
    </div>

</div>

</x-admin-layout>
