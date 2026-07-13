<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán Thất bại - FEA Online</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-[#fcfcfc] text-slate-900 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-xl text-center space-y-6">
        
        <!-- Premium Crossmark Animation -->
        <div class="inline-flex items-center justify-center">
            <div class="relative">
                <!-- Pulsing outer rings -->
                <div class="absolute inset-0 rounded-full bg-rose-100 animate-ping opacity-75"></div>
                <div class="absolute inset-0 rounded-full bg-rose-50 scale-125 opacity-50"></div>
                <!-- Main circle -->
                <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-rose-500 text-white shadow-lg shadow-rose-500/20">
                    <svg class="h-10 w-10 stroke-current" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Failure Message -->
        <div class="space-y-2">
            <h2 class="text-2xl font-black text-slate-900">Thanh toán không thành công!</h2>
            <p class="text-xs text-slate-500">Giao dịch đã bị hủy hoặc gặp lỗi trong quá trình xử lý.</p>
            <p class="text-xs text-rose-600 font-semibold bg-rose-50 inline-block px-3 py-1.5 rounded-full mt-2">
                Tự động quay về giỏ hàng sau <span id="countdown" class="font-extrabold text-sm">5</span> giây...
            </p>
        </div>

        <!-- Receipt Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm text-left space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3">Chi tiết giao dịch</h3>
            
            <div class="grid grid-cols-2 gap-y-3 text-xs leading-relaxed">
                <span class="text-slate-500">Mã đơn hàng:</span>
                <strong class="text-slate-900 font-mono text-right">{{ $order->order_code }}</strong>

                <span class="text-slate-500">Phương thức thanh toán:</span>
                <strong class="text-slate-900 text-right">
                    @if($order->payment_method === 'vnpay') VNPay
                    @elseif($order->payment_method === 'momo') MoMo
                    @else Chuyển khoản
                    @endif
                </strong>

                <span class="text-slate-500">Mã giao dịch:</span>
                <strong class="text-slate-900 font-mono text-right">{{ $order->transaction_id ?? 'N/A' }}</strong>

                <span class="text-slate-500">Thời gian giao dịch:</span>
                <strong class="text-slate-900 text-right">{{ $order->updated_at->format('H:i d/m/Y') }}</strong>

                <span class="text-slate-500 font-semibold">Số tiền cần thanh toán:</span>
                <strong class="text-sm font-black text-rose-600 text-right">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-2">
                <h4 class="text-xs font-bold text-slate-800 mb-3">Danh sách khóa học đăng ký:</h4>
                <div class="space-y-3">
                    @foreach($orderItems as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 text-[#0056D2] rounded-lg flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr($item->course?->title ?? 'C', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <h5 class="font-bold text-xs text-slate-900 truncate">{{ $item->course?->title }}</h5>
                                <p class="text-[10px] text-slate-500 mt-0.5">Giảng viên: {{ $item->course?->instructor?->name }}</p>
                            </div>
                            <span class="text-xs font-bold text-slate-900 shrink-0">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex gap-3">
                <a href="{{ route('student.cart') }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-3 px-4 rounded-xl transition duration-150">
                    Quay lại giỏ hàng
                </a>
                <a href="{{ route('student.checkout.pay', $order->order_code) }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-3 px-4 rounded-xl transition duration-150 shadow-sm shadow-blue-500/10">
                    Thử thanh toán lại
                </a>
            </div>
        </div>

    </div>

    <!-- Redirect script -->
    <script>
        var count = 5;
        var counter = setInterval(timer, 1000);
        function timer() {
            count = count - 1;
            if (count <= 0) {
                clearInterval(counter);
                window.location.href = "{{ route('student.cart') }}";
                return;
            }
            document.getElementById("countdown").innerHTML = count;
        }
    </script>

</body>
</html>
