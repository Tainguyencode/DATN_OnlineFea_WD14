<section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <h2 class="font-extrabold">Yêu cầu hoàn tiền</h2>
    <p class="mt-2 text-sm text-slate-500">Áp dụng cho đơn có thanh toán, trong vòng 7 ngày và chưa học đến 50% của bất kỳ khóa học nào trong đơn.</p>

    @if($refund && in_array($refund->status, ['pending', 'processing', 'approved'], true))
        <p class="mt-4 rounded-xl bg-amber-50 p-4 text-sm font-semibold text-amber-800" role="status">
            {{ match($refund->status) { 'pending' => 'Yêu cầu hoàn tiền đang chờ duyệt.', 'processing' => 'Yêu cầu hoàn tiền đang được xử lý.', 'approved' => 'Yêu cầu hoàn tiền đã được duyệt.' } }}
        </p>
    @else
        @if($refund?->status === 'rejected')
            <p class="mt-4 text-sm text-rose-600">Yêu cầu trước đã bị từ chối. {{ $refund->admin_note }}</p>
        @endif

        @if(! $refundEligibility['has_value'])
            <p class="mt-4 text-sm text-slate-500">Đơn hàng không phát sinh thanh toán nên không có số tiền để hoàn.</p>
        @elseif(! $refundEligibility['within_window'])
            <p class="mt-4 text-sm text-slate-500">Đơn hàng đã hết thời hạn yêu cầu hoàn tiền 7 ngày.</p>
        @elseif(! $refundEligibility['progress_ok'])
            <p class="mt-4 text-sm text-slate-500">Không thể yêu cầu hoàn tiền vì có khóa học trong đơn đã học từ 50% tiến độ.</p>
        @else
            <form method="POST" action="{{ route('student.orders.refund', $order) }}" class="mt-4 space-y-4" x-data="{ submitting: false }" x-on:submit="submitting = true">
                @csrf
                <p class="text-sm">Số tiền yêu cầu hoàn: <strong>{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</strong></p>
                <div>
                    <label for="refund-reason" class="block text-sm font-semibold">Lý do hoàn tiền</label>
                    <textarea id="refund-reason" name="reason" required minlength="10" maxlength="1000" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent p-3" placeholder="Nhập lý do từ 10 đến 1000 ký tự">{{ old('reason') }}</textarea>
                    @error('reason')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label for="refund-bank" class="block text-sm font-semibold">Ngân hàng nhận tiền</label>
                        <select id="refund-bank" name="bank_code" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white p-3 dark:bg-slate-900">
                            <option value="">Chọn ngân hàng</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank['code'] }}" @selected(old('bank_code') === $bank['code'])>{{ $bank['shortName'] }}</option>
                            @endforeach
                        </select>
                        @error('bank_code')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="refund-account-number" class="block text-sm font-semibold">Số tài khoản</label>
                        <input id="refund-account-number" name="bank_account_number" type="text" inputmode="numeric" required pattern="[0-9]{6,20}" maxlength="20" value="{{ old('bank_account_number') }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent p-3">
                        @error('bank_account_number')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="refund-account-name" class="block text-sm font-semibold">Tên chủ tài khoản</label>
                        <input id="refund-account-name" name="bank_account_name" type="text" required minlength="3" maxlength="100" value="{{ old('bank_account_name') }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-transparent p-3">
                        @error('bank_account_name')<p class="text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" :disabled="submitting" class="min-h-11 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-60" x-text="submitting ? 'Đang gửi…' : 'Gửi yêu cầu hoàn tiền'">Gửi yêu cầu hoàn tiền</button>
            </form>
        @endif
    @endif
</section>
