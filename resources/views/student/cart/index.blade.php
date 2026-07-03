<x-student-layout title="Giỏ hàng" page-title="Giỏ hàng" breadcrumb="Thanh toán khóa học">

@if($cart->items->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        <p class="text-slate-600 text-lg font-medium">Giỏ hàng trống</p>
        <a href="{{ route('home') }}#courses" class="inline-block mt-4 text-[#0056D2] font-medium hover:underline">Tiếp tục mua sắm →</a>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
            @foreach($cart->items as $item)
                @php $course = $item->course; $price = $course->discount_price ?? $course->sale_price ?? $course->price; @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-50 dark:bg-blue-950/40 text-[#0056D2] dark:text-blue-300 rounded-xl flex items-center justify-center font-bold shrink-0">
                        {{ strtoupper(substr($course->title, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-slate-900">{{ $course->title }}</h3>
                        <p class="text-sm text-slate-500">{{ $course->instructor?->name }}</p>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-[#0056D2]">{{ number_format($price, 0, ',', '.') }}đ</div>
                        <form method="POST" action="{{ route('student.cart.remove', $course->id) }}" class="mt-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:underline">Xóa</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit sticky top-24">
            <h3 class="font-bold text-slate-900 text-lg mb-4">Thanh toán</h3>
            <div class="flex justify-between text-slate-600 mb-2">
                <span>Tạm tính</span>
                <span>{{ number_format($total, 0, ',', '.') }}đ</span>
            </div>
            <div class="flex justify-between font-bold text-lg text-slate-900 border-t border-slate-200 pt-4 mt-4 mb-6">
                <span>Tổng cộng</span>
                <span class="text-[#0056D2]">{{ number_format($total, 0, ',', '.') }}đ</span>
            </div>
            <form method="POST" action="{{ route('student.cart.checkout') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Mã giảm giá</label>
                    <input type="text" name="coupon_code" placeholder="Nhập mã (VD: WELCOME20)"
                           class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-[#0056D2] outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Phương thức thanh toán</label>
                    <select name="payment_method" required class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm bg-white focus:ring-2 focus:ring-[#0056D2] outline-none">
                        <option value="vnpay">VNPay</option>
                        <option value="momo">MoMo</option>
                        <option value="bank_transfer">Chuyển khoản</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#0056D2] text-white font-semibold py-3 rounded-xl transition hover:bg-[#0046B8]">
                    Thanh toán ngay
                </button>
            </form>
        </div>
    </div>
@endif

</x-student-layout>
