@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng #'.$order->order_code.' - Website học online FEA')

@section('content')
<div class="bg-slate-50 py-8 dark:bg-slate-950 min-h-[calc(100vh-16rem)]" 
     x-data="{
         orderCode: '{{ $order->order_code }}',
         subtotal: {{ (float) ($order->subtotal > 0 ? $order->subtotal : $order->total_amount) }},
         discountAmount: {{ (float) ($order->discount_amount ?? 0) }},
         totalAmount: {{ (float) $order->total_amount }},
         couponCode: '{{ $order->coupon?->code ?? '' }}',
         appliedCouponCode: '{{ $order->coupon?->code ?? '' }}',
         couponError: '',
         couponSuccess: '',
         isApplying: false,
         timer: null,
         csrfToken: '{{ csrf_token() }}',
         availableCoupons: {{ json_encode(($activeCoupons ?? collect([]))->map(fn($cp) => [
             'id' => $cp->id,
             'code' => $cp->code,
             'type' => $cp->type,
             'value' => (float)$cp->value,
             'min_order_amount' => (float)$cp->min_order_amount
         ])) }},
         formatMoney(val) {
             return new Intl.NumberFormat('vi-VN').format(val) + 'đ';
         },
         initPolling() {
             this.timer = setInterval(() => {
                 this.checkStatus();
             }, 3000);
         },
         async checkStatus() {
             try {
                 let res = await fetch('/student/checkout/' + this.orderCode + '/status', {
                     headers: { 'Accept': 'application/json' }
                 });
                 let data = await res.json();
                 if (data.status === 'paid') {
                     clearInterval(this.timer);
                     window.location.href = '/student/checkout/' + this.orderCode + '/success';
                 }
             } catch (e) {
                 console.error(e);
             }
         },
         async applyCoupon(code = null) {
             if (code) {
                 this.couponCode = code;
             }
             if (!this.couponCode) return;
             
             this.isApplying = true;
             this.couponError = '';
             this.couponSuccess = '';

             try {
                 let res = await fetch('/student/checkout/' + this.orderCode + '/apply-coupon', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': this.csrfToken,
                         'Accept': 'application/json'
                     },
                     body: JSON.stringify({ coupon_code: this.couponCode })
                 });
                 let data = await res.json();
                 if (data.success) {
                     this.discountAmount = data.discount_amount;
                     this.totalAmount = data.new_total;
                     this.appliedCouponCode = data.coupon_code;
                     this.couponSuccess = data.message;
                 } else {
                     this.couponError = data.message;
                 }
             } catch (e) {
                 this.couponError = 'Không thể áp dụng mã giảm giá. Vui lòng thử lại.';
                 console.error(e);
             } finally {
                 this.isApplying = false;
             }
         },
         async removeCoupon() {
             this.isApplying = true;
             try {
                 let res = await fetch('/student/checkout/' + this.orderCode + '/remove-coupon', {
                     method: 'DELETE',
                     headers: {
                         'X-CSRF-TOKEN': this.csrfToken,
                         'Accept': 'application/json'
                     }
                 });
                 let data = await res.json();
                 if (data.success) {
                     this.discountAmount = 0;
                     this.totalAmount = data.new_total;
                     this.appliedCouponCode = '';
                     this.couponCode = '';
                     this.couponSuccess = 'Đã gỡ mã giảm giá.';
                     this.couponError = '';
                 }
             } catch (e) {
                 console.error(e);
             } finally {
                 this.isApplying = false;
             }
         }
     }"
     x-init="initPolling()">
    
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- NAV QUAY LẠI -->
        <div class="flex items-center justify-between">
            <a href="{{ route('student.orders') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại Danh sách đơn hàng
            </a>

            <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold text-slate-500">#{{ $order->order_code }}</span>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-12">
            <!-- CỘT CHỌN CỔNG THANH TOÁN (5 COLS) -->
            <div class="space-y-6 lg:col-span-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h1 class="text-lg font-extrabold text-slate-950 dark:text-white">Phương thức thanh toán</h1>
                    <p class="mt-1 text-xs text-slate-500">Chọn cổng thanh toán để hoàn tất đơn hàng</p>

                    <form method="POST" action="{{ route('student.checkout.process_payment', $order->order_code) }}" class="mt-6 space-y-4" x-data="{ selectedGateway: 'bank_transfer' }">
                        @csrf

                        <!-- CỔNG THANH TOÁN -->
                        <label class="relative flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition"
                               :class="selectedGateway === 'bank_transfer' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20 dark:border-indigo-500 dark:bg-indigo-950/20' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="payment_method" value="bank_transfer" x-model="selectedGateway" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-extrabold text-xs dark:bg-emerald-900/50 dark:text-emerald-300 shrink-0">
                                    QR
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-950 dark:text-white">PayOS VietQR</span>
                                    <p class="mt-0.5 text-[11px] text-slate-500">Tự động kích hoạt sau khi thanh toán</p>
                                </div>
                            </div>
                        </label>

                        <div class="pt-4">
                            <button type="submit" class="h-12 w-full rounded-xl bg-indigo-600 text-xs font-extrabold text-white transition hover:bg-indigo-700 shadow-md flex items-center justify-center gap-2 cursor-pointer">
                                <span>Tiến hành quét mã QR (<span x-text="formatMoney(totalAmount)"></span>)</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CỘT BÊN PHẢI: TỔNG KẾT ĐƠN HÀNG & MÃ GIẢM GIÁ (7 COLS) -->
            <div class="space-y-4 lg:col-span-7">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Thông tin đơn hàng</h3>

                    <div class="space-y-3">
                        @foreach($order->items ?? [] as $item)
                            @php
                                $isModel = $item instanceof \App\Models\OrderItem;
                                $course = $isModel ? $item->course : null;
                                $title = $course?->title ?? ($item['title'] ?? 'Khóa học');
                                $price = $isModel ? $item->price : ($item['price'] ?? 0);
                            @endphp
                            <div class="flex items-center justify-between text-xs">
                                <span class="line-clamp-1 font-semibold text-slate-800 dark:text-slate-200">{{ $title }}</span>
                                <span class="font-bold text-slate-950 dark:text-white shrink-0 ml-2">{{ number_format($price, 0, ',', '.') }}đ</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- MÃ GIẢM GIÁ TRỰC TIẾP TRÊN TRANG THANH TOÁN -->
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-4">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Mã giảm giá / Voucher</label>
                        
                        <div class="relative flex items-center">
                            <input type="text" placeholder="Nhập mã giảm giá"
                                   x-model="couponCode"
                                   :readonly="appliedCouponCode !== ''"
                                   :class="appliedCouponCode !== '' ? 'bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-200 border-emerald-500 font-semibold' : 'bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700'"
                                   class="w-full pl-3 pr-20 py-2 border rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none uppercase font-mono tracking-wider transition">
                            
                            <button type="button" 
                                    @click="appliedCouponCode !== '' ? removeCoupon() : applyCoupon()"
                                    :disabled="isApplying || (!couponCode && appliedCouponCode === '')"
                                    :class="appliedCouponCode !== '' ? 'bg-rose-500 hover:bg-rose-600 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white'"
                                    class="absolute right-1 px-3 py-1.5 rounded-lg font-extrabold text-xs transition shadow-sm cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                                <span x-show="isApplying" class="inline-block animate-spin mr-1">⌛</span>
                                <span x-text="appliedCouponCode !== '' ? 'Gỡ' : 'Áp dụng'"></span>
                            </button>
                        </div>

                        <!-- Thông báo lỗi hoặc thành công -->
                        <div class="mt-1.5 text-xs font-medium text-rose-500" x-show="couponError" x-text="couponError"></div>
                        <div class="mt-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400" x-show="couponSuccess" x-text="couponSuccess"></div>

                        <!-- Danh sách Voucher có sẵn -->
                        <div class="mt-3 space-y-1.5" x-show="availableCoupons.length > 0">
                            <template x-for="cp in availableCoupons" :key="cp.id">
                                <div class="flex items-center justify-between p-2 bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-slate-200 dark:border-slate-800 text-xs">
                                    <div class="min-w-0 pr-1 flex items-center gap-1.5">
                                        <span class="font-mono font-extrabold text-indigo-700 dark:text-indigo-300 px-1.5 py-0.5 bg-indigo-50 dark:bg-indigo-950/60 rounded border border-indigo-200 dark:border-indigo-800 text-[11px]" x-text="cp.code"></span>
                                        <span class="text-[11px] font-medium text-slate-600 dark:text-slate-400" x-text="cp.type === 'percent' ? 'Giảm ' + parseFloat(cp.value) + '%' : 'Giảm ' + formatMoney(cp.value)"></span>
                                    </div>
                                    <button type="button" 
                                            @click="applyCoupon(cp.code)"
                                            :disabled="appliedCouponCode === cp.code"
                                            class="px-2 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:text-indigo-300 font-extrabold text-[11px] shrink-0 transition disabled:opacity-40 cursor-pointer">
                                        <span x-text="appliedCouponCode === cp.code ? 'Đã dùng' : 'Dùng'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Tạm tính</span>
                            <span x-text="formatMoney(subtotal)"></span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-semibold" x-show="discountAmount > 0">
                            <span class="flex items-center gap-1">
                                Giảm giá
                                <span class="text-[10px] bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 px-1 py-0.5 rounded font-bold uppercase" x-text="appliedCouponCode"></span>
                            </span>
                            <span x-text="'-' + formatMoney(discountAmount)"></span>
                        </div>
                        <div class="flex justify-between items-center border-t border-slate-100 dark:border-slate-800 pt-2 text-sm">
                            <span class="font-extrabold text-slate-950 dark:text-white">Tổng cộng</span>
                            <span class="font-extrabold text-base text-indigo-600 dark:text-indigo-400" x-text="formatMoney(totalAmount)"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
