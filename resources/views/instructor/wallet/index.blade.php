
<x-instructor-layout title="Ví tiền & Rút tiền" page-title="Ví tiền & Quản lý rút tiền">

    <div x-data="{
        showBankModal: false,
        showWithdrawModal: false,
        showDetailModal: false,
        activeDetail: null,
        copied: false,
        bankCode: '{{ old('bank_code', $user->bank_code ?? '') }}',
        bankName: '{{ old('bank_name', $user->bank_name ?? '') }}',
        accountNumber: '{{ old('bank_account_number', $user->bank_account_number ?? '') }}',
        accountName: '{{ old('bank_account_name', $user->bank_account_name ?? '') }}',
        withdrawAmount: '{{ old('amount', '') }}',
        maxAmount: {{ floor($stats['available_balance']) }},
        
        openDetail(item) {
            this.activeDetail = item;
            this.showDetailModal = true;
        },
        copyCode(code) {
            if (navigator.clipboard && code) {
                navigator.clipboard.writeText(code);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
        },
        onBankChange(event) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            this.bankName = selectedOption.getAttribute('data-name') || selectedOption.value;
            this.bankCode = selectedOption.value;
        },
        setAmountPercent(percent) {
            this.withdrawAmount = Math.floor(this.maxAmount * (percent / 100));
        }
    }" class="space-y-6">

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="rounded-xl bg-rose-500/10 p-2 text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold">Lỗi thao tác:</h4>
                        <ul class="mt-1 list-inside list-disc text-sm space-y-1 text-rose-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Top Stat Cards Grid --}}
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Available Balance Card --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 p-6 text-white shadow-lg shadow-emerald-900/10 transition hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">Số dư khả dụng</span>
                    <div class="rounded-xl bg-white/20 p-2.5 backdrop-blur-md">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-3xl font-black tracking-tight">{{ number_format($stats['available_balance'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-medium text-emerald-100">Sẵn sàng để rút tiền về ngân hàng</p>
                </div>
                <div class="mt-5">
                    <button
                        type="button"
                        @click="showWithdrawModal = true"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-emerald-800 shadow-md transition hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-white/50 active:scale-[0.98] cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Rút tiền
                    </button>
                </div>
            </div>

            {{-- Pending Withdrawal Card --}}
            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Đang chờ xử lý</span>
                    <div class="rounded-xl bg-amber-50 p-2.5 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['pending_withdrawal'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-medium text-amber-600 font-semibold">Đang chờ Admin duyệt chuyển khoản</p>
                </div>
            </div>

            {{-- Total Withdrawn Card --}}
            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Đã rút thành công</span>
                    <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['total_withdrawn'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-medium text-slate-500">Đã nhận thực tế vào tài khoản ngân hàng</p>
                </div>
            </div>

            {{-- Total Net Earnings Card --}}
            <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Tổng thu nhập thực nhận</span>
                    <div class="rounded-xl bg-violet-50 p-2.5 text-violet-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['total_earnings'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-medium text-slate-500">Thu nhập từ bán các khóa học</p>
                </div>
            </div>
        </div>

        {{-- Bank Details Section --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-5">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Tài khoản Ngân hàng Nhận tiền</h3>
                        <p class="text-xs text-slate-500">Thông tin tài khoản để nhận tiền chuyển khoản tự động khi bạn rút tiền</p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="showBankModal = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-400 focus:outline-none cursor-pointer"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    {{ $user->bank_account_number ? 'Thay đổi tài khoản' : 'Thêm tài khoản ngân hàng' }}
                </button>
            </div>

            <div class="mt-5">
                @if ($user->bank_account_number && $user->bank_name)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 p-5">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-16 bg-white border border-slate-200 rounded-xl flex items-center justify-center p-1.5 shadow-xs shrink-0">
                                @php
                                    $currentBank = collect($banks)->firstWhere('code', $user->bank_code) ?? collect($banks)->firstWhere('shortName', $user->bank_name);
                                @endphp
                                @if ($currentBank && !empty($currentBank['logo']))
                                    <img src="{{ $currentBank['logo'] }}" alt="{{ $user->bank_name }}" class="h-full object-contain">
                                @else
                                    <span class="font-black text-xs text-slate-700 uppercase">{{ $user->bank_code ?? 'BANK' }}</span>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    {{ $user->bank_name }}
                                    <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700 uppercase">Napas247</span>
                                </h4>
                                <p class="font-mono text-base font-bold text-slate-800 mt-0.5 tracking-wider">{{ $user->bank_account_number }}</p>
                                <p class="text-xs text-slate-500 uppercase font-semibold mt-0.5">{{ $user->bank_account_name }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-slate-500 bg-white border border-slate-200 px-3.5 py-2 rounded-xl">
                            <span class="text-emerald-600 font-bold">✓ Đã xác thực thông tin</span>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center bg-slate-50/50">
                        <div class="mx-auto h-12 w-12 text-slate-400 rounded-full bg-slate-100 flex items-center justify-center">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h4 class="mt-3 text-sm font-bold text-slate-900">Chưa cập nhật tài khoản ngân hàng</h4>
                        <p class="mt-1 text-xs text-slate-500">Vui lòng cập nhật tên ngân hàng và số tài khoản để bắt đầu thực hiện rút tiền.</p>
                        <button
                            type="button"
                            @click="showBankModal = true"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition cursor-pointer"
                        >
                            Cập nhật ngay
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Withdrawal Requests Table --}}
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Lịch sử Rút tiền</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Theo dõi trạng thái duyệt và mã đối soát giao dịch từ hệ thống</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3.5">Mã Yêu cầu</th>
                            <th class="px-6 py-3.5 text-right">Số tiền rút</th>
                            <th class="px-6 py-3.5">Tài khoản Ngân hàng</th>
                            <th class="px-6 py-3.5 text-center">Trạng thái</th>
                            <th class="px-6 py-3.5">Mã Giao dịch / Ghi chú</th>
                            <th class="px-6 py-3.5">Thời gian tạo</th>
                            <th class="px-6 py-3.5 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($withdrawals as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                    #REQ{{ $item->id }}
                                </td>
                                <td class="px-6 py-4 text-right font-black text-sm text-emerald-600">
                                    {{ number_format($item->amount, 0, ',', '.') }}đ
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900">{{ $item->bank_name }}</p>
                                    <p class="font-mono text-slate-700">{{ $item->bank_account_number }}</p>
                                    <p class="text-[11px] text-slate-500 uppercase">{{ $item->bank_account_name }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($item->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-[11px] font-bold text-amber-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Chờ duyệt
                                        </span>
                                    @elseif ($item->status === 'approved')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold text-emerald-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Đã chuyển tiền
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1 text-[11px] font-bold text-rose-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Từ chối
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($item->transaction_ref)
                                        <span class="font-mono text-[11px] font-bold bg-slate-100 text-slate-800 px-2 py-0.5 rounded">
                                            Ref: {{ $item->transaction_ref }}
                                        </span>
                                    @endif
                                    @if ($item->admin_note)
                                        <p class="text-slate-600 text-xs mt-1 italic">{{ $item->admin_note }}</p>
                                    @elseif(!$item->transaction_ref)
                                        <span class="text-slate-400 italic">Đang chờ xử lý...</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $item->created_at->format('H:i - d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        type="button"
                                        @click="openDetail({{ json_encode([
                                            'id' => $item->id,
                                            'amount' => number_format($item->amount, 0, ',', '.') . 'đ',
                                            'bank_name' => $item->bank_name,
                                            'bank_account_number' => $item->bank_account_number,
                                            'bank_account_name' => $item->bank_account_name,
                                            'status' => $item->status,
                                            'transaction_ref' => $item->transaction_ref ?? '---',
                                            'admin_note' => $item->admin_note ?? '',
                                            'created_at' => $item->created_at->format('H:i:s - d/m/Y'),
                                            'processed_at' => $item->processed_at ? $item->processed_at->format('H:i:s - d/m/Y') : 'Chưa xử lý',
                                        ]) }})"
                                        class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Xem chi tiết
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Chưa có lịch sử rút tiền</p>
                                    <p class="mt-1 text-xs text-slate-400">Các yêu cầu rút tiền của bạn sẽ xuất hiện ở đây.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($withdrawals->hasPages())
                <div class="border-t border-slate-100 p-4">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Cập nhật Ngân hàng --}}
        <div
            x-show="showBankModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="bank-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="showBankModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="showBankModal = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                <div
                    x-show="showBankModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-900" id="bank-modal-title">
                            Cập nhật Tài khoản Ngân hàng
                        </h3>
                        <button type="button" @click="showBankModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('instructor.wallet.bank-details.update') }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="bank_name" :value="bankName">

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ngân hàng thụ hưởng (VietQR Napas247)</label>
                            <select
                                name="bank_code"
                                @change="onBankChange($event)"
                                required
                                class="w-full rounded-xl border-slate-300 py-2.5 px-3 text-sm font-semibold text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                <option value="">-- Chọn ngân hàng --</option>
                                @foreach ($banks as $b)
                                    <option
                                        value="{{ $b['code'] }}"
                                        data-name="{{ $b['shortName'] ?? $b['name'] }}"
                                        @selected(($user->bank_code ?? '') === $b['code'] || ($user->bank_name ?? '') === ($b['shortName'] ?? $b['name']))
                                    >
                                        {{ $b['shortName'] }} - {{ $b['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Số tài khoản ngân hàng</label>
                            <input
                                type="text"
                                name="bank_account_number"
                                x-model="accountNumber"
                                placeholder="Ví dụ: 0387043899"
                                required
                                class="w-full font-mono rounded-xl border-slate-300 py-2.5 px-4 text-sm font-bold text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tên chủ tài khoản (Viết hoa không dấu)</label>
                            <input
                                type="text"
                                name="bank_account_name"
                                x-model="accountName"
                                placeholder="Ví dụ: NGUYEN VAN A"
                                required
                                class="w-full rounded-xl border-slate-300 py-2.5 px-4 text-sm font-bold uppercase text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            />
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 mt-6">
                            <button
                                type="button"
                                @click="showBankModal = false"
                                class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                            >
                                Hủy
                            </button>
                            <button
                                type="submit"
                                class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition cursor-pointer"
                            >
                                Lưu tài khoản
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Yêu cầu Rút tiền --}}
        <div
            x-show="showWithdrawModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="withdraw-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="showWithdrawModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="showWithdrawModal = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                <div
                    x-show="showWithdrawModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-base font-bold text-slate-900" id="withdraw-modal-title">
                            Rút tiền về Ngân hàng
                        </h3>
                        <button type="button" @click="showWithdrawModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form action="{{ route('instructor.wallet.withdraw') }}" method="POST" class="mt-4 space-y-4">
                        @csrf

                        {{-- Receiver Bank Summary Box --}}
                        <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                            <p class="text-[11px] font-bold uppercase text-slate-500">Tài khoản ngân hàng nhận tiền:</p>
                            @if ($user->bank_account_number && $user->bank_name)
                                <div class="mt-1 flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-slate-900 text-sm">{{ $user->bank_name }}</p>
                                        <p class="font-mono text-slate-700 text-xs">{{ $user->bank_account_number }} - {{ $user->bank_account_name }}</p>
                                    </div>
                                    <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">VietQR</span>
                                </div>
                            @else
                                <p class="text-xs font-bold text-rose-600 mt-1">⚠️ Chưa có tài khoản ngân hàng. Hãy cập nhật ngân hàng trước!</p>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Số tiền muốn rút (VNĐ)</label>
                                <span class="text-xs text-slate-500 font-medium">Khả dụng: <strong class="text-emerald-600">{{ number_format($stats['available_balance'], 0, ',', '.') }}đ</strong></span>
                            </div>

                            <div class="relative">
                                <input
                                    type="number"
                                    name="amount"
                                    x-model="withdrawAmount"
                                    min="10000"
                                    :max="maxAmount"
                                    placeholder="Tối thiểu 10.000 VNĐ"
                                    required
                                    class="w-full font-bold text-lg rounded-xl border-slate-300 py-3 pl-4 pr-12 text-slate-900 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                />
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-sm">VNĐ</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Hạn mức rút tiền tối thiểu: 10,000 VNĐ.</p>
                        </div>

                        {{-- Quick Percentage Selector Buttons --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Chọn nhanh số tiền:</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="setAmountPercent(25)" class="rounded-xl border border-slate-200 bg-slate-50 py-2 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition">25%</button>
                                <button type="button" @click="setAmountPercent(50)" class="rounded-xl border border-slate-200 bg-slate-50 py-2 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition">50%</button>
                                <button type="button" @click="setAmountPercent(100)" class="rounded-xl border border-slate-200 bg-slate-50 py-2 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition">100% (Tất cả)</button>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 mt-6">
                            <button
                                type="button"
                                @click="showWithdrawModal = false"
                                class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                            >
                                Hủy
                            </button>
                            <button
                                type="submit"
                                :disabled="!accountNumber || maxAmount < 10000"
                                class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                            >
                                Rút tiền ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Chi tiết Giao dịch Rút tiền --}}
        <div
            x-show="showDetailModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="detail-modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="showDetailModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="showDetailModal = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                <div
                    x-show="showDetailModal"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                                💸
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900" id="detail-modal-title">
                                    Chi tiết Giao dịch Rút tiền
                                </h3>
                                <p class="text-xs text-slate-500 font-mono" x-text="activeDetail ? '#REQ' + activeDetail.id : ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <template x-if="activeDetail">
                        <div class="mt-5 space-y-4">
                            {{-- Amount Banner --}}
                            <div class="rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200/80 p-4 text-center">
                                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">Số tiền yêu cầu rút</span>
                                <span class="text-3xl font-black text-emerald-600 mt-1 block" x-text="activeDetail.amount"></span>
                            </div>

                            {{-- Details Grid --}}
                            <div class="rounded-2xl border border-slate-200 divide-y divide-slate-100 bg-slate-50/50 text-xs">
                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Trạng thái:</span>
                                    <template x-if="activeDetail.status === 'pending'">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 font-bold text-amber-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Đang chờ duyệt
                                        </span>
                                    </template>
                                    <template x-if="activeDetail.status === 'approved'">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 font-bold text-emerald-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Đã chuyển tiền
                                        </span>
                                    </template>
                                    <template x-if="activeDetail.status === 'rejected'">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-3 py-1 font-bold text-rose-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Từ chối
                                        </span>
                                    </template>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Ngân hàng thụ hưởng:</span>
                                    <span class="font-bold text-slate-900" x-text="activeDetail.bank_name"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Số tài khoản:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="activeDetail.bank_account_number"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Chủ tài khoản:</span>
                                    <span class="font-bold uppercase text-slate-900" x-text="activeDetail.bank_account_name"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Mã GD đối soát (Ref):</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded shadow-2xs" x-text="activeDetail.transaction_ref"></span>
                                        <button
                                            type="button"
                                            @click="copyCode(activeDetail?.transaction_ref)"
                                            class="text-[10px] font-bold text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded cursor-pointer"
                                        >
                                            <span x-text="copied ? 'Đã chép!' : 'Sao chép'"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Thời gian khởi tạo:</span>
                                    <span class="text-slate-700 font-medium" x-text="activeDetail.created_at"></span>
                                </div>

                                <div class="p-3.5 flex justify-between items-center">
                                    <span class="text-slate-500 font-semibold">Thời gian xử lý:</span>
                                    <span class="text-slate-700 font-medium" x-text="activeDetail.processed_at"></span>
                                </div>
                            </div>

                            <template x-if="activeDetail.admin_note">
                                <div class="rounded-2xl border border-slate-200 bg-amber-50/50 p-4 text-xs">
                                    <span class="font-bold text-slate-800 block mb-1">Ghi chú từ Admin:</span>
                                    <p class="text-slate-700 italic" x-text="activeDetail.admin_note"></p>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="mt-6 flex justify-end">
                        <button
                            type="button"
                            @click="showDetailModal = false"
                            class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition cursor-pointer"
                        >
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-instructor-layout>
