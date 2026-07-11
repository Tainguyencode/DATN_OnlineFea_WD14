<x-student-layout title="Thanh toán Chuyển khoản" page-title="Thanh toán Chuyển khoản" breadcrumb="Đơn hàng: {{ $order->order_code }}">

<div class="mx-auto max-w-4xl">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_320px]">
        
        <!-- Cột trái: Hướng dẫn chuyển khoản -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Thông tin chuyển khoản</h3>
                
                <div class="space-y-4">
                    <!-- Ngân hàng -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Ngân hàng</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">MB Bank (Ngân hàng Quân đội)</span>
                    </div>

                    <!-- Số tài khoản -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Số tài khoản</span>
                        <div class="flex items-center gap-2">
                            <span id="account-number" class="font-mono text-sm font-bold text-slate-900 dark:text-white">0987654321</span>
                            <button onclick="copyToClipboard('0987654321', 'stk-btn')" id="stk-btn" class="text-xs text-[#0056D2] hover:underline font-semibold">Sao chép</button>
                        </div>
                    </div>

                    <!-- Chủ tài khoản -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Chủ tài khoản</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">CONG TY CONG NGHE FEA</span>
                    </div>

                    <!-- Số tiền -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Số tiền chuyển khoản</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-[#0056D2] dark:text-blue-300">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                            <button onclick="copyToClipboard('{{ (int) $order->total_amount }}', 'amount-btn')" id="amount-btn" class="text-xs text-[#0056D2] hover:underline font-semibold">Sao chép</button>
                        </div>
                    </div>

                    <!-- Nội dung chuyển khoản -->
                    <div class="flex items-center justify-between pb-1">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Nội dung (Memo)</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-sm font-bold text-rose-600 dark:text-rose-400">{{ $order->order_code }}</span>
                            <button onclick="copyToClipboard('{{ $order->order_code }}', 'memo-btn')" id="memo-btn" class="text-xs text-[#0056D2] hover:underline font-semibold">Sao chép</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Các bước thực hiện -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Các bước thanh toán</h3>
                <ol class="list-decimal list-inside space-y-3 text-sm text-slate-600 dark:text-slate-300">
                    <li>Mở ứng dụng ngân hàng di động (Mobile Banking) trên điện thoại của bạn.</li>
                    <li>Chọn chức năng quét mã **QR** hoặc chuyển tiền nhanh 24/7 qua số tài khoản.</li>
                    <li>Quét mã QR bên cạnh hoặc nhập chính xác thông tin STK và **Nội dung chuyển khoản** ở trên.</li>
                    <li>Xác nhận số tiền chuyển khoản và thực hiện giao dịch thành công.</li>
                    <li>Hệ thống sẽ tự động cập nhật và mở khóa học cho bạn trong vòng 3-5 phút sau khi nhận được tiền.</li>
                </ol>
            </div>

            <!-- Bảng giả lập kết quả thanh toán cho Môi trường Phát triển -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-6 shadow-sm dark:border-amber-900/40 dark:bg-amber-950/20">
                <div class="flex items-center gap-3 mb-3 text-amber-800 dark:text-amber-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <h4 class="font-bold text-sm">Bảng Giả Lập Thanh Toán (Môi trường Development)</h4>
                </div>
                <p class="text-xs text-amber-700 dark:text-amber-400 mb-4 leading-relaxed">
                    Hệ thống đang chạy ở môi trường phát triển (Local/Sandbox). Bạn có thể bấm các nút dưới đây để giả lập kết quả giao dịch mà không cần thực hiện quét mã hoặc chuyển khoản thật.
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="status" value="success">
                        <button type="submit" class="w-full inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 text-sm font-bold text-white transition hover:bg-emerald-700 shadow-sm">
                            Thanh toán thành công (Kích hoạt khóa học)
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="w-full inline-flex h-10 items-center justify-center rounded-xl border border-rose-300 bg-white text-sm font-bold text-rose-700 transition hover:bg-rose-50 dark:border-rose-800 dark:bg-slate-900 dark:text-rose-400 dark:hover:bg-slate-800 shadow-sm">
                            Thanh toán thất bại / Hủy bỏ
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cột phải: QR Code VietQR -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 text-center">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3">Quét mã để thanh toán nhanh</h3>
                
                <div class="mx-auto max-w-[200px] aspect-square border border-slate-100 rounded-xl p-2 bg-white flex items-center justify-center">
                    <img src="https://img.vietqr.io/image/MB-0987654321-compact2.png?amount={{ (int) $order->total_amount }}&addInfo={{ $order->order_code }}&accountName=CONG%20TY%20CONG%20NGHE%20FEA" 
                         alt="VietQR MB Bank FEA Technology Company" 
                         class="w-full h-full object-contain">
                </div>
                
                <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                    Sử dụng bất kỳ ứng dụng ngân hàng di động nào có tính năng quét mã QR để quét và chuyển khoản trực tiếp.
                </p>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Mã đơn hàng:</span>
                    <strong class="text-slate-900 dark:text-white font-mono">{{ $order->order_code }}</strong>
                </div>
                <div class="mt-1 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Tổng tiền:</span>
                    <strong class="text-slate-900 dark:text-white">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
    function copyToClipboard(text, buttonId) {
        navigator.clipboard.writeText(text).then(function() {
            var btn = document.getElementById(buttonId);
            var originalText = btn.innerText;
            btn.innerText = "Đã chép!";
            btn.classList.add("text-emerald-600");
            setTimeout(function() {
                btn.innerText = originalText;
                btn.classList.remove("text-emerald-600");
            }, 2000);
        }).catch(function(err) {
            console.error('Không thể sao chép: ', err);
        });
    }
</script>

</x-student-layout>
