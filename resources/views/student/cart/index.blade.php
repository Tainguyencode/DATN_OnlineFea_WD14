@extends('layouts.app')

@section('content')
@include('partials.financial-clean-icons')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 min-h-[70vh]">
    <div class="mb-4">
        <button type="button" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('home') }}'; }" class="inline-flex items-center gap-2 text-sm sm:text-base font-bold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-400 cursor-pointer transition py-1">
            ← Quay lại
        </button>
    </div>

    <!-- Tiêu đề giỏ hàng -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white">Giỏ hàng của bạn</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Quản lý các khóa học đang chọn mua và tiến hành thanh toán an toàn</p>
        </div>
    </div>

    <!-- BANNER KHÔI PHỤC ĐƠN HÀNG CHỜ THANH TOÁN (PENDING ORDER RESUME) -->
    @if(isset($pendingOrder) && $pendingOrder)
        <div class="mb-8 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">Bạn có 1 đơn hàng chưa hoàn tất thanh toán</h4>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">Mã đơn: <span class="font-mono font-bold">#{{ $pendingOrder->order_code }}</span> ({{ number_format($pendingOrder->total_amount, 0, ',', '.') }}đ)</p>
                </div>
            </div>
            <a href="{{ route('student.checkout.pay', $pendingOrder->order_code) }}" 
               class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs transition shadow-sm inline-flex items-center justify-center gap-1.5 shrink-0">
                <span>Tiếp tục quét mã QR</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    @endif

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
                'title' => $c->title,
                'instructor' => $c->instructor?->name ?? 'Giảng viên',
                'price' => (float)($c->discount_price ?? $c->sale_price ?? $c->price)
            ])) }},
            checkedIds: {{ json_encode($cart->courses->pluck('id')) }},
            selectAll: true,
            paymentMethod: 'bank_transfer',
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
                let isPercentage = this.appliedCoupon.type === 'percent';
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
            },
            async removeItem(courseId) {
                try {
                    let response = await fetch('/student/cart/remove-ajax/' + courseId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    let data = await response.json();

                    if (!response.ok || !data?.success) {
                        this.showNotification(this.getActionErrorMessage(data), 'error');
                        return;
                    }

                    this.courses = this.courses.filter(c => Number(c.id) !== Number(courseId));
                    this.checkedIds = this.checkedIds.filter(id => Number(id) !== Number(courseId));
                    this.updateSelectAll();
                    this.showNotification('Đã xóa khóa học khỏi giỏ hàng.', 'success');
                } catch (e) {
                    console.error(e);
                    this.showNotification('Không thể thực hiện thao tác. Vui lòng thử lại.', 'error');
                }
            },
            async moveToWishlist(courseId) {
                try {
                    let response = await fetch('/student/cart/move-to-wishlist/' + courseId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    let data = await response.json();

                    if (!response.ok || !data?.success) {
                        this.showNotification(this.getActionErrorMessage(data), 'error');
                        return;
                    }

                    this.courses = this.courses.filter(c => Number(c.id) !== Number(courseId));
                    this.checkedIds = this.checkedIds.filter(id => Number(id) !== Number(courseId));
                    this.updateSelectAll();
                    this.showNotification(data.message, 'success');
                } catch (e) {
                    console.error(e);
                    this.showNotification('Không thể thực hiện thao tác. Vui lòng thử lại.', 'error');
                }
            },
            getActionErrorMessage(data) {
                return typeof data?.message === 'string' && data.message.trim() !== ''
                    ? data.message
                    : 'Không thể thực hiện thao tác. Vui lòng thử lại.';
            },
            showNotification(msg, type = 'success') {
                if (window.AppToast?.show) {
                    window.AppToast.show({ message: msg, type });
                    return;
                }

                console.error('Shared toast API is unavailable.');
            }
        }" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <!-- Danh sách khóa học trong giỏ hàng -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Chọn tất cả -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center justify-between shadow-sm select-none">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="w-4 h-4 text-[#0056D2] border-slate-300 rounded focus:ring-[#0056D2] cursor-pointer">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Chọn tất cả khóa học (<span x-text="checkedIds.length"></span>/<span x-text="courses.length"></span>)</span>
                    </div>
                </div>

                <!-- Danh sách items -->
                <template x-if="courses.length === 0">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center">
                        <p class="text-slate-500 dark:text-slate-400">Bạn đã xóa hết khóa học khỏi giỏ.</p>
                        <a href="{{ route('home') }}#courses" class="inline-block mt-3 text-xs font-bold text-[#0056D2] dark:text-blue-400 hover:underline">Khám phá thêm khóa học</a>
                    </div>
                </template>

                <template x-for="course in courses" :key="course.id">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col sm:flex-row sm:items-center gap-4 shadow-sm select-none transition hover:border-slate-300 dark:hover:border-slate-700">
                        <div class="flex items-center gap-3 shrink-0">
                            <input type="checkbox" :value="course.id" x-model="checkedIds" @change="updateSelectAll()" class="w-4 h-4 text-[#0056D2] border-slate-300 rounded focus:ring-[#0056D2] cursor-pointer shrink-0">
                            <div class="w-14 h-14 bg-blue-50 dark:bg-blue-950/40 text-[#0056D2] dark:text-blue-300 rounded-xl flex items-center justify-center font-extrabold shrink-0 text-xl">
                                <span x-text="course.title.charAt(0).toUpperCase()"></span>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-slate-900 dark:text-white truncate text-base" x-text="course.title"></h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" x-text="course.instructor"></p>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between gap-2 shrink-0 border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100 dark:border-slate-800">
                            <div class="font-extrabold text-lg text-[#0056D2] dark:text-blue-300" x-text="formatMoney(course.price)"></div>
                            <div class="flex items-center gap-3 text-xs font-semibold">
                                <button type="button" 
                                        @click="moveToWishlist(course.id)" 
                                        class="text-slate-500 hover:text-[#0056D2] dark:hover:text-blue-400 transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    <span>Yêu thích</span>
                                </button>
                                <span class="text-slate-300 dark:text-slate-700">|</span>
                                <button type="button" 
                                        @click="removeItem(course.id)" 
                                        class="text-rose-500 hover:text-rose-600 transition">
                                    Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- KHỐI GỢI Ý KHÓA HỌC MUA KÈM (FREQUENTLY BOUGHT TOGETHER) -->
                @if(isset($suggestedCourses) && $suggestedCourses->isNotEmpty())
                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-800">
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <span>💡 Có thể bạn cũng thích</span>
                            <span class="text-xs font-medium text-slate-400">(Khóa học phổ biến)</span>
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($suggestedCourses as $suggested)
                                @php $sPrice = $suggested->discount_price ?? $suggested->sale_price ?? $suggested->price; @endphp
                                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex flex-col justify-between shadow-sm">
                                    <div>
                                        <div class="w-full h-24 bg-gradient-to-br from-blue-500/10 to-indigo-500/10 rounded-xl mb-3 flex items-center justify-center font-black text-blue-600 text-2xl">
                                            {{ strtoupper(substr($suggested->title, 0, 1)) }}
                                        </div>
                                        <h4 class="font-bold text-xs text-slate-900 dark:text-white line-clamp-2">{{ $suggested->title }}</h4>
                                        <p class="text-[11px] text-slate-400 mt-1">{{ $suggested->instructor?->name }}</p>
                                    </div>
                                    <div class="mt-3 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                        <span class="font-extrabold text-sm text-[#0056D2] dark:text-blue-400">{{ number_format($sPrice, 0, ',', '.') }}đ</span>
                                        <form method="POST" action="{{ route('student.cart.add', $suggested->id) }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/60 text-[#0056D2] dark:text-blue-300 hover:bg-blue-100 rounded-lg text-[11px] font-bold transition">
                                                + Thêm vào giỏ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
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
                    
                    <!-- Nhắc nhở thiếu tiền để đạt điều kiện coupon -->
                    <template x-if="appliedCoupon && !isCouponConditionMet">
                        <div class="text-xs text-rose-500 font-semibold bg-rose-50 dark:bg-rose-950/20 p-2.5 rounded-lg space-y-1">
                            <div>Mã <span class="font-bold" x-text="appliedCoupon?.code"></span> yêu cầu đơn hàng tối thiểu <span x-text="formatMoney(appliedCoupon?.min_order_amount || 0)"></span>.</div>
                            <div class="text-rose-600 font-bold">💡 Mua thêm <span x-text="formatMoney(appliedCoupon.min_order_amount - total)"></span> để được giảm giá!</div>
                        </div>
                    </template>

                    <div class="flex justify-between text-slate-900 dark:text-white font-extrabold text-lg pt-1 border-t border-slate-50 dark:border-slate-800/40">
                        <span>Tổng cộng</span>
                        <span class="text-[#0056D2] dark:text-blue-300" x-text="formatMoney(grandTotal)"></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('student.cart.checkout') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ (string) Str::uuid() }}">
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
                                            <span class="text-slate-500 font-medium" x-text="cp.type === 'percent' ? 'Giảm ' + parseFloat(cp.value) + '%' : 'Giảm ' + formatMoney(cp.value)"></span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1" x-show="parseFloat(cp.min_order_amount) > 0">
                                            Đơn tối thiểu: <span x-text="formatMoney(cp.min_order_amount)"></span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="applyCoupon(cp.code)"
                                            :disabled="appliedCoupon?.code === cp.code"
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
                            <!-- Bank Transfer / PayOS Option -->
                            <div @click="paymentMethod = 'bank_transfer'" 
                                 :class="paymentMethod === 'bank_transfer' ? 'border-emerald-600 bg-emerald-50/20 dark:border-emerald-500' : 'border-slate-200 hover:border-slate-300 dark:border-slate-800'"
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition select-none">
                                <div class="flex items-center gap-3">
                                    <div class="h-6 w-6 flex items-center justify-center shrink-0 text-emerald-600 dark:text-emerald-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 dark:text-white block">Chuyển khoản ngân hàng (PayOS VietQR)</span>
                                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Tự động kích hoạt qua quét mã QR</span>
                                    </div>
                                </div>
                                <div class="h-4 w-4 rounded-full border-2 flex items-center justify-center shrink-0"
                                     :class="paymentMethod === 'bank_transfer' ? 'border-emerald-600 bg-emerald-600 dark:border-emerald-400 dark:bg-emerald-400' : 'border-slate-300'">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="paymentMethod === 'bank_transfer'"></div>
                                </div>
                            </div>

                            <!-- MoMo Option -->
                            <div @click="paymentMethod = 'momo'"
                                 :class="paymentMethod === 'momo' ? 'border-[#a50064] bg-pink-50/50 dark:border-pink-400 dark:bg-pink-950/20' : 'border-slate-200 hover:border-slate-300 dark:border-slate-800'"
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition select-none">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-12 rounded-lg bg-[#a50064] text-white flex items-center justify-center shrink-0 text-[10px] font-black tracking-tight">MoMo</div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 dark:text-white block">MoMo</span>
                                        <span class="text-[10px] text-[#a50064] dark:text-pink-300 font-medium">Thanh toán thử nghiệm qua ví MoMo</span>
                                    </div>
                                </div>
                                <div class="h-4 w-4 rounded-full border-2 flex items-center justify-center shrink-0"
                                     :class="paymentMethod === 'momo' ? 'border-[#a50064] bg-[#a50064]' : 'border-slate-300'">
                                    <div class="h-1.5 w-1.5 rounded-full bg-white" x-show="paymentMethod === 'momo'"></div>
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
