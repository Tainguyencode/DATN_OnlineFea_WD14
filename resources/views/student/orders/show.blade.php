@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #'.$order->order_code.' - Website học online FEA')

@section('content')
@include('partials.financial-clean-icons')
<div class="bg-slate-50 py-8 dark:bg-slate-950 min-h-[calc(100vh-16rem)]">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- NÚT QUAY LẠI VÀ THAO TÁC IN -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('student.orders') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition">
                Quay lại danh sách đơn hàng
            </a>

            <div class="flex items-center gap-2 print:hidden">
                <button onclick="window.print()" class="inline-flex h-9 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 shadow-sm">
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
                            Đã thanh toán
                        </span>
                    @elseif($order->status === 'refunded')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-50 px-4 py-1.5 text-xs font-bold text-purple-700 border border-purple-200">
                            Đã hoàn tiền
                        </span>
                    @elseif($order->status === 'pending')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-4 py-1.5 text-xs font-bold text-amber-700 border border-amber-200">
                            Chờ thanh toán
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-4 py-1.5 text-xs font-bold text-rose-700 border border-rose-200">
                            {{ ucfirst($order->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- THÔNG BÁO FLASH SUCCESS / ERROR -->
            @if(session('success'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-bold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-bold text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                    {{ session('error') }}
                </div>
            @endif

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
                                PayOS VietQR
                            </strong>
                        </div>

                        @if($order->payment)
                            <div class="border-t border-slate-100 dark:border-slate-800 pt-3">
                                <span class="block font-medium text-slate-400 uppercase tracking-wider text-[10px]">Mã giao dịch</span>
                                <strong class="mt-1 block font-mono font-bold text-slate-800 dark:text-slate-200">
                                    {{ $order->payment->transaction_id ?? $order->transaction_id ?? 'N/A' }}
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
                            <p class="font-bold">Đơn hàng chưa hoàn tất thanh toán</p>
                            <p class="leading-relaxed">Vui lòng bấm nút dưới đây để hoàn tất thanh toán và kích hoạt quyền học tập ngay.</p>
                            <a href="{{ route('student.checkout.pay', $order->order_code) }}" class="inline-flex h-10 w-full items-center justify-center rounded-xl bg-amber-500 font-bold text-white transition hover:bg-amber-600 shadow-sm">
                                Thanh toán ngay
                            </a>
                        </div>
                    @endif

                    <!-- KHU VỰC HOÀN TIỀN (REFUND) -->
                    @php
                        $refund = $order->refund;
                    @endphp

                    @if($refund)
                        @if($refund->status === 'pending')
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900/50 dark:bg-amber-950/30 text-xs space-y-2">
                                <div class="flex items-center gap-2 text-amber-700 dark:text-amber-300 font-bold">
                                    Yêu cầu hoàn tiền đang chờ đối soát
                                </div>
                                <p class="text-slate-600 dark:text-slate-300">Yêu cầu hoàn tiền số tiền <strong class="text-slate-900 dark:text-white">{{ number_format($refund->amount, 0, ',', '.') }}đ</strong> đã gửi vào {{ $refund->created_at->format('d/m/Y H:i') }}. Ban quản trị đang xử lý đối soát.</p>
                                <div class="border-t border-amber-200/60 dark:border-amber-900/40 pt-2 text-[11px] text-slate-500">
                                    NH: <strong>{{ $refund->bank_code }}</strong> - STK: <strong>{{ $refund->bank_account_number }}</strong> ({{ $refund->bank_account_name }})
                                </div>
                            </div>
                        @elseif($refund->status === 'processing')
                            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-900/50 dark:bg-blue-950/30 text-xs space-y-2">
                                <div class="flex items-center gap-2 font-bold text-blue-700 dark:text-blue-300">
                                    <span class="h-2 w-2 animate-pulse rounded-full bg-blue-500"></span>
                                    Đang thực hiện chuyển tiền
                                </div>
                                <p class="leading-relaxed text-slate-600 dark:text-slate-300">Yêu cầu đã được duyệt sơ bộ và đang được ngân hàng xử lý. Vui lòng không gửi yêu cầu khác. Trạng thái sẽ được cập nhật sau khi có kết quả đối soát.</p>
                            </div>
                        @elseif($refund->status === 'approved')
                            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-5 dark:border-purple-900/50 dark:bg-purple-950/30 text-xs space-y-2">
                                <div class="flex items-center gap-2 text-purple-700 dark:text-purple-300 font-bold">
                                    Đã hoàn tiền thành công
                                </div>
                                <p class="text-slate-600 dark:text-slate-300">Số tiền <strong class="text-purple-700 dark:text-purple-300">{{ number_format($refund->amount, 0, ',', '.') }}đ</strong> đã được hoàn về tài khoản {{ $refund->bank_code }} ({{ $refund->bank_account_number }}) vào ngày {{ $refund->processed_at ? $refund->processed_at->format('d/m/Y H:i') : $refund->updated_at->format('d/m/Y H:i') }}.</p>
                                @if($refund->transaction_reference)
                                    <p class="text-[11px] font-mono text-slate-500">Mã tham chiếu: {{ $refund->transaction_reference }}</p>
                                @endif
                            </div>
                        @elseif($refund->status === 'rejected')
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 dark:border-rose-900/50 dark:bg-rose-950/30 text-xs space-y-2">
                                <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 font-bold">
                                    Yêu cầu hoàn tiền bị từ chối
                                </div>
                                <p class="text-slate-600 dark:text-slate-300">Lý do từ chối: <em class="font-semibold text-rose-800 dark:text-rose-300">"{{ $refund->admin_note }}"</em></p>
                            </div>
                        @endif
                    @endif

                    <!-- NÚT GỬI YÊU CẦU HOÀN TIỀN (Nếu đơn Paid và chưa có refund pending/approved) -->
                    @if($order->status === 'paid' && (!$refund || $refund->status === 'rejected'))
                        <div x-data="{
                            openModal: {{ $errors->any() ? 'true' : 'false' }},
                            submitting: false,
                            confirmed: false,
                            bankCode: {{ Illuminate\Support\Js::from(old('bank_code', '')) }},
                            accountNumber: {{ Illuminate\Support\Js::from(old('bank_account_number', '')) }},
                            accountName: {{ Illuminate\Support\Js::from(old('bank_account_name', '')) }},
                            normalizeAccountNumber() {
                                this.accountNumber = (this.accountNumber || '').replace(/\D/g, '').slice(0, 20);
                            },
                            normalizeAccountName() {
                                this.accountName = (this.accountName || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D').replace(/[^a-zA-Z\s]/g, '').replace(/\s+/g, ' ').toUpperCase().slice(0, 100);
                            },
                            get bankFormValid() {
                                return Boolean(this.bankCode) && /^[0-9]{6,20}$/.test(this.accountNumber || '') && /^[A-Z ]{3,100}$/.test((this.accountName || '').trim());
                            },
                            get bankPreviewUrl() {
                                if (!this.bankFormValid) return '';
                                const content = {{ Illuminate\Support\Js::from('HOAN TIEN DH '.$order->order_code) }};
                                return `https://img.vietqr.io/image/${encodeURIComponent(this.bankCode)}-${encodeURIComponent(this.accountNumber)}-compact2.png?amount={{ (int) $order->total_amount }}&addInfo=${encodeURIComponent(content)}&accountName=${encodeURIComponent(this.accountName.trim())}`;
                            }
                        }">
                            <div class="mb-3 rounded-2xl border border-slate-200 bg-white p-4 text-xs dark:border-slate-800 dark:bg-slate-900">
                                <p class="font-extrabold text-slate-900 dark:text-white">Điều kiện hoàn tiền</p>
                                <div class="mt-3 space-y-2 text-slate-600 dark:text-slate-300">
                                    <p class="flex justify-between gap-3"><span>Gửi trong 7 ngày từ khi thanh toán</span><strong class="{{ $refundEligibility['within_window'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ $refundEligibility['within_window'] ? 'Đạt' : 'Đã quá hạn' }}</strong></p>
                                    <p class="flex justify-between gap-3"><span>Tiến độ mỗi khóa dưới 50%</span><strong class="{{ $refundEligibility['progress_ok'] ? 'text-emerald-600' : 'text-rose-600' }}">{{ number_format($refundEligibility['max_progress'], 0) }}%</strong></p>
                                    <p class="flex justify-between gap-3"><span>Hạn gửi yêu cầu</span><strong>{{ $refundEligibility['deadline']?->format('H:i d/m/Y') ?? 'Không xác định' }}</strong></p>
                                </div>
                            </div>
                            <button @click="openModal = true" type="button" @disabled(! $refundEligibility['within_window'] || ! $refundEligibility['progress_ok'] || ! $refundEligibility['has_value']) class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 transition hover:bg-rose-50 hover:text-rose-600 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 shadow-sm">
                                {{ $refundEligibility['within_window'] && $refundEligibility['progress_ok'] && $refundEligibility['has_value'] ? 'Yêu cầu hoàn tiền đơn hàng' : 'Đơn hàng không đủ điều kiện hoàn tiền' }}
                            </button>

                            <!-- MODAL GỬI YÊU CẦU HOÀN TIỀN -->
                            <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
                                <div @click.away="openModal = false" class="max-h-[92vh] w-full max-w-4xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900 space-y-4">
                                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                                        <h3 class="text-base font-extrabold text-slate-950 dark:text-white">Yêu cầu hoàn tiền đơn hàng #{{ $order->order_code }}</h3>
                                        <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg font-bold">&times;</button>
                                    </div>

                                    <form action="{{ route('student.orders.refund', $order) }}" method="POST" class="space-y-4 text-xs" @submit="submitting = true">
                                        @csrf

                                        <div class="rounded-xl bg-indigo-50 p-3 dark:bg-indigo-950/40 text-indigo-900 dark:text-indigo-200">
                                            <p class="font-bold">Số tiền hoàn dự kiến: <span class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span></p>
                                            <p class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">Tiền sẽ được hoàn trả trực tiếp về tài khoản ngân hàng của bạn sau khi Admin kiểm tra và đối soát.</p>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="font-bold text-slate-700 dark:text-slate-300">Ngân hàng nhận tiền *</label>
                                            <select name="bank_code" x-model="bankCode" required class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-800 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">
                                                <option value="">Chọn ngân hàng</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank['code'] }}" @selected(old('bank_code') === $bank['code'])>{{ $bank['shortName'] ?? $bank['code'] }} — {{ $bank['name'] ?? $bank['code'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('bank_code') <p class="text-rose-600">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="space-y-1">
                                                <label class="font-bold text-slate-700 dark:text-slate-300">Số tài khoản *</label>
                                                <input type="text" name="bank_account_number" x-model="accountNumber" @input="normalizeAccountNumber" placeholder="Ví dụ: 0987654321" inputmode="numeric" autocomplete="off" minlength="6" maxlength="20" pattern="[0-9]{6,20}" title="Số tài khoản phải gồm từ 6 đến 20 chữ số" required class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-800 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">
                                                @error('bank_account_number') <p class="text-rose-600">{{ $message }}</p> @enderror
                                            </div>
                                            <div class="space-y-1">
                                                <label class="font-bold text-slate-700 dark:text-slate-300">Tên chủ tài khoản *</label>
                                                <input type="text" name="bank_account_name" x-model="accountName" @input="normalizeAccountName" placeholder="NGUYEN VAN A" required class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold uppercase text-slate-800 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">
                                                @error('bank_account_name') <p class="text-rose-600">{{ $message }}</p> @enderror
                                            </div>
                                        </div>

                                        <div class="flex min-h-72 items-center justify-center rounded-2xl border p-4" :class="bankFormValid ? 'border-emerald-200 bg-emerald-50/60 dark:border-emerald-900 dark:bg-emerald-950/20' : 'border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-950'">
                                            <div class="flex flex-col items-center text-center">
                                                <div class="flex aspect-square w-64 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
                                                    <template x-if="bankFormValid"><img :src="bankPreviewUrl" alt="Xem trước VietQR tài khoản nhận hoàn tiền" class="h-full w-full object-contain"></template>
                                                    <template x-if="!bankFormValid"><span class="px-6 text-sm font-semibold text-slate-400">QR sẽ xuất hiện khi thông tin ngân hàng hợp lệ</span></template>
                                                </div>
                                                <p x-show="bankFormValid" class="mt-2 font-bold text-emerald-700">VietQR nhận hoàn tiền dự kiến {{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                                                <p class="mt-1 text-[11px] text-slate-500">Dùng QR để kiểm tra lại ngân hàng, số tài khoản và tên người nhận.</p>
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="font-bold text-slate-700 dark:text-slate-300">Lý do yêu cầu hoàn tiền *</label>
                                            <textarea name="reason" rows="3" minlength="10" maxlength="1000" placeholder="Mô tả vấn đề cụ thể để chúng tôi xử lý nhanh hơn..." required class="w-full rounded-xl border border-slate-200 bg-white p-3 text-xs text-slate-800 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">{{ old('reason') }}</textarea>
                                            @error('reason') <p class="text-rose-600">{{ $message }}</p> @enderror
                                        </div>

                                        <label class="flex items-start gap-2 rounded-xl border border-slate-200 p-3 leading-relaxed dark:border-slate-700">
                                            <input type="checkbox" x-model="confirmed" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                            <span>Tôi xác nhận thông tin tài khoản là chính xác và hiểu rằng quyền truy cập các khóa học trong đơn sẽ bị thu hồi khi hoàn tiền thành công.</span>
                                        </label>

                                        <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-800 pt-3">
                                            <button type="button" @click="openModal = false" class="h-9 rounded-xl border border-slate-200 px-4 font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-slate-800">Hủy</button>
                                            <button type="submit" :disabled="!confirmed || submitting" class="h-9 rounded-xl bg-rose-600 px-5 font-bold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50 shadow-sm"><span x-text="submitting ? 'Đang gửi...' : 'Gửi yêu cầu hoàn tiền'"></span></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
