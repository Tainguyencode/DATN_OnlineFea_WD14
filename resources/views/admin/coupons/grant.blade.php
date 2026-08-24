<x-admin-layout title="Tặng mã giảm giá cho học viên" page-title="Tặng mã giảm giá cho học viên" breadcrumb="Mã giảm giá">

<div class="mx-auto max-w-5xl">

    @if ($errors->any())
        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <p class="font-bold">Vui lòng kiểm tra lại thông tin mã giảm giá.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.coupons.grant.store') }}" class="space-y-5" novalidate>
        @csrf

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="grid gap-5 lg:grid-cols-2">
                <!-- Chọn Học viên -->
                <div class="lg:col-span-2">
                    <label for="user_id" class="mb-1.5 block text-sm font-bold text-slate-700">Học viên nhận voucher <span class="text-rose-500">*</span></label>
                    <select id="user_id" name="user_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <option value="">-- Chọn học viên nhận voucher --</option>

                        @if(isset($topStudents) && $topStudents->isNotEmpty())
                            <optgroup label="🏆 HỌC VIÊN TOP THÁNG NÀY (BẢNG XẾP HẠNG)">
                                @foreach($topStudents as $index => $student)
                                    @php
                                        $rankLabel = match($index) {
                                            0 => '🏆 TOP 1 Tháng này',
                                            1 => '🥈 TOP 2 Tháng này',
                                            2 => '🥉 TOP 3 Tháng này',
                                            default => '⭐ TOP ' . ($index + 1) . ' Tháng này',
                                        };
                                    @endphp
                                    <option value="{{ $student->id }}" @selected(old('user_id', $selectedUserId) == $student->id)>
                                        {{ $rankLabel }} - {{ $student->name }} ({{ number_format($student->period_xp) }} XP) - {{ $student->email }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        <optgroup label="DANH SÁCH TẤT CẢ HỌC VIÊN">
                            @foreach($otherStudents as $student)
                                <option value="{{ $student->id }}" @selected(old('user_id', $selectedUserId) == $student->id)>
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @error('user_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Mã giảm giá -->
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="code" class="text-sm font-bold text-slate-700">Mã giảm giá <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="generateCode()" class="text-xs font-bold text-rose-600 hover:underline">
                            Tự sinh mã
                        </button>
                    </div>
                    <input id="code" type="text" name="code" value="{{ old('code') }}" maxlength="50" placeholder="VD: WELCOME50" style="text-transform: uppercase"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 font-mono font-bold text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('code') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Loại giảm giá -->
                <div>
                    <label for="type" class="mb-1.5 block text-sm font-bold text-slate-700">Loại giảm giá <span class="text-rose-500">*</span></label>
                    <select id="type" name="type"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <option value="percent" @selected(old('type') === 'percent')>Giảm theo phần trăm (%)</option>
                        <option value="fixed" @selected(old('type') === 'fixed')>Số tiền cố định (đ)</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Giá trị giảm -->
                <div>
                    <label for="value" class="mb-1.5 block text-sm font-bold text-slate-700">Giá trị giảm <span class="text-rose-500">*</span></label>
                    <input id="value" type="number" step="any" name="value" value="{{ old('value') }}" min="0" placeholder="Nhập số % hoặc số tiền cố định"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('value') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Giá trị đơn tối thiểu -->
                <div>
                    <label for="min_order_amount" class="mb-1.5 block text-sm font-bold text-slate-700">Giá trị đơn hàng tối thiểu <span class="text-rose-500">*</span></label>
                    <input id="min_order_amount" type="number" step="any" name="min_order_amount" value="{{ old('min_order_amount', 0) }}" min="0" placeholder="VD: 100000"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('min_order_amount') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Số lượt sử dụng tối đa -->
                <div>
                    <label for="max_uses" class="mb-1.5 block text-sm font-bold text-slate-700">Số lượt sử dụng tối đa <span class="text-rose-500">*</span></label>
                    <input id="max_uses" type="number" name="max_uses" value="{{ old('max_uses', 1) }}" min="1" placeholder="Để trống nếu không giới hạn"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('max_uses') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Phạm vi áp dụng (Khóa học) -->
                <div>
                    <label for="course_id" class="mb-1.5 block text-sm font-bold text-slate-700">Phạm vi áp dụng</label>
                    <select id="course_id" name="course_id"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                        <option value="">Tất cả khóa học (Toàn sàn)</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>
                                Khóa học: {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Thời gian kết thúc -->
                <div>
                    <label for="expires_at" class="mb-1.5 block text-sm font-bold text-slate-700">Thời gian kết thúc</label>
                    <input id="expires_at" type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('expires_at') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <!-- Lý do tặng -->
                <div class="lg:col-span-2">
                    <label for="reason" class="mb-1.5 block text-sm font-bold text-slate-700">Lý do tặng voucher <span class="text-rose-500">*</span></label>
                    <input id="reason" type="text" name="reason" value="{{ old('reason') }}" placeholder="VD: Thưởng TOP 1 tháng 8, Học viên xuất sắc..."
                           class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    @error('reason') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <!-- Footer Buttons -->
        <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">Mã giảm giá riêng tư sẽ được tự động thêm vào Kho Voucher của học viên.</p>
            <div class="flex gap-2">
                <a href="{{ route('admin.coupons.index') }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                    Hủy
                </a>
                <button type="submit"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg bg-rose-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700">
                    Tặng mã giảm giá
                </button>
            </div>
        </div>
    </form>

</div>

<script>
    function generateCode() {
        const prefix = 'GIFT';
        const randomStr = Math.random().toString(36).substring(2, 7).toUpperCase();
        document.getElementById('code').value = `${prefix}-${randomStr}`;
    }
</script>

</x-admin-layout>
