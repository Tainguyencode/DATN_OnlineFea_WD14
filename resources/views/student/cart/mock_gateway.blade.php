<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán giả lập - FEA Online</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (đã có sẵn trong project) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-[#fcfcfc] text-slate-900 min-h-screen flex flex-col justify-between">

@php
    $isMoMo = $gateway === 'momo';
@endphp

    @if($isMoMo)
        <!-- Giao diện MoMo -->
        <header class="bg-white border-b border-slate-100 py-4 px-6 sm:px-12 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/momo-logo.jpg') }}" alt="MoMo Logo" class="w-10 h-10 rounded-xl object-contain">
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Cổng thanh toán MoMo</h1>
                    <p class="text-[10px] text-slate-400">Môi trường thử nghiệm Sandbox</p>
                </div>
            </div>
            <a href="{{ route('student.dashboard') }}" class="text-xs text-slate-400 hover:text-slate-600 transition">Quay về</a>
        </header>

        <main class="flex-1 max-w-5xl w-full mx-auto p-6 sm:p-12 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3">Thông tin đơn hàng</h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Nhà cung cấp</span>
                            <strong class="text-slate-900">FEA Online Learning Platform</strong>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Mã đơn hàng</span>
                            <strong class="text-slate-900 font-mono">{{ $order->order_code }}</strong>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Mô tả giao dịch</span>
                            <strong class="text-slate-900">Thanh toán đơn hàng {{ $order->order_code }}</strong>
                        </div>
                        <div class="border-t border-slate-100 pt-3 flex justify-between items-center">
                            <span class="text-sm font-semibold text-slate-600">Số tiền thanh toán</span>
                            <strong class="text-2xl font-black text-slate-900">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                        </div>
                    </div>
                </div>

                <div class="bg-pink-50/50 border border-pink-100/50 rounded-2xl p-4 flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-medium">Đơn hàng sẽ hết hạn sau:</span>
                    <div class="flex items-center gap-1 font-bold text-pink-600">
                        <span class="bg-pink-100 rounded px-1.5 py-0.5">09</span>
                        <span>Phút</span>
                        <span class="bg-pink-100 rounded px-1.5 py-0.5" id="momo-sec">54</span>
                        <span>Giây</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center">
                <div class="bg-[#d82d8b] w-full max-w-[380px] rounded-3xl p-6 text-white text-center shadow-lg space-y-4">
                    <div class="flex items-center justify-between text-xs border-b border-white/10 pb-3">
                        <span class="font-medium opacity-80">Quét mã MoMo</span>
                        <span class="font-bold bg-white/20 px-2 py-0.5 rounded uppercase tracking-wider">MoMo Pay</span>
                    </div>
                    <div class="relative bg-white p-3 rounded-2xl inline-block shadow-md mx-auto">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=momo://pay?amount={{ $order->total_amount }}&orderId={{ $order->order_code }}" alt="MoMo QR Code" class="w-48 h-48 block">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img src="{{ asset('images/momo-logo.jpg') }}" alt="MoMo Icon" class="w-8 h-8 rounded-lg border-2 border-white bg-white">
                        </div>
                    </div>
                    <div class="space-y-2 text-xs">
                        <p class="font-bold flex items-center justify-center gap-1.5">Sử dụng App MoMo để quét mã</p>
                        <p class="opacity-80 leading-relaxed text-[10px]">Hoặc dùng ứng dụng camera điện thoại hỗ trợ QR code để quét thanh toán nhanh.</p>
                    </div>
                </div>

                <!-- Simulator Control panel for MoMo -->
                <div class="w-full max-w-[380px] mt-6 bg-amber-50 border border-amber-200 rounded-3xl p-5 text-left text-xs leading-relaxed space-y-4">
                    <div class="flex items-center gap-2 text-amber-800 font-bold">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Giả lập kết quả Sandbox MoMo</span>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}">
                            @csrf
                            <input type="hidden" name="status" value="success">
                            <button type="submit" class="w-full text-white bg-emerald-600 hover:bg-emerald-700 font-bold py-2.5 rounded-xl transition duration-150 shadow-sm text-center cursor-pointer">
                                Thanh toán thành công
                            </button>
                        </form>

                        <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}">
                            @csrf
                            <input type="hidden" name="status" value="failed">
                            <button type="submit" class="w-full text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 font-bold py-2.5 rounded-xl transition duration-150 shadow-sm text-center cursor-pointer">
                                Thanh toán thất bại
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        
        <script>
            setInterval(function() {
                var el = document.getElementById('momo-sec');
                if (el) {
                    var sec = parseInt(el.innerText);
                    if (sec > 0) { sec--; el.innerText = sec < 10 ? '0' + sec : sec; } else { el.innerText = '59'; }
                }
            }, 1000);
        </script>
    @else
        <!-- Giao diện VNPay giống hệt mẫu screenshot -->
        <header class="bg-white border-b border-slate-100 py-4 px-6 sm:px-12 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <div class="h-10 w-24 flex items-center">
                    <img src="{{ asset('images/vnpay-logo.png') }}" alt="VNPay Logo" class="h-full w-full object-contain">
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Cổng thanh toán VNPay</h1>
                    <p class="text-[10px] text-slate-400">Môi trường thử nghiệm Sandbox</p>
                </div>
            </div>
            <a href="{{ route('student.dashboard') }}" class="text-xs text-slate-400 hover:text-slate-600 transition">Quay về</a>
        </header>

        <main class="flex-1 w-full max-w-5xl mx-auto p-6 sm:p-12 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
                <!-- Cột trái: Thông tin đơn hàng (5/12 width) -->
                <div class="md:col-span-5 bg-slate-50 border border-slate-200/50 rounded-lg p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200/60 pb-3">Thông tin đơn hàng</h3>
                    
                    <div class="space-y-4 text-xs">
                        <div class="space-y-1">
                            <span class="text-slate-400 block">Số tiền thanh toán</span>
                            <strong class="text-xl font-bold text-[#0056D2]">{{ number_format($order->total_amount, 0, ',', '.') }} VND</strong>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 block">Giá trị đơn hàng</span>
                            <strong class="text-sm font-semibold text-slate-800">{{ number_format($order->total_amount, 0, ',', '.') }} VND</strong>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 block">Phí giao dịch</span>
                            <strong class="text-sm font-semibold text-slate-800">0 VND</strong>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 block">Mã đơn hàng</span>
                            <strong class="text-sm font-bold text-slate-800 font-mono">{{ $order->order_code }}</strong>
                        </div>
                        <div class="space-y-1">
                            <span class="text-slate-400 block">Nhà cung cấp</span>
                            <strong class="text-sm font-bold text-slate-800">FEA ONLINE PLATFORM</strong>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Quét mã QR và Hủy thanh toán (7/12 width) -->
                <div class="md:col-span-7 flex flex-col items-center justify-center text-center space-y-4 py-4">
                    <h2 class="text-lg font-bold text-slate-800">Quét mã qua App Ngân hàng/ Ví điện tử</h2>
                    
                    <div class="flex items-center gap-1.5 text-xs text-sky-600 font-medium hover:underline cursor-pointer">
                        <svg class="w-4 h-4 text-sky-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                        <span>Hướng dẫn thanh toán</span>
                    </div>

                    <!-- Custom VNPay QR Code container with blue corners -->
                    <div class="relative bg-white border border-slate-100 rounded-xl p-6 shadow-sm flex flex-col items-center">
                        <!-- VNPay top logo -->
                        <div class="h-6 mb-3">
                            <img src="{{ asset('images/vnpay-logo.png') }}" alt="VNPayQR" class="h-full w-auto object-contain">
                        </div>
                        
                        <!-- QR Code Frame corner brackets style -->
                        <div class="relative p-2.5 border border-slate-200 rounded-md">
                            <!-- Blue Corner brackets -->
                            <div class="absolute -top-1.5 -left-1.5 w-4 h-4 border-t-4 border-l-4 border-sky-600 rounded-tl"></div>
                            <div class="absolute -top-1.5 -right-1.5 w-4 h-4 border-t-4 border-r-4 border-sky-600 rounded-tr"></div>
                            <div class="absolute -bottom-1.5 -left-1.5 w-4 h-4 border-b-4 border-l-4 border-sky-600 rounded-bl"></div>
                            <div class="absolute -bottom-1.5 -right-1.5 w-4 h-4 border-b-4 border-r-4 border-sky-600 rounded-br"></div>
                            
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=vnpay://pay?amount={{ $order->total_amount }}&orderId={{ $order->order_code }}" 
                                 alt="VNPay QR" class="w-48 h-48 block">
                        </div>

                        <!-- Scan to Pay text -->
                        <span class="text-xs font-semibold text-slate-500 italic mt-3.5 block">Scan to Pay</span>
                    </div>

                    <!-- Hủy thanh toán button -->
                    <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}" class="w-full max-w-[240px] mb-2">
                        @csrf
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold py-2.5 px-6 rounded-lg transition duration-150">
                            Hủy thanh toán
                        </button>
                    </form>

                    <!-- Giả lập thanh toán thành công button -->
                    <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}" class="w-full max-w-[240px] mb-2">
                        @csrf
                        <input type="hidden" name="status" value="success">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 px-6 rounded-lg transition duration-150 shadow-sm">
                            Thanh toán thành công
                        </button>
                    </form>

                    <!-- Giả lập thanh toán thất bại button -->
                    <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}" class="w-full max-w-[240px]">
                        @csrf
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold py-2.5 px-6 rounded-lg transition duration-150 shadow-sm">
                            Thanh toán thất bại
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bottom: Grid of bank lists -->
            <div class="border-t border-slate-200/60 pt-8 space-y-6">
                
                <!-- Section 1: Promo Banks -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Danh sách đơn vị hỗ trợ thanh toán có áp dụng khuyến mãi</h4>
                    <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-9 gap-3">
                        @php
                            $promoBanks = ['Vietcombank', 'BIDV', 'VietinBank', 'Agribank', 'VNPAY App', 'ABBank', 'BaoViet Bank', 'HDBank', 'SCB', 'BIDC', 'VietABank', 'Eximbank', 'Co-op Bank', 'Vietbank', 'Public Bank', 'Saigonbank', 'Viettel Money'];
                        @endphp
                        @foreach($promoBanks as $bank)
                            <div class="bg-white border border-slate-200/50 rounded p-2.5 flex items-center justify-center text-center text-[10px] font-bold text-slate-700 shadow-sm min-h-[44px]">
                                {{ $bank }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Section 2: Other Banks -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Danh sách đơn vị khác hỗ trợ thanh toán VNPAYQR</h4>
                    <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-8 gap-3">
                        @php
                            $otherBanks = ['Techcombank', 'MB Bank', 'VPBank', 'VIB', 'Sacombank', 'TPBank', 'MSB', 'ACB', 'SHB', 'OCB', 'NCB', 'BVBank', 'BAC A Bank', 'Kienlongbank', 'PVcomBank', 'Woori Bank', 'LPBank', 'PG Bank', 'SeaBank', 'Shinhan Bank', 'Cake', 'Timo', 'Liobank', 'ZaloPay', 'ShopeePay'];
                        @endphp
                        @foreach($otherBanks as $bank)
                            <div class="bg-white border border-slate-200/50 rounded p-2.5 flex items-center justify-center text-center text-[10px] font-bold text-slate-600 shadow-sm min-h-[44px]">
                                {{ $bank }}
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- VNPay Support footer info -->
            <div class="border-t border-slate-200/60 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <a href="mailto:hotrovnpay@vnpay.vn" class="hover:underline text-sky-600 font-medium">hotrovnpay@vnpay.vn</a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Secure GlobalSign</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> PCI DSS Compliant</span>
                </div>
            </div>
            
        </main>
    @endif

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-6 text-center text-xs text-slate-400">
        Hệ thống thanh toán giả lập Sandbox FEA © {{ date('Y') }}. Hỗ trợ kỹ thuật: support@fea.vn
    </footer>

</body>
</html>
