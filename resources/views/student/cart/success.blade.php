<x-student-layout title="Thanh toán Thành công - FEA Online" page-title="Thanh toán thành công" breadcrumb="Thanh toán">

    <div class="mx-auto w-full max-w-xl py-4 text-center space-y-6">
        
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
            <h2 class="text-2xl font-black text-slate-900">Thanh toán thành công!</h2>
            <p class="text-xs text-slate-500">Khóa học đã được đăng ký và kích hoạt thành công.</p>
        </div>

        <!-- Receipt Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm text-left space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3">Chi tiết giao dịch</h3>
            
            <div class="grid grid-cols-2 gap-y-3 text-xs leading-relaxed">
                <span class="text-slate-500">Mã đơn hàng:</span>
                <strong class="text-slate-900 font-mono text-right">{{ $order->order_code }}</strong>

                <span class="text-slate-500">Phương thức thanh toán:</span>
                <strong class="text-slate-900 text-right">
                    PayOS VietQR
                </strong>

                <span class="text-slate-500">Mã giao dịch:</span>
                <strong class="text-slate-900 font-mono text-right">{{ $order->transaction_id ?? 'N/A' }}</strong>

                <span class="text-slate-500">Ngày giờ thanh toán:</span>
                <strong class="text-slate-900 text-right">{{ $order->updated_at->format('H:i d/m/Y') }}</strong>

                <span class="text-slate-500 font-semibold">Số tiền đã thanh toán:</span>
                <strong class="text-sm font-black text-[#0056D2] text-right">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
            </div>

            <div class="border-t border-slate-100 pt-4 mt-2">
                <h4 class="text-xs font-bold text-slate-800 mb-3">Danh sách khóa học đã mua:</h4>
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

            <!-- Action Buttons -->
            <div class="pt-4 flex items-center gap-3 border-t border-slate-100">
                <a href="{{ route('student.dashboard') }}" class="flex-1 text-center bg-[#0056D2] hover:bg-[#0046B8] text-white text-xs font-bold py-3 rounded-xl transition shadow-sm">
                    Vào học ngay →
                </a>
                <a href="{{ route('home') }}" class="text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-3 rounded-xl transition">
                    Trang chủ
                </a>
            </div>
        </div>

    </div>

</x-student-layout>
