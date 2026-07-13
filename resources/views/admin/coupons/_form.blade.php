@php
    $isEdit = $coupon->exists;
@endphp

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

<form method="POST" action="{{ $action }}" class="space-y-5">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="code" class="mb-1.5 block text-sm font-bold text-slate-700">Mã giảm giá <span class="text-rose-500">*</span></label>
                <input id="code" type="text" name="code" value="{{ old('code', $coupon->code) }}" maxlength="50" placeholder="VD: WELCOME50" style="text-transform: uppercase"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('code') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="mb-1.5 block text-sm font-bold text-slate-700">Loại giảm giá <span class="text-rose-500">*</span></label>
                <select id="type" name="type"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                    <option value="percent" @selected(old('type', $coupon->type) === 'percent')>Giảm theo phần trăm (%)</option>
                    <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Số tiền cố định (đ)</option>
                </select>
                @error('type') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="value" class="mb-1.5 block text-sm font-bold text-slate-700">Giá trị giảm <span class="text-rose-500">*</span></label>
                <input id="value" type="number" step="any" name="value" value="{{ old('value', $coupon->value) }}" min="0" placeholder="Nhập số % hoặc số tiền cố định"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('value') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="min_order_amount" class="mb-1.5 block text-sm font-bold text-slate-700">Giá trị đơn hàng tối thiểu <span class="text-rose-500">*</span></label>
                <input id="min_order_amount" type="number" step="any" name="min_order_amount" value="{{ old('min_order_amount', $coupon->min_order_amount ?? 0) }}" min="0" placeholder="VD: 100000"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('min_order_amount') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="max_uses" class="mb-1.5 block text-sm font-bold text-slate-700">Số lượt sử dụng tối đa</label>
                <input id="max_uses" type="number" name="max_uses" value="{{ old('max_uses', $coupon->max_uses) }}" min="1" placeholder="Để trống nếu không giới hạn"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('max_uses') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Trạng thái kích hoạt</span>
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-bold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true))
                           class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
                    Kích hoạt sử dụng mã này
                </label>
            </div>

            <div>
                <label for="starts_at" class="mb-1.5 block text-sm font-bold text-slate-700">Thời gian bắt đầu</label>
                <input id="starts_at" type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('starts_at') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="expires_at" class="mb-1.5 block text-sm font-bold text-slate-700">Thời gian kết thúc</label>
                <input id="expires_at" type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition-colors duration-200 focus:border-rose-400 focus:ring-4 focus:ring-rose-100">
                @error('expires_at') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-500">Mã giảm giá sẽ được hiển thị cho học viên trên trang giỏ hàng nếu hợp lệ.</p>
        <div class="flex gap-2">
            <a href="{{ route('admin.coupons.index') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                Hủy
            </a>
            <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-rose-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700">
                {{ $submitLabel }}
            </button>
        </div>
    </div>
</form>
