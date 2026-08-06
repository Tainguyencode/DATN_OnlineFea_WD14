@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #'.$order->order_code.' - Website học online FEA')

@section('content')
<div class="bg-slate-50 py-8 dark:bg-slate-950 min-h-[calc(100vh-16rem)]">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- NÚT QUAY LẠI VÀ THAO TÁC IN -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('student.orders') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại danh sách đơn hàng
            </a>

            <div class="flex items-center gap-2 print:hidden">
                <button onclick="window.print()" class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 shadow-sm">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    In hóa đơn
                </button>
            </div>
        </div>

        <!-- HEADER HÓA ĐƠN -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between border-b border-slate-100 dark:border-slate-800 pb-5">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Hóa đơn mua khóa học</span>
                    <h1 class="mt-1 font-mono text-2xl font-extrabold text-slate-950 dark:text-white">
                        #{{ $order->order_code }}
                    </h1>
                    <p class="mt-1 text-xs text-slate-500">
                        Thời gian tạo: {{ $order->created_at->format('d/m/Y H:i:s') }}
                    </p>
                </div>

                <div>
                    @if($order->status === 'paid')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-4 py-1.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Đã thanh toán
                        </span>
                    @elseif($order->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-4 py-1.5 text-xs font-bold text-amber-700 border border-amber-200">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span> Chờ thanh toán
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-4 py-1.5 text-xs font-bold text-rose-700 border border-rose-200">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span> {{ ucfirst($order->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- THÔNG TIN CHI TIẾT ĐƠN HÀNG GRID -->
            <div class="mt-6 grid gap-6 lg:grid-cols-3">
                <!-- CỘT BÊN TRÁI: KHÓA HỌC ĐÃ MUA -->
                <div class="space-y-4 lg:col-span-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Khóa học trong đơn hàng</h3>

                    <div class="divide-y divide-slate-100 rounded-2xl border border-slate-200 bg-white overflow-hidden dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-950">
                        @foreach($order->items ?? [] as $item)
                            @php
                                $isModel = $item instanceof \App\Models\OrderItem;
                                $course = $isModel ? $item->course : null;
                                $itemPrice = $isModel ? $item->price : ($item['price'] ?? 0);
                                $entryUrl = $course?->learningEntryUrl();
                            @endphp
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="h-16 w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-slate-800">
                                        <img src="{{ $course?->thumbnailUrl() ?? 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=200&auto=format&fit=crop&q=80' }}" alt="{{ $course?->title }}" class="h-full w-full object-cover" loading="lazy">
                                    </div>
                                    <div class="space-y-1">
                                        <a href="{{ $course ? route('courses.show', $course->slug) : '#' }}" class="font-extrabold text-sm text-slate-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">
                                            {{ $course?->title ?? ($item['title'] ?? 'Khóa học') }}
                                        </a>
                                        <p class="text-xs text-slate-500">
                                            Giảng viên: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $course?->instructor?->name ?? 'FEA Academy' }}</span>
                                        </p>
                                        @if($course?->lessons_count)
                                            <p class="text-[11px] text-slate-400">{{ $course->lessons_count }} bài học</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col sm:items-end justify-between gap-2 shrink-0">
                                    <div class="text-right">
                                        <span class="font-bold text-sm text-slate-950 dark:text-white">
                                            {{ number_format($itemPrice, 0, ',', '.') }}đ
                                        </span>
                                    </div>
                                    
                                    @if($order->status === 'paid' && $entryUrl)
                                        <a href="{{ $entryUrl }}" class="inline-flex h-9 items-center justify-center rounded-xl bg-emerald-600 px-4 text-xs font-bold text-white transition hover:bg-emerald-700 shadow-sm">
                                            Vào học ngay
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- KHU VỰC TỔNG KẾT TÀI CHÍNH -->
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60 space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng kết thanh toán</h4>

                        @php
                            $subtotal = collect($order->items ?? [])->sum(fn ($i) => (float) ($i instanceof \App\Models\OrderItem ? $i->price : ($i['price'] ?? 0)));
                            $discount = max(0, $subtotal - $order->total_amount);
                        @endphp

                        <div class="flex justify-between text-xs text-slate-600 dark:text-slate-400">
                            <span>Tổng tiền gốc các khóa học:</span>
                            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                        </div>

                        @if($discount > 0 || $order->coupon)
                            <div class="flex justify-between text-xs text-emerald-600 dark:text-emerald-400">
                                <span>Giảm giá @if($order->coupon)({{ $order->coupon->code }})@endif:</span>
                                <span class="font-semibold">-{{ number_format($discount, 0, ',', '.') }}đ</span>
                            </div>
                        @endif

                        <div class="border-t border-slate-200 dark:border-slate-800 pt-3 flex justify-between items-center text-sm">
                            <span class="font-extrabold text-slate-900 dark:text-white">Tổng tiền thực tế đã thanh toán:</span>
                            <span class="font-extrabold text-lg text-indigo-600 dark:text-indigo-400">
                                {{ number_format($order->total_amount, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>

                <!-- CỘT BÊN PHẢI: THÔNG TIN PHƯƠNG THỨC THANH TOÁN & GIAO DỊCH -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Thông tin giao dịch</h3>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-4 text-xs">
                        <div>
                            <span class="block font-medium text-slate-400 uppercase tracking-wider text-[10px]">Phương thức thanh toán</span>
                            <strong class="mt-1 block font-bold text-sm text-slate-900 dark:text-white">
                                @if(str_contains(strtolower($order->payment_method ?? ''), 'momo'))
                                    💖 MoMo Wallet
                                @elseif(str_contains(strtolower($order->payment_method ?? ''), 'vnpay'))
                                    💳 VNPay Gateway
                                @else
                                    🏛️ Chuyển khoản Ngân hàng (MB Bank)
                                @endif
                            </strong>
                        </div>

                        @if($order->payment)
                            <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                                <span class="block font-medium text-slate-400 uppercase tracking-wider text-[10px]">Mã giao dịch ngân hàng</span>
                                <strong class="mt-1 block font-mono font-bold text-slate-800 dark:text-slate-200">
                                    {{ $order->payment->transaction_code ?? $order->payment->vnp_transaction_no ?? 'N/A' }}
                                </strong>
                            </div>

                            <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                                <span class="block font-medium text-slate-400 uppercase tracking-wider text-[10px]">Thời gian xác nhận</span>
                                <strong class="mt-1 block font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $order->payment->paid_at ? $order->payment->paid_at->format('d/m/Y H:i:s') : $order->created_at->format('d/m/Y H:i:s') }}
                                </strong>
                            </div>
                        @endif

                        <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                            <span class="block font-medium text-slate-400 uppercase tracking-wider text-[10px]">Tài khoản mua hàng</span>
                            <strong class="mt-1 block font-semibold text-slate-800 dark:text-slate-200">
                                {{ auth()->user()->name }} ({{ auth()->user()->email }})
                            </strong>
                        </div>
                    </div>

                    @if($order->status === 'pending')
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/60 dark:bg-amber-950/40 text-xs text-amber-900 dark:text-amber-200 space-y-3">
                            <p class="font-bold">⚡ Đơn hàng chưa hoàn tất thanh toán</p>
                            <p class="leading-relaxed">Vui lòng bấm nút dưới đây để hoàn tất thanh toán và kích hoạt quyền học tập ngay.</p>
                            <a href="{{ route('student.checkout.pay', $order->order_code) }}" class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-amber-500 font-bold text-white transition hover:bg-amber-600 shadow-sm">
                                Thanh toán ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
