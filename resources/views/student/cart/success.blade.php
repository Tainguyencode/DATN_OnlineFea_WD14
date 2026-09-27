<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thanh toán thành công - FEA Online</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <x-public.header />
<main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">

    <div class="mx-auto w-full max-w-xl text-center space-y-6">
        
        <!-- Premium Checkmark Animation -->
        <div class="inline-flex items-center justify-center">
            <div class="relative">
                <!-- Pulsing outer rings -->
                <div class="absolute inset-0 rounded-full bg-emerald-100 animate-ping opacity-75"></div>
                <div class="absolute inset-0 rounded-full bg-emerald-50 scale-125 opacity-50"></div>
                <!-- Main circle -->
                <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">
                    <svg class="h-10 w-10 stroke-current" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div class="space-y-2">
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Thanh toán thành công!</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Khóa học đã được đăng ký và kích hoạt thành công.</p>
        </div>

        <!-- Receipt Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm text-left space-y-4 dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 dark:border-slate-800 dark:text-white">Chi tiết giao dịch</h3>
            
            <div class="grid grid-cols-2 gap-y-3 text-xs leading-relaxed">
                <span class="text-slate-500">Mã đơn hàng:</span>
                <strong class="text-slate-900 font-mono text-right dark:text-white">{{ $order->order_code }}</strong>

                <span class="text-slate-500">Phương thức thanh toán:</span>
                <strong class="text-slate-900 text-right dark:text-white">
                    PayOS VietQR
                </strong>

                <span class="text-slate-500">Mã giao dịch:</span>
                <strong class="text-slate-900 font-mono text-right dark:text-white">{{ $order->transaction_id ?? 'N/A' }}</strong>

                <span class="text-slate-500">Ngày giờ thanh toán:</span>
                <strong class="text-slate-900 text-right dark:text-white">{{ $order->updated_at->format('H:i d/m/Y') }}</strong>

                <span class="text-slate-500 font-semibold">Số tiền đã thanh toán:</span>
                <strong class="text-sm font-black text-[#0056D2] text-right">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-2 dark:border-slate-800">
                <h4 class="text-xs font-bold text-slate-800 mb-3 dark:text-slate-200">Danh sách khóa học đã mua:</h4>
                <div class="space-y-3">
                    @foreach($orderItems as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 text-[#0056D2] rounded-lg flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr($item->course?->title ?? 'C', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h5 class="font-bold text-xs text-slate-900 truncate dark:text-white">{{ $item->course?->title }}</h5>
                                <p class="text-[10px] text-slate-500 mt-0.5">Giảng viên: {{ $item->course?->instructor?->name }}</p>
                            </div>
                            <span class="text-xs font-bold text-slate-900 shrink-0 dark:text-white">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center gap-3 border-t border-slate-100 dark:border-slate-800">
                <x-payment-learning-links :order="$order" />
                <a href="{{ route('home') }}" class="text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-3 rounded-xl transition dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    Trang chủ
                </a>
            </div>
        </div>

    </div>

</main>
</body>
</html>
