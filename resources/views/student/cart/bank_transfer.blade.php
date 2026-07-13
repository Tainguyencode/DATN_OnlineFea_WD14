<x-student-layout title="Thanh toán Chuyển khoản" page-title="Thanh toán Chuyển khoản" breadcrumb="Đơn hàng: {{ $order->order_code }}">

<div class="mx-auto max-w-4xl px-4 py-2">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_320px]">
        
        <!-- Cột trái: Hướng dẫn chuyển khoản & Giả lập -->
        <div class="space-y-6">
            
            <!-- Thẻ ngân hàng ảo + Thông tin thanh toán -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                    <span class="flex h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Thông tin tài khoản nhận tiền
                </h3>
                
                <!-- Mockup Thẻ Ngân hàng MB Bank phong cách Premium -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-900 via-indigo-950 to-slate-950 p-6 text-white shadow-md mb-6">
                    <div class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-blue-500/10 blur-2xl"></div>
                    <div class="absolute -left-10 -top-10 h-40 w-40 rounded-full bg-indigo-500/10 blur-2xl"></div>
                    
                    <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-5">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300">Ngân hàng thụ hưởng</span>
                            <h4 class="text-base font-black tracking-wide">MB BANK</h4>
                        </div>
                        <span class="text-xs font-bold bg-white/10 px-2.5 py-1 rounded-lg backdrop-blur-md">NAPAS 247</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 block mb-0.5">Số tài khoản</span>
                            <span class="text-xl font-mono font-extrabold tracking-widest text-slate-100">0987 654 321</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 block mb-0.5">Chủ tài khoản</span>
                                <span class="text-xs font-bold truncate block">CONG TY CONG NGHE FEA</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] uppercase tracking-wider text-slate-400 block mb-0.5">Số tiền chuyển</span>
                                <span class="text-sm font-extrabold text-emerald-400">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng chi tiết sao chép nhanh -->
                <div class="space-y-3.5">
                    <!-- Ngân hàng -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800/80">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Tên ngân hàng</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">MB Bank (Ngân hàng Quân đội)</span>
                    </div>

                    <!-- Số tài khoản -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800/80">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Số tài khoản</span>
                        <div class="flex items-center gap-2">
                            <span id="account-number" class="font-mono text-sm font-bold text-slate-900 dark:text-white mr-1">0987654321</span>
                            <button onclick="copyToClipboard('0987654321', 'stk-btn')" id="stk-btn" class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 bg-slate-50 hover:bg-slate-100 border border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 rounded-lg text-[#0056D2] dark:text-blue-300 transition duration-150 shadow-sm cursor-pointer outline-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span>Sao chép</span>
                            </button>
                        </div>
                    </div>

                    <!-- Chủ tài khoản -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800/80">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Chủ tài khoản</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white">CONG TY CONG NGHE FEA</span>
                    </div>

                    <!-- Số tiền -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800/80">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Số tiền chuyển khoản</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-[#0056D2] dark:text-blue-300 mr-1">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                            <button onclick="copyToClipboard('{{ (int) $order->total_amount }}', 'amount-btn')" id="amount-btn" class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 bg-slate-50 hover:bg-slate-100 border border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 rounded-lg text-[#0056D2] dark:text-blue-300 transition duration-150 shadow-sm cursor-pointer outline-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span>Sao chép</span>
                            </button>
                        </div>
                    </div>

                    <!-- Nội dung chuyển khoản -->
                    <div class="flex items-center justify-between pb-1">
                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">Nội dung (Memo)</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-sm font-bold text-rose-600 dark:text-rose-400 mr-1">{{ $order->order_code }}</span>
                            <button onclick="copyToClipboard('{{ $order->order_code }}', 'memo-btn')" id="memo-btn" class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 bg-slate-50 hover:bg-slate-100 border border-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 dark:border-slate-700 rounded-lg text-[#0056D2] dark:text-blue-300 transition duration-150 shadow-sm cursor-pointer outline-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span>Sao chép</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Các bước thanh toán -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Quy trình thực hiện chuyển khoản
                </h3>
                <ol class="list-decimal list-inside space-y-3.5 text-sm text-slate-600 dark:text-slate-300">
                    <li>Mở ứng dụng ngân hàng di động (Mobile Banking) trên thiết bị của bạn.</li>
                    <li>Sử dụng chức năng quét mã <strong class="text-slate-950 dark:text-white">QR Code</strong> để quét ảnh bên phải để tự động điền đầy đủ thông tin.</li>
                    <li>Nếu nhập tay, vui lòng đảm bảo nhập <strong class="text-rose-600 dark:text-rose-400 font-bold">chính xác tuyệt đối</strong> thông tin số tài khoản và nội dung chuyển khoản ở trên.</li>
                    <li>Xác nhận số tiền chuyển khoản và thực hiện giao dịch.</li>
                    <li>Hệ thống sẽ tự động đối soát và kích hoạt khóa học trong khoảng 3-5 phút sau khi giao dịch thành công.</li>
                </ol>
            </div>

            <!-- Bảng giả lập kết quả thanh toán cho Môi trường Phát triển -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50/40 p-6 shadow-sm dark:border-amber-900/40 dark:bg-amber-950/20">
                <div class="flex items-center gap-3 mb-3 text-amber-800 dark:text-amber-300">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h4 class="font-bold text-sm">Giả lập thanh toán (Môi trường Development)</h4>
                </div>
                <p class="text-xs text-amber-700 dark:text-amber-400 mb-4 leading-relaxed">
                    Bạn đang ở môi trường phát triển cục bộ. Bạn có thể bấm các nút dưới đây để giả lập phản hồi thanh toán từ phía ngân hàng:
                </p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="status" value="success">
                        <button type="submit" class="w-full inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 hover:bg-emerald-700 text-sm font-bold text-white transition shadow-sm cursor-pointer">
                            Thanh toán thành công (Active khóa học)
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="status" value="failed">
                        <button type="submit" class="w-full inline-flex h-10 items-center justify-center rounded-xl border border-rose-200 bg-white hover:bg-rose-50 text-sm font-bold text-rose-700 transition dark:border-rose-800 dark:bg-slate-900 dark:text-rose-400 dark:hover:bg-slate-800 shadow-sm cursor-pointer">
                            Thanh toán thất bại / Hủy bỏ
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cột phải: QR Code VietQR -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 text-center">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3.5">Quét mã QR qua ứng dụng</h3>
                
                <!-- VietQR dynamic container with focus border -->
                <div class="relative bg-white border border-slate-100 rounded-xl p-4 shadow-sm flex flex-col items-center">
                    <!-- Corner bracket decoration for scanner focus -->
                    <div class="absolute -top-1.5 -left-1.5 w-4.5 h-4.5 border-t-4 border-l-4 border-indigo-600 rounded-tl"></div>
                    <div class="absolute -top-1.5 -right-1.5 w-4.5 h-4.5 border-t-4 border-r-4 border-indigo-600 rounded-tr"></div>
                    <div class="absolute -bottom-1.5 -left-1.5 w-4.5 h-4.5 border-b-4 border-l-4 border-indigo-600 rounded-bl"></div>
                    <div class="absolute -bottom-1.5 -right-1.5 w-4.5 h-4.5 border-b-4 border-r-4 border-indigo-600 rounded-br"></div>
                    
                    <img src="https://img.vietqr.io/image/MB-0987654321-compact2.png?amount={{ (int) $order->total_amount }}&addInfo={{ $order->order_code }}&accountName=CONG%20TY%20CONG%20NGHE%20FEA" 
                         alt="VietQR MB Bank FEA Technology Company" 
                         class="w-full aspect-square object-contain block max-w-[180px]">
                    
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-2 block">VietQR</span>
                </div>
                
                <p class="text-[11px] text-slate-400 mt-4 leading-relaxed">
                    Hỗ trợ quét qua ứng dụng Internet Banking của mọi ngân hàng Việt Nam và các ví điện tử (MoMo, ZaloPay, ShopeePay...).
                </p>
                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800/80 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Mã đơn hàng:</span>
                    <strong class="text-slate-900 dark:text-white font-mono">{{ $order->order_code }}</strong>
                </div>
                <div class="mt-2 flex justify-between text-xs text-slate-500 dark:text-slate-400">
                    <span>Tổng tiền thanh toán:</span>
                    <strong class="text-sm font-black text-[#0056D2] dark:text-blue-300">{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
    function copyToClipboard(text, buttonId) {
        navigator.clipboard.writeText(text).then(function() {
            var btn = document.getElementById(buttonId);
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg><span class="text-emerald-600 dark:text-emerald-400 font-bold">Đã chép</span>';
            btn.classList.add("border-emerald-200", "bg-emerald-50/20", "dark:border-emerald-950/40", "dark:bg-emerald-950/20");
            setTimeout(function() {
                btn.innerHTML = originalHtml;
                btn.classList.remove("border-emerald-200", "bg-emerald-50/20", "dark:border-emerald-950/40", "dark:bg-emerald-950/20");
            }, 2000);
        }).catch(function(err) {
            console.error('Không thể sao chép: ', err);
        });
    }
</script>

</x-student-layout>
