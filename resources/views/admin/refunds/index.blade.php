<x-admin-layout title="Quản lý Hoàn tiền Đơn hàng" page-title="Quản lý & Duyệt Yêu cầu Hoàn tiền (PayOS Refund)">

    <div x-data="{
        approveModalOpen: false,
        rejectModalOpen: false,
        refundId: null,
        orderCode: '',
        userName: '',
        amount: 0,
        bankCode: '',
        bankAcc: '',
        bankName: '',
        reason: '',
        adminNote: '',
        transactionReference: '',

        get vietQrUrl() {
            if (!this.bankCode || !this.bankAcc) return '';
            const content = `HOAN TIEN DH ${this.orderCode}`;
            return `https://img.vietqr.io/image/${encodeURIComponent(this.bankCode)}-${encodeURIComponent(this.bankAcc)}-compact2.png?amount=${Math.round(this.amount)}&addInfo=${encodeURIComponent(content)}&accountName=${encodeURIComponent(this.bankName)}`;
        },

        openApproveModal(id, code, name, amt, bank, acc, bName, rsn) {
            this.refundId = id;
            this.orderCode = code;
            this.userName = name;
            this.amount = amt;
            this.bankCode = bank;
            this.bankAcc = acc;
            this.bankName = bName;
            this.reason = rsn;
            this.adminNote = '';
            this.transactionReference = 'FT' + new Date().toISOString().replace(/\D/g, '').slice(0, 14);
            this.approveModalOpen = true;
        },

        openRejectModal(id, code, name, amt) {
            this.refundId = id;
            this.orderCode = code;
            this.userName = name;
            this.amount = amt;
            this.adminNote = '';
            this.rejectModalOpen = true;
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
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50/90 p-4 text-rose-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-xl bg-rose-500/10 p-2 text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Stat Summary Cards --}}
        @php
            $countPending = \App\Models\Refund::where('status', 'pending')->count();
            $totalPending = \App\Models\Refund::where('status', 'pending')->sum('amount');
            $countApproved = \App\Models\Refund::where('status', 'approved')->count();
            $totalApproved = \App\Models\Refund::where('status', 'approved')->sum('amount');
            $countRejected = \App\Models\Refund::where('status', 'rejected')->count();
        @endphp

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Chờ duyệt hoàn tiền</span>
                    <div class="rounded-xl bg-amber-50 p-2.5 text-amber-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-amber-600">{{ number_format($totalPending, 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $countPending }} yêu cầu chờ đối soát</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Đã hoàn tiền thành công</span>
                    <div class="rounded-xl bg-purple-50 p-2.5 text-purple-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($totalApproved, 0, ',', '.') }}đ</span>
                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ $countApproved }} đơn hàng đã hoàn tiền</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Từ chối hoàn tiền</span>
                    <div class="rounded-xl bg-rose-50 p-2.5 text-rose-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-rose-600">{{ number_format($countRejected) }}</span>
                    <p class="mt-1 text-xs font-semibold text-slate-500">Yêu cầu hoàn tiền bị từ chối</p>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 p-5 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Danh sách Yêu cầu Hoàn tiền</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Quản lý và đối soát yêu cầu trả lại tiền cho học viên khi hủy khóa học</p>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.refunds.index') }}" class="mt-4 sm:mt-0 flex flex-wrap items-center gap-3">
                    <div class="relative min-w-64">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Mã đơn, học viên, STK..."
                            class="w-full rounded-xl border-slate-300 py-2 pl-9 pr-4 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <select name="status" onchange="this.form.submit()" class="rounded-xl border-slate-300 py-2 px-3 text-xs font-semibold text-slate-700 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt (Pending)</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang xử lý (Processing)</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã hoàn tiền (Approved)</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối (Rejected)</option>
                    </select>

                    <button type="submit" class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                        Lọc
                    </button>
                    @if (request('search') || request('status'))
                        <a href="{{ route('admin.refunds.index') }}" class="text-xs font-semibold text-rose-600 hover:underline">Xóa lọc</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Mã đơn & Ngày tạo</th>
                            <th class="px-5 py-3.5">Học viên</th>
                            <th class="px-5 py-3.5 text-right">Số tiền hoàn</th>
                            <th class="px-5 py-3.5">Tài khoản nhận refund</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Trạng thái</th>
                            <th class="px-5 py-3.5 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($refunds as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4">
                                    <span class="font-mono font-bold text-indigo-600 text-sm">#{{ $item->order?->order_code ?? 'N/A' }}</span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $item->created_at->format('H:i - d/m/Y') }}</p>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 shrink-0 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($item->user?->name ?? 'HV', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block">{{ $item->user?->name ?? 'N/A' }}</span>
                                            <p class="text-slate-500 text-[11px]">{{ $item->user?->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-right font-black text-sm text-indigo-600">
                                    {{ number_format($item->amount, 0, ',', '.') }}đ
                                </td>

                                <td class="px-5 py-4">
                                    <p class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                        <span class="rounded bg-slate-100 text-slate-800 font-mono font-bold text-[10px] px-1.5 py-0.5 uppercase border border-slate-200">{{ $item->bank_code }}</span>
                                        <span>{{ $item->bank_account_name }}</span>
                                    </p>
                                    <p class="font-mono text-slate-800 font-bold mt-1 text-xs">{{ $item->bank_account_number }}</p>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 italic mt-0.5">Lý do: "{{ $item->reason }}"</p>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    @if ($item->status === 'pending')
                                        <span class="status-badge status-pending">Chờ đối soát</span>
                                    @elseif ($item->status === 'processing')
                                        <span class="status-badge status-info">Đang xử lý</span>
                                    @elseif ($item->status === 'approved')
                                        <span class="status-badge status-success">Đã hoàn tiền</span>
                                    @else
                                        <span class="status-badge status-danger">Từ chối</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if (in_array($item->status, ['pending', 'processing'], true))
                                        <div class="flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                @click="openApproveModal({{ $item->id }}, '{{ $item->order?->order_code }}', '{{ addslashes($item->user?->name) }}', {{ $item->amount }}, '{{ $item->bank_code }}', '{{ $item->bank_account_number }}', '{{ addslashes($item->bank_account_name) }}', '{{ addslashes($item->reason) }}')"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-purple-600 px-3 py-2 text-xs font-bold text-white shadow-md hover:bg-purple-700 transition active:scale-95 cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                {{ $item->status === 'processing' ? 'Thử xử lý lại' : 'Duyệt Hoàn tiền' }}
                                            </button>

                                            @if($item->status === 'pending')
                                            <button
                                                type="button"
                                                @click="openRejectModal({{ $item->id }}, '{{ $item->order?->order_code }}', '{{ addslashes($item->user?->name) }}', {{ $item->amount }})"
                                                class="inline-flex items-center rounded-xl border border-rose-300 bg-white px-2.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-50 transition cursor-pointer"
                                            >
                                                Từ chối
                                            </button>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-left text-[11px] space-y-0.5">
                                            <span class="inline-block rounded bg-slate-100 px-2 py-0.5 font-semibold text-slate-700">
                                                Cơ chế: {{ $item->refund_method === 'payos_payout' ? 'PayOS Payout Auto' : 'Thủ công' }}
                                            </span>
                                            @if ($item->transaction_reference)
                                                <p class="font-mono text-slate-500">Ref: {{ $item->transaction_reference }}</p>
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
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Chưa có yêu cầu hoàn tiền nào</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($refunds->hasPages())
                <div class="border-t border-slate-200 p-4">
                    {{ $refunds->links() }}
                </div>
            @endif
        </div>

        {{-- Modal duyệt hoàn tiền thủ công bằng VietQR --}}
        <div
            x-show="approveModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div @click="approveModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div class="relative max-h-[92vh] transform overflow-y-auto rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl p-6 border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-extrabold text-slate-900">Duyệt Hoàn tiền Đơn hàng #<span x-text="orderCode"></span></h3>
                        <button type="button" @click="approveModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <form :action="'/admin/refunds/' + refundId + '/approve'" method="POST" class="mt-4 space-y-4 text-xs">
                        @csrf

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start">
                            <div class="order-2 mx-auto w-full max-w-sm rounded-3xl border border-emerald-200 bg-emerald-50/60 p-3 text-center shadow-sm md:order-2 md:sticky md:top-0">
                                <div class="aspect-square w-full overflow-hidden rounded-2xl border border-slate-200 bg-white p-2">
                                    <img :src="vietQrUrl" alt="Mã VietQR hoàn tiền" class="h-full w-full object-contain">
                                </div>
                                <p class="mt-2 font-extrabold text-emerald-800">Quét VietQR để hoàn tiền</p>
                                <p class="mt-1 text-[11px] leading-relaxed text-slate-500">QR đã điền sẵn số tiền và nội dung. Hãy kiểm tra trên ứng dụng ngân hàng trước khi chuyển.</p>
                            </div>

                        <div class="order-1 space-y-4 md:order-1">
                        <div class="rounded-2xl bg-indigo-50/80 p-4 border border-indigo-100 space-y-2">
                            <div class="flex justify-between items-center text-slate-700">
                                <span>Học viên nhận:</span>
                                <strong class="font-bold text-slate-900" x-text="userName"></strong>
                            </div>
                            <div class="flex justify-between items-center text-slate-700">
                                <span>Số tiền hoàn trả:</span>
                                <strong class="font-black text-sm text-indigo-600" x-text="new Intl.NumberFormat('vi-VN').format(amount) + 'đ'"></strong>
                            </div>
                            <div class="flex justify-between items-center text-slate-700">
                                <span>Tài khoản ngân hàng:</span>
                                <strong class="font-mono font-bold text-slate-900" x-text="bankCode + ' - ' + bankAcc + ' (' + bankName + ')'"></strong>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
                            <p class="font-extrabold">Quy trình hoàn tiền thủ công</p>
                            <p class="mt-1 leading-relaxed">Chuyển khoản đúng thông tin bên trên qua ứng dụng ngân hàng hoặc PayOS Dashboard, sau đó nhập mã giao dịch để xác nhận.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ghi chú duyệt (Không bắt buộc)</label>
                            <input
                                type="text"
                                name="admin_note"
                                x-model="adminNote"
                                placeholder="Ghi chú mã giao dịch hoặc thông tin đối soát..."
                                class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs text-slate-800 focus:border-purple-500 focus:ring-purple-500"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mã giao dịch/đối soát *</label>
                            <input type="text" name="transaction_reference" x-model="transactionReference" required minlength="4" maxlength="100" placeholder="Ví dụ: FT260826123456" class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs font-mono text-slate-800 focus:border-purple-500 focus:ring-purple-500" />
                            <p class="mt-1 text-[11px] text-slate-500">Chỉ xác nhận sau khi đã chuyển tiền thành công. Mã này sẽ hiển thị cho học viên.</p>
                        </div>
                        </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 mt-6">
                            <button type="button" @click="approveModalOpen = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Hủy</button>
                            <button type="submit" class="rounded-xl bg-purple-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-purple-700 transition">Xác nhận Duyệt & Hoàn tiền</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Từ chối Hoàn tiền --}}
        <div
            x-show="rejectModalOpen"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                <div @click="rejectModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6 border border-slate-200">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-base font-bold text-rose-600">Từ chối Yêu cầu Hoàn tiền</h3>
                        <button type="button" @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <form :action="'/admin/refunds/' + refundId + '/reject'" method="POST" class="mt-4 space-y-4 text-xs">
                        @csrf

                        <p class="text-slate-600">
                            Bạn có chắc chắn muốn từ chối yêu cầu hoàn tiền số tiền <strong class="text-rose-600 font-bold" x-text="new Intl.NumberFormat('vi-VN').format(amount) + 'đ'"></strong> của học viên <strong class="text-slate-900 font-bold" x-text="userName"></strong> cho đơn hàng #<strong class="text-slate-900 font-bold" x-text="orderCode"></strong> không?
                        </p>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lý do từ chối (Gửi tới học viên) *</label>
                            <textarea
                                name="admin_note"
                                x-model="adminNote"
                                rows="3"
                                placeholder="Ví dụ: Khóa học đã học quá 50% tiến độ hoặc thời hạn yêu cầu hoàn tiền đã vượt quá 7 ngày quy định."
                                required
                                class="w-full rounded-xl border-slate-300 py-2 px-3 text-xs text-slate-800 focus:border-rose-500 focus:ring-rose-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4 mt-6">
                            <button type="button" @click="rejectModalOpen = false" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Hủy</button>
                            <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700 transition">Từ chối & Gửi Thông báo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
