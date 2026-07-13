@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-[70vh]">
    
    <!-- Tiêu đề giỏ hàng -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-900 dark:text-white">Giỏ hàng của bạn</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1.5">Quản lý các khóa học đang chọn mua và tiến hành thanh toán an toàn</p>
    </div>

    @if($cart->courses->isEmpty())
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-16 text-center shadow-sm">
            <svg class="w-16 h-16 text-slate-300 dark:text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="text-slate-600 dark:text-slate-400 text-lg font-medium">Giỏ hàng của bạn đang trống</p>
            <a href="{{ route('home') }}#courses" class="inline-block mt-4 text-[#0056D2] dark:text-blue-400 font-semibold hover:underline">Tiếp tục mua sắm →</a>
        </div>
    @else
        <div x-data="{
            courses: {{ json_encode($cart->courses->map(fn($c) => [
                'id' => $c->id,
                'price' => (float)($c->discount_price ?? $c->sale_price ?? $c->price)
            ])) }},
            checkedIds: {{ json_encode($cart->courses->pluck('id')) }},
            selectAll: true,
            paymentMethod: 'vnpay',
            couponCode: '',
            appliedCoupon: null,
            couponError: '',
            couponSuccess: '',
            isApplying: false,
            availableCoupons: {{ json_encode($activeCoupons->map(fn($cp) => [
                'id' => $cp->id,
                'code' => $cp->code,
                'type' => $cp->type,
                'value' => (float)$cp->value,
                'min_order_amount' => (float)$cp->min_order_amount
            ])) }},
            csrfToken: '{{ csrf_token() }}',
            toggleSelectAll() {
                if (this.selectAll) {
                    this.checkedIds = this.courses.map(c => c.id);
                } else {
                    this.checkedIds = [];
                }
            },
            updateSelectAll() {
                this.selectAll = this.checkedIds.length === this.courses.length;
            },
            get total() {
                let sum = 0;
                this.courses.forEach(c => {
                    if (this.checkedIds.map(Number).includes(Number(c.id))) {
                        sum += c.price;
                    }
                });
                return sum;
            },
            get isCouponConditionMet() {
                if (!this.appliedCoupon) return true;
                return this.total >= parseFloat(this.appliedCoupon.min_order_amount);
            },
            get discount() {
                if (!this.appliedCoupon || !this.isCouponConditionMet) return 0;
                let subtotal = this.total;
                let isPercentage = this.appliedCoupon.type === 'percent' || this.appliedCoupon.type === 'percentage';
                if (isPercentage) {
                    return subtotal * (parseFloat(this.appliedCoupon.value) / 100);
                } else {
                    return Math.min(parseFloat(this.appliedCoupon.value), subtotal);
                }
            },
            get grandTotal() {
                return Math.max(0, this.total - this.discount);
            },
            formatMoney(value) {
                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
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
                    let response = await fetch('{{ route('student.cart.coupon.apply') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            coupon_code: this.couponCode,
                            course_ids: this.checkedIds
                        })
                    });
                    
                    let data = await response.json();
                    
                    if (data.success) {
                        this.appliedCoupon = data.coupon;
                        this.couponSuccess = data.message;
                        this.couponCode = data.coupon.code;
                    } else {
                        this.appliedCoupon = null;
                        this.couponError = data.message;
                    }
                } catch (e) {
                    this.couponError = 'Đã xảy ra lỗi khi kiểm tra mã giảm giá.';
                    console.error(e);
                } finally {
                    this.isApplying = false;
                }
            },
            removeCoupon() {
                this.appliedCoupon = null;
                this.couponCode = '';
                this.couponSuccess = '';
                this.couponError = '';
            }
        }" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Danh sách khóa học trong giỏ hàng -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Chọn tất cả -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-3 shadow-sm select-none">
                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 text-[#0056D2] border-slate-300 rounded focus:ring-[#0056D2] cursor-pointer">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Chọn tất cả khóa học (<span x-text="checkedIds.length"></span>/<span x-text="courses.length"></span>)</span>
                </div>

                @foreach($cart->courses as $course)
                    @php 
                        $price = $course->discount_price ?? $course->sale_price ?? $course->price; 
                    @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex items-center gap-4 shadow-sm select-none">
                        <input type="checkbox" :value="{{ $course->id }}" x-model="checkedIds" @change="updateSelectAll()" class="w-4 h-4 text-[#0056D2] border-slate-300 rounded focus:ring-[#0056D2] cursor-pointer shrink-0">
                        <div class="w-16 h-16 bg-blue-50 dark:bg-blue-950/40 text-[#0056D2] dark:text-blue-300 rounded-xl flex items-center justify-center font-extrabold shrink-0 text-xl">
                            {{ strtoupper(substr($course->title, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-900 dark:text-white truncate text-base">{{ $course->title }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $course->instructor?->name }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="font-extrabold text-lg text-[#0056D2] dark:text-blue-300">{{ number_format($price, 0, ',', '.') }}đ</div>
                            <form method="POST" action="{{ route('student.cart.remove', $course->id) }}" class="mt-1.5">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-600 transition hover:underline">Xóa khỏi giỏ</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Hộp thông tin thanh toán & chọn cổng -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm sticky top-24">
                <h3 class="font-extrabold text-slate-950 dark:text-white text-lg mb-4">Thông tin thanh toán</h3>
                
                <div class="space-y-3 text-sm border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400">
                        <span>Tạm tính</span>
                        <span class="font-semibold text-slate-800 dark:text-white" x-text="formatMoney(total)"></span>
                    </div>
                    <!-- Dòng hiển thị giảm giá nếu có -->
                    <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-medium" x-show="discount > 0">
                        <span class="flex items-center gap-1">
                            Mã giảm giá
                            <span class="text-[10px] bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 px-1.5 py-0.5 rounded font-bold uppercase" x-text="appliedCoupon ? appliedCoupon.code : ''"></span>
                        </span>
                        <span x-text="'-' + formatMoney(discount)"></span>
                    </div>
                    <!-- Cảnh báo nếu coupon không đủ điều kiện đơn hàng tối thiểu -->
                    <div class="text-xs text-rose-500 font-semibold bg-rose-50 dark:bg-rose-950/20 p-2.5 rounded-lg" x-show="appliedCoupon && !isCouponConditionMet">
                        Mã <span class="font-bold" x-text="appliedCoupon.code"></span> yêu cầu đơn hàng từ <span x-text="formatMoney(appliedCoupon.min_order_amount)"></span>. Hãy chọn thêm khóa học để áp dụng.
                    </div>
                    <div class="flex justify-between text-slate-900 dark:text-white font-extrabold text-lg pt-1 border-t border-slate-50 dark:border-slate-800/40">
                        <span>Tổng cộng</span>
                        <span class="text-[#0056D2] dark:text-blue-300" x-text="formatMoney(grandTotal)"></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('student.cart.checkout') }}" class="space-y-4">
                    @csrf
                    <!-- Dynamic Selected Course Inputs -->
                    <template x-for="id in checkedIds" :key="id">
                        <input type="hidden" name="course_ids[]" :value="id">
                    </template>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Mã giảm giá</label>
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" placeholder="Nhập mã (VD: WELCOME20)"
                                   x-model="couponCode"
                                   :readonly="appliedCoupon !== null"
                                   :class="appliedCoupon ? 'bg-slate-50 dark:bg-slate-800 text-slate-500 border-emerald-500 dark:border-emerald-500' : ''"
                                   class="flex-1 px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-[#0056D2] dark:bg-slate-950 dark:text-white outline-none">
                            
                            <button type="button" 
                                    @click="appliedCoupon ? removeCoupon() : applyCoupon()"
                                    :disabled="isApplying || (!couponCode && !appliedCoupon)"
                                    :class="appliedCoupon ? 'bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200' : 'bg-slate-900 hover:bg-slate-800 text-white'"
                                    class="px-4 py-2.5 rounded-xl font-bold text-sm transition cursor-pointer shrink-0 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="isApplying" class="inline-block animate-spin mr-1">⌛</span>
                                <span x-text="appliedCoupon ? 'Gỡ mã' : 'Áp dụng'"></span>
                            </button>
                        </div>
                        
                        <!-- Thông báo lỗi hoặc thành công -->
                        <div class="mt-2 text-xs font-medium text-rose-500" x-show="couponError" x-text="couponError"></div>
                        <div class="mt-2 text-xs font-medium text-emerald-600 dark:text-emerald-400" x-show="couponSuccess" x-text="couponSuccess"></div>
                    </div>

                    <!-- Danh sách mã giảm giá khả dụng -->
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/60" x-show="availableCoupons.length > 0">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Mã giảm giá khả dụng</label>
                        <div class="space-y-2">
                            <template x-for="cp in availableCoupons" :key="cp.id">
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-950/40 rounded-xl border border-slate-200 dark:border-slate-800/80 text-xs">
                                    <div class="min-w-0 flex-1 pr-2">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono font-bold text-slate-900 dark:text-white px-1.5 py-0.5 bg-white dark:bg-slate-900 rounded border border-slate-200 dark:border-slate-800" x-text="cp.code"></span>
                                            <span class="text-slate-500 font-medium" x-text="cp.type === 'percent' || cp.type === 'percentage' ? 'Giảm ' + parseFloat(cp.value) + '%' : 'Giảm ' + formatMoney(cp.value)"></span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1" x-show="parseFloat(cp.min_order_amount) > 0">
                                            Đơn tối thiểu: <span x-text="formatMoney(cp.min_order_amount)"></span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="applyCoupon(cp.code)"
                                            :disabled="appliedCoupon !== null && appliedCoupon.code === cp.code"
                                            class="bg-blue-50 dark:bg-blue-950/60 text-[#0056D2] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900 font-bold px-3 py-1.5 rounded-lg transition text-[11px] shrink-0 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                        Áp dụng
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Phương thức thanh toán</label>
                        
                        <div class="grid grid-cols-1 gap-2.5">
                            <!-- VNPay Option -->
                            <div @click="paymentMethod = 'vnpay'" 
                                 :class="paymentMethod === 'vnpay' ? 'border-[#0056D2] bg-blue-50/20 dark:border-blue-500' : 'border-slate-200 hover:border-slate-300 dark:border-slate-800'"
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition select-none">
                                <div class="flex items-center gap-3">
                                    <div class="h-6 w-16 flex items-center justify-center shrink-0">
                                        <img src="{{ asset('images/vnpay-logo.png') }}" alt="VNPay" class="h-full w-auto object-contain">
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-white">Cổng thanh toán VNPay</span>
                                </div>
                                <div class="h-4 w-4 rounded-full border-2 flex items-center justify-center shrink-0"
                                     :class="paymentMethod === 'vnpay' ? 'border-[#0056D2] bg-[#0056D2]' : 'border-slate-300'">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="paymentMethod === 'vnpay'"></div>
                                </div>
                            </div>

                            <!-- MoMo Option -->
                            <div @click="paymentMethod = 'momo'" 
                                 :class="paymentMethod === 'momo' ? 'border-[#d82d8b] bg-pink-50/10 dark:border-pink-500' : 'border-slate-200 hover:border-slate-300 dark:border-slate-800'"
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition select-none">
                                <div class="flex items-center gap-3">
                                    <div class="h-6 w-6 flex items-center justify-center shrink-0">
                                        <img src="{{ asset('images/momo-logo.jpg') }}" alt="MoMo" class="h-full w-auto object-contain rounded">
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-white">Ví điện tử MoMo</span>
                                </div>
                                <div class="h-4 w-4 rounded-full border-2 flex items-center justify-center shrink-0"
                                     :class="paymentMethod === 'momo' ? 'border-[#d82d8b] bg-[#d82d8b]' : 'border-slate-300'">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="paymentMethod === 'momo'"></div>
                                </div>
                            </div>

                            <!-- Bank Transfer Option -->
                            <div @click="paymentMethod = 'bank_transfer'" 
                                 :class="paymentMethod === 'bank_transfer' ? 'border-slate-800 bg-slate-50 dark:border-white dark:bg-slate-800/40' : 'border-slate-200 hover:border-slate-300 dark:border-slate-800'"
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition select-none">
                                <div class="flex items-center gap-3">
                                    <div class="h-6 w-6 flex items-center justify-center shrink-0 text-slate-600 dark:text-slate-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-white">Chuyển khoản liên ngân hàng</span>
                                </div>
                                <div class="h-4 w-4 rounded-full border-2 flex items-center justify-center shrink-0"
                                     :class="paymentMethod === 'bank_transfer' ? 'border-slate-800 bg-slate-800 dark:border-slate-400 dark:bg-slate-400' : 'border-slate-300'">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="paymentMethod === 'bank_transfer'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            :disabled="checkedIds.length === 0 || (appliedCoupon !== null && !isCouponConditionMet)"
                            :class="checkedIds.length === 0 || (appliedCoupon !== null && !isCouponConditionMet) ? 'opacity-50 cursor-not-allowed bg-slate-400 dark:bg-slate-700' : 'bg-[#0056D2] hover:bg-[#0046B8]'"
                            class="w-full text-white font-bold py-3.5 rounded-xl transition shadow-md mt-6 cursor-pointer">
                        Thanh toán ngay
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
