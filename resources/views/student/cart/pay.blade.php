@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng #'.$order->order_code.' - Website học online FEA')

@section('content')
<div class="bg-slate-50 py-8 dark:bg-slate-950 min-h-[calc(100vh-16rem)]">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- NAV QUAY LẠI -->
        <div class="flex items-center justify-between">
            <a href="{{ route('student.orders') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại Danh sách đơn hàng
            </a>

            <span class="font-mono text-xs font-bold text-slate-500">Mã đơn: #{{ $order->order_code }}</span>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- CỘT CHỌN CỔNG THANH TOÁN (2 COLS) -->
            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <h1 class="text-xl font-extrabold text-slate-950 dark:text-white">Lựa chọn phương thức thanh toán</h1>
                    <p class="mt-1 text-xs text-slate-500">Vui lòng chọn cổng thanh toán bạn muốn sử dụng để hoàn tất đơn hàng này</p>

                    <form method="POST" action="{{ route('student.checkout.process_payment', $order->order_code) }}" class="mt-6 space-y-4" x-data="{ selectedGateway: 'payos' }">
                        @csrf

                        <!-- CỔNG 1: PAYOS VIETQR (TỰ ĐỘNG) -->
                        <label class="relative flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition"
                               :class="selectedGateway === 'payos' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20 dark:border-indigo-500 dark:bg-indigo-950/20' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950'">
                            <div class="flex items-center gap-4">
                                <input type="radio" name="payment_method" value="payos" x-model="selectedGateway" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 font-extrabold text-xs">
                                    PayOS
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-slate-950 dark:text-white">Thanh toán tự động PayOS (VietQR)</span>
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-extrabold text-emerald-700">Khuyên dùng</span>
                                    </div>
                                    <p class="mt-0.5 text-xs text-slate-500">Quét mã VietQR thanh toán tức thì qua App Ngân hàng (MB, VCB, Techcombank, ...)</p>
                                </div>
                            </div>
                        </label>



                        <!-- CỔNG 3: MOMO -->
                        <label class="relative flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition"
                               :class="selectedGateway === 'momo' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20 dark:border-indigo-500 dark:bg-indigo-950/20' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950'">
                            <div class="flex items-center gap-4">
                                <input type="radio" name="payment_method" value="momo" x-model="selectedGateway" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100 text-pink-700 font-extrabold text-xs">
                                    MoMo
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-slate-950 dark:text-white">Ví điện tử MoMo</span>
                                    <p class="mt-0.5 text-xs text-slate-500">Quét mã QR bằng ứng dụng Ví MoMo trên điện thoại</p>
                                </div>
                            </div>
                        </label>

                        <!-- CỔNG 4: CHUYỂN KHOẢN NGÂN HÀNG THỦ CÔNG -->
                        <label class="relative flex cursor-pointer items-center justify-between rounded-2xl border p-4 transition"
                               :class="selectedGateway === 'bank_transfer' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500/20 dark:border-indigo-500 dark:bg-indigo-950/20' : 'border-slate-200 bg-white hover:border-slate-300 dark:border-slate-800 dark:bg-slate-950'">
                            <div class="flex items-center gap-4">
                                <input type="radio" name="payment_method" value="bank_transfer" x-model="selectedGateway" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 font-extrabold text-xs dark:bg-slate-800 dark:text-slate-200">
                                    BANK
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-slate-950 dark:text-white">Chuyển khoản Ngân hàng (MB Bank)</span>
                                    <p class="mt-0.5 text-xs text-slate-500">Chuyển khoản thủ công theo thông tin số tài khoản và cú pháp đơn hàng</p>
                                </div>
                            </div>
                        </label>

                        <div class="pt-4">
                            <button type="submit" class="h-12 w-full rounded-xl bg-indigo-600 text-sm font-extrabold text-white transition hover:bg-indigo-700 shadow-md flex items-center justify-center gap-2">
                                <span>Tiến hành thanh toán ngay</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CỘT BÊN PHẢI: TỔNG KẾT ĐƠN HÀNG (1 COL) -->
            <div class="space-y-4">
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

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-3 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Tạm tính</span>
                            <span>{{ number_format($order->subtotal ?? $order->total_amount, 0, ',', '.') }}đ</span>
                        </div>
                        @if(($order->discount_amount ?? 0) > 0)
                            <div class="flex justify-between text-emerald-600 font-semibold">
                                <span>Giảm giá</span>
                                <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center border-t border-slate-100 dark:border-slate-800 pt-2 text-sm">
                            <span class="font-extrabold text-slate-950 dark:text-white">Tổng cộng</span>
                            <span class="font-extrabold text-base text-indigo-600 dark:text-indigo-400">
                                {{ number_format($order->total_amount, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
