<x-admin-layout title="Quản lý Rút tiền Giảng viên" pageTitle="Duyệt Yêu cầu Rút tiền Giảng viên (VietQR Napas247)">

    <div x-data="{
        qrModalOpen: false,
        rejectModalOpen: false,
        withdrawalId: null,
        userId: null,
        instructorName: '',
        amount: 0,
        bankName: '',
        bankCode: '',
        bankAcc: '',
        bankOwner: '',
        vietQrUrl: '',
        transferContent: '',
        transactionRef: '',
        adminNote: '',
        copiedField: null,

        openQrModal(id, userId, name, amt, bank, code, acc, owner, qr) {
            this.withdrawalId = id;
            this.userId = userId;
            this.instructorName = name;
            this.amount = amt;
            this.bankName = bank;
            this.bankCode = code || 'MB';
            this.bankAcc = acc;
            this.bankOwner = owner;
            this.vietQrUrl = qr;
            this.transferContent = 'RUT TIEN MAGV ' + userId + ' REQ' + id;
            this.transactionRef = 'FT' + new Date().toISOString().replace(/\D/g,'').slice(0,14);
            this.qrModalOpen = true;
        },

        openRejectModal(id, name, amt) {
            this.withdrawalId = id;
            this.instructorName = name;
            this.amount = amt;
            this.adminNote = '';
            this.rejectModalOpen = true;
        },

        copyText(text, field) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            } else {
                const el = document.createElement('textarea');
                el.value = text;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            }
            this.copiedField = field;
            setTimeout(() => { this.copiedField = null; }, 2000);
        }
    }" class="space-y-6">

        {{-- Session Flash Notifications --}}
        @if (session('success'))
            <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 text-emerald-800 shadow-sm backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-emerald-500/10 p-2 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

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

        {{-- Stat Summary Cards --}}
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Chờ duyệt chi trả</span>
                    <div class="rounded-xl bg-amber-50 p-2.5 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-amber-600">{{ number_format($stats['total_pending'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $stats['count_pending'] }} yêu cầu rút tiền đang chờ</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Đã chi trả thành công</span>
                    <div class="rounded-xl bg-emerald-50 p-2.5 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['total_approved'], 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $stats['count_approved'] }} yêu cầu đã được duyệt</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Số lượng Từ chối</span>
                    <div class="rounded-xl bg-rose-50 p-2.5 text-rose-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-rose-600">{{ number_format($stats['count_rejected']) }}</span>
                    <p class="mt-1 text-xs font-semibold text-slate-500">Yêu cầu từ chối chuyển tiền</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-emerald-600 bg-emerald-600 p-5 text-white shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-100">Cổng Chuyển tiền</span>
                    <span class="rounded-lg bg-white/20 px-2 py-0.5 text-[10px] font-black uppercase text-white">Napas247</span>
                </div>
                <div class="mt-4">
                    <span class="text-sm font-black block">VietQR Fast Transfer</span>
                    <p class="mt-1 text-xs text-emerald-100 font-medium">Quét mã QR bằng App Ngân hàng để chuyển tiền cho Giảng viên rồi xác nhận thành công!</p>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 p-5 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Danh sách Yêu cầu Rút tiền Giảng viên</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Mở mã VietQR -> Quét chuyển tiền bằng App Ngân hàng -> Xác nhận để hệ thống tự động gửi thông báo cho Giảng viên</p>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="mt-4 sm:mt-0 flex flex-wrap items-center gap-3">
                    <div class="relative min-w-64">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Tìm giảng viên, STK, mã ref..."
                            class="w-full rounded-xl border-slate-300 py-2 pl-9 pr-4 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-emerald-500"
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ duyệt (Pending)</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Đã chuyển (Approved)</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Từ chối (Rejected)</option>
                    </select>

                    <button type="submit" class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        Lọc
                    </button>
                    @if ($search || $status)
                        <a href="{{ route('admin.withdrawals.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">Xóa lọc</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Mã & Thời gian</th>
                            <th class="px-5 py-3.5">Giảng viên</th>
                            <th class="px-5 py-3.5 text-right">Số tiền rút</th>
                            <th class="px-5 py-3.5">Tài khoản Ngân hàng Nhận</th>
                            <th class="px-4 py-3.5 text-center">Trạng thái</th>
                            <th class="px-5 py-3.5 text-center">Thao tác Chuyển tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($withdrawals as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4">
                                    <span class="font-mono font-bold text-slate-900 text-sm">#REQ{{ $item->id }}</span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->created_at->format('H:i - d/m/Y') }}</p>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 shrink-0 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($item->user?->name ?? 'GV', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $item->user_id) }}" class="font-bold text-slate-900 hover:text-emerald-600 transition">
                                                {{ $item->user?->name ?? 'N/A' }}
                                            </a>
                                            <p class="text-slate-500 text-[11px]">{{ $item->user?->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-right font-black text-sm text-emerald-600">
                                    {{ number_format($item->amount, 0, ',', '.') }}đ
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900 flex items-center gap-1.5">
                                        {{ $item->bank_name }}
                                        <span class="rounded bg-emerald-100 text-emerald-800 font-mono font-bold text-[10px] px-1.5 py-0.2 uppercase">{{ $item->bank_code ?? 'Napas' }}</span>
                                    </p>
                                    <p class="font-mono text-slate-800 font-bold mt-0.5 text-xs">{{ $item->bank_account_number }}</p>
                                    <p class="text-[11px] text-slate-500 uppercase font-semibold">{{ $item->bank_account_name }}</p>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @if ($item->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-[11px] font-bold text-amber-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Chờ chuyển tiền
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

                                <td class="px-5 py-4 text-center">
                                    @if ($item->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                @click="openQrModal({{ $item->id }}, {{ $item->user_id }}, '{{ addslashes($item->user?->name) }}', {{ $item->amount }}, '{{ addslashes($item->bank_name) }}', '{{ $item->bank_code ?? 'MB' }}', '{{ $item->bank_account_number }}', '{{ addslashes($item->bank_account_name) }}', '{{ $item->viet_qr_url }}')"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-md hover:bg-emerald-700 transition active:scale-95 cursor-pointer"
                                                title="Quét mã VietQR bằng App ngân hàng"
                                            >
                                                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                Quét VietQR
                                            </button>

                                            <button
                                                type="button"
                                                @click="openRejectModal({{ $item->id }}, '{{ addslashes($item->user?->name) }}', {{ $item->amount }})"
                                                class="inline-flex items-center rounded-xl border border-rose-300 bg-white px-2.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50 transition cursor-pointer"
                                            >
                                                Từ chối
                                            </button>
                                        </div>
                                    @else
                                        <div class="text-left text-[11px] space-y-0.5">
                                            @if ($item->transaction_ref)
                                                <p class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded w-fit">Ref: {{ $item->transaction_ref }}</p>
                                            @endif
                                            @if ($item->admin_note)
                                                <p class="text-slate-500 italic max-w-xs truncate">{{ $item->admin_note }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Không tìm thấy yêu cầu rút tiền nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($withdrawals->hasPages())
                <div class="border-t border-slate-200 p-4">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Quét Mã VietQR Napas247 - Bố Cục Căn Giữa Hoàn Hảo (Khôi Phục Khung 32cm x 20cm) --}}
        <div
            x-show="qrModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-3 text-center sm:p-0">
                <div
                    x-show="qrModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="qrModalOpen = false"
                    class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
                ></div>

                <div
                    x-show="qrModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    style="width: 32cm; max-width: 95vw; height: 20cm; min-height: 20cm;"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-6 p-7 sm:p-9 flex flex-col justify-between border border-slate-200"
                >
                    {{-- Dòng Header Gợi Ý Bóng Đèn Căn Giữa --}}
                    <div class="flex items-center justify-center gap-2 text-center text-sm text-slate-700 font-medium border-b border-slate-100 pb-4">
                        <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span>Mở App Ngân hàng bất kỳ để <strong>quét mã VietQR</strong> hoặc <strong>chuyển khoản</strong> chính xác số tiền, nội dung bên dưới</span>
                    </div>

                    <form :action="'/admin/withdrawals/' + withdrawalId + '/approve'" method="POST" class="flex-1 flex flex-col justify-center items-center py-2">
                        @csrf

                        {{-- Bố Cục 2 Cột Căn Giữa Hoàn Hảo (Flex Row Center) Không Bị Trống Lề --}}
                        <div class="flex flex-row items-center justify-center gap-10 sm:gap-14 w-full max-w-4xl mx-auto flex-1">
                            
                            {{-- CỘT TRÁI: Mã QR Code To & Nút Hủy bên dưới --}}
                            <div class="w-72 sm:w-80 shrink-0 flex flex-col items-center justify-center">
                                <div class="w-full rounded-3xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                                    <img :src="vietQrUrl" alt="Mã VietQR Chuyển Tiền" class="w-full h-auto rounded-2xl block mx-auto">
                                </div>

                                {{-- Nút Hủy --}}
                                <div class="mt-4">
                                    <button
                                        type="button"
                                        @click="qrModalOpen = false"
                                        class="w-40 rounded-xl border border-slate-300 bg-white py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer shadow-2xs text-center"
                                    >
                                        Hủy
                                    </button>
                                </div>
                            </div>

                            {{-- Đường Vạch Phân Cách Dọc Nhẹ Nhàng --}}
                            <div class="h-80 w-px bg-slate-100 hidden sm:block shrink-0"></div>

                            {{-- CỘT PHẢI (W-[420px]): Thông Tin Ngân Hàng Thiết Kế Tinh Tế Cân Đối --}}
                            <div class="w-full max-w-[420px] shrink-0 space-y-3.5 text-sm">
                                
                                {{-- Ngân hàng --}}
                                <div class="bg-slate-50/70 p-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-blue-900 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0 shadow-2xs">
                                        <span x-text="(bankCode || 'MB').slice(0,3)"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 font-medium block text-xs">Ngân hàng thụ hưởng</span>
                                        <strong class="text-slate-900 font-extrabold text-sm sm:text-base" x-text="bankName"></strong>
                                    </div>
                                </div>

                                {{-- Chủ tài khoản --}}
                                <div class="bg-slate-50/70 p-3 rounded-2xl border border-slate-100">
                                    <span class="text-slate-400 font-medium block text-xs">Chủ tài khoản:</span>
                                    <strong class="text-slate-900 font-black uppercase text-base tracking-wide" x-text="bankOwner"></strong>
                                </div>

                                {{-- Số tài khoản & Button Sao chép --}}
                                <div class="bg-slate-50/70 p-3 rounded-2xl border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 font-medium block text-xs">Số tài khoản:</span>
                                        <strong class="text-slate-900 font-mono font-black text-base sm:text-lg tracking-wider" x-text="bankAcc"></strong>
                                    </div>
                                    <button
                                        type="button"
                                        @click="copyText(bankAcc, 'acc')"
                                        class="rounded-xl bg-emerald-100/70 px-4 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-200/80 transition cursor-pointer shadow-2xs"
                                    >
                                        <template x-if="copiedField === 'acc'">
                                            <span>✓ Đã chép</span>
                                        </template>
                                        <template x-if="copiedField !== 'acc'">
                                            <span>Sao chép</span>
                                        </template>
                                    </button>
                                </div>

                                {{-- Số tiền & Button Sao chép --}}
                                <div class="bg-slate-50/70 p-3 rounded-2xl border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 font-medium block text-xs">Số tiền cần chuyển:</span>
                                        <strong class="text-emerald-600 font-black text-base sm:text-lg" x-text="new Intl.NumberFormat('vi-VN').format(amount) + ' vnd'"></strong>
                                    </div>
                                    <button
                                        type="button"
                                        @click="copyText(amount, 'amt')"
                                        class="rounded-xl bg-emerald-100/70 px-4 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-200/80 transition cursor-pointer shadow-2xs"
                                    >
                                        <template x-if="copiedField === 'amt'">
                                            <span>✓ Đã chép</span>
                                        </template>
                                        <template x-if="copiedField !== 'amt'">
                                            <span>Sao chép</span>
                                        </template>
                                    </button>
                                </div>

                                {{-- Nội dung & Button Sao chép --}}
                                <div class="bg-slate-50/70 p-3 rounded-2xl border border-slate-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-slate-400 font-medium block text-xs">Nội dung chuyển khoản:</span>
                                        <strong class="text-slate-900 font-mono font-bold text-xs sm:text-sm" x-text="transferContent"></strong>
                                    </div>
                                    <button
                                        type="button"
                                        @click="copyText(transferContent, 'content')"
                                        class="rounded-xl bg-emerald-100/70 px-4 py-1.5 text-xs font-bold text-emerald-800 hover:bg-emerald-200/80 transition cursor-pointer shadow-2xs"
                                    >
                                        <template x-if="copiedField === 'content'">
                                            <span>✓ Đã chép</span>
                                        </template>
                                        <template x-if="copiedField !== 'content'">
                                            <span>Sao chép</span>
                                        </template>
                                    </button>
                                </div>

                                {{-- Dòng Lưu ý --}}
                                <p class="text-xs text-slate-500 pt-0.5 leading-relaxed">
                                    💡 <strong>Lưu ý:</strong> Nhập chính xác số tiền <strong class="text-slate-900 font-bold" x-text="new Intl.NumberFormat('vi-VN').format(amount) + 'đ'"></strong> và nội dung <strong class="text-slate-900 font-bold" x-text="transferContent"></strong> khi thao tác chuyển khoản.
                                </p>

                                {{-- Nút Xác Nhận Duyệt Đơn --}}
                                <div class="pt-1">
                                    <button
                                        type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-emerald-700 transition cursor-pointer active:scale-95"
                                    >
                                        <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Xác Nhận Đã Chuyển Tiền & Duyệt Đơn
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden inputs --}}
                        <input type="hidden" name="transaction_ref" :value="transactionRef">
                        <input type="hidden" name="admin_note" value="Đã chuyển khoản VietQR Napas247 thành công.">
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Từ chối Rút tiền --}}
        <div
            x-show="rejectModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div
                    x-show="rejectModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="rejectModalOpen = false"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                ></div>

                <div
                    x-show="rejectModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-rose-600">Từ chối Yêu cầu Rút tiền</h3>
                        <button type="button" @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form :action="'/admin/withdrawals/' + withdrawalId + '/reject'" method="POST" class="mt-4 space-y-4">
                        @csrf

                        <p class="text-xs text-slate-600">
                            Từ chối yêu cầu rút <strong class="text-rose-600 font-bold" x-text="new Intl.NumberFormat('vi-VN').format(amount) + 'đ'"></strong> của giảng viên <strong class="text-slate-900 font-bold" x-text="instructorName"></strong>. Số tiền này sẽ được hoàn về số dư khả dụng của Giảng viên và gửi thông báo lý do.
                        </p>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lý do từ chối (Gửi tới giảng viên)</label>
                            <textarea
                                name="admin_note"
                                x-model="adminNote"
                                rows="3"
                                placeholder="Ví dụ: Sai số tài khoản ngân hàng hoặc thông tin tên không khớp."
                                required
                                class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs text-slate-800 focus:border-rose-500 focus:ring-rose-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 mt-6">
                            <button
                                type="button"
                                @click="rejectModalOpen = false"
                                class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                            >
                                Hủy
                            </button>
                            <button
                                type="submit"
                                class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700 transition cursor-pointer"
                            >
                                Từ chối & Gửi Thông báo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
