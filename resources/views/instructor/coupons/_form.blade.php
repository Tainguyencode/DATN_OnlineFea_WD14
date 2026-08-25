@php
    $isEdit = $coupon->exists;
@endphp

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Thông tin mã giảm giá</h3>
            <p class="text-xs text-slate-500">Mã giảm giá do bạn tạo sẽ trừ trực tiếp vào thu nhập cá nhân của bạn khi học viên áp dụng.</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="code" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Mã giảm giá <span class="text-rose-500">*</span></label>
                <input id="code" type="text" name="code" value="{{ old('code', $coupon->code) }}" maxlength="50" placeholder="VD: TEACHER50" style="text-transform: uppercase"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @error('code') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="course_id" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Áp dụng cho khóa học</label>
                <select id="course_id" name="course_id"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="">Tất cả khóa học của tôi</option>
                    @foreach($courses as $courseItem)
                        <option value="{{ $courseItem->id }}" @selected((string)old('course_id', $coupon->course_id) === (string)$courseItem->id)>
                            {{ $courseItem->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Loại giảm giá <span class="text-rose-500">*</span></label>
                <select id="type" name="type"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                    <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Giảm theo phần trăm (%)</option>
                    <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Số tiền cố định (đ)</option>
                </select>
                @error('type') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="value" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Giá trị giảm <span class="text-rose-500">*</span></label>
                <input id="value" type="number" step="any" name="value" value="{{ old('value', $coupon->value) }}" min="0" placeholder="Nhập số % hoặc số tiền cố định"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @error('value') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="min_order_amount" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Giá trị đơn hàng tối thiểu (đ)</label>
                <input id="min_order_amount" type="number" step="any" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? 0) }}" min="0" placeholder="VD: 100000"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @error('min_order_amount') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="max_uses" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Số lượt sử dụng tối đa</label>
                <input id="max_uses" type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" min="1" placeholder="Để trống nếu không giới hạn"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @error('max_uses') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="starts_at" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Thời gian bắt đầu</label>
                <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @error('starts_at') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="expires_at" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Thời gian kết thúc</label>
                <input id="expires_at" type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition duration-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                @error('expires_at') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="lg:col-span-2">
                <span class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-300">Trạng thái kích hoạt</span>
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-700 cursor-pointer dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-300">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true))
                           class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                    Kích hoạt mã giảm giá này ngay lập tức
                </label>
            </div>
        </div>
    </section>

    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between dark:border-slate-800 dark:bg-slate-900">
        <p class="text-xs text-slate-500">Mã giảm giá sẽ giúp thu hút học viên đăng ký khóa học của bạn hơn.</p>
        <div class="flex gap-2">
            <a href="{{ route('instructor.coupons.index') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition duration-200 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                Hủy
            </a>
            <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition duration-200 hover:bg-emerald-700 shadow-sm shadow-emerald-600/30">
                {{ $submitLabel }}
            </button>
        </div>
    </div>
</form>
