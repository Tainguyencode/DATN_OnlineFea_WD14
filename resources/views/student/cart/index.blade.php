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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- Danh sách khóa học trong giỏ hàng -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart->courses as $course)
                    @php 
                        $price = $course->discount_price ?? $course->sale_price ?? $course->price; 
                    @endphp
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex items-center gap-4 shadow-sm">
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
                        <span class="font-semibold text-slate-800 dark:text-white">{{ number_format($total, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex justify-between text-slate-900 dark:text-white font-extrabold text-lg pt-1">
                        <span>Tổng cộng</span>
                        <span class="text-[#0056D2] dark:text-blue-300">{{ number_format($total, 0, ',', '.') }}đ</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('student.cart.checkout') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Mã giảm giá</label>
                        <input type="text" name="coupon_code" placeholder="Nhập mã (VD: WELCOME20)"
                               class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-[#0056D2] dark:bg-slate-950 dark:text-white outline-none">
                    </div>
                    
                    <div x-data="{ paymentMethod: 'vnpay' }" class="space-y-3">
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Phương thức thanh toán</label>
                        
                        <div class="grid grid-cols-1 gap-2.5">
                            <!-- VNPay Option -->
                            <div @click="paymentMethod = 'vnpay'" 
                                 :class="paymentMethod === 'vnpay' ? 'border-[#0056D2] bg-blue-50/20 dark:border-blue-500' : 'border-slate-200 hover:border-slate-300 dark:border-slate-800'"
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition">
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
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition">
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
                                 class="flex items-center justify-between p-3.5 rounded-xl border-2 cursor-pointer transition">
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

                    <button type="submit" class="w-full bg-[#0056D2] text-white font-bold py-3.5 rounded-xl transition hover:bg-[#0046B8] shadow-md mt-6 cursor-pointer">
                        Thanh toán ngay
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
