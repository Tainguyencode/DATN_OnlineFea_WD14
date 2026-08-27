<x-admin-layout title="Chi tiết Yêu cầu Hoàn tiền" page-title="Chi tiết Yêu cầu Hoàn tiền #{{ $refund->id }}">

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Top Navigation & Action Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('admin.refunds.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-indigo-600 transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Quay lại danh sách Yêu cầu Hoàn tiền
            </a>

            <div>
                @if ($refund->status === 'pending')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-4 py-1.5 text-xs font-bold text-amber-800 border border-amber-200">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span> Chờ đối soát hoàn tiền
                    </span>
                @elseif ($refund->status === 'processing')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-4 py-1.5 text-xs font-bold text-blue-800 border border-blue-200">
                        <span class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span> Đang xử lý chuyển tiền
                    </span>
                @elseif ($refund->status === 'approved')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-4 py-1.5 text-xs font-bold text-purple-800 border border-purple-200">
                        <span class="h-2 w-2 rounded-full bg-purple-500"></span> Đã hoàn tiền thành công
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-4 py-1.5 text-xs font-bold text-rose-800 border border-rose-200">
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span> Từ chối hoàn tiền
                    </span>
                @endif
            </div>
        </div>

        {{-- Main Details Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-5 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Yêu cầu hoàn tiền đơn hàng</span>
                    <h1 class="font-mono text-2xl font-extrabold text-slate-900 mt-1">#{{ $refund->order?->order_code }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Ngày gửi: {{ $refund->created_at->format('d/m/Y H:i:s') }}</p>
                </div>

                <div class="text-left sm:text-right">
                    <span class="text-xs font-semibold text-slate-500 block">Số tiền yêu cầu hoàn:</span>
                    <span class="font-black text-2xl text-indigo-600">{{ number_format($refund->amount, 0, ',', '.') }}đ</span>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 text-xs">
                {{-- Học viên --}}
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 space-y-2">
                    <h3 class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Thông tin Học viên</h3>
                    <p class="font-extrabold text-sm text-slate-900">{{ $refund->user?->name }}</p>
                    <p class="text-slate-500">{{ $refund->user?->email }}</p>
                    <p class="text-slate-500">SĐT: {{ $refund->user?->phone ?? 'Chưa cập nhật' }}</p>
                </div>

                {{-- Ngân hàng nhận --}}
                <div class="rounded-2xl bg-indigo-50/60 p-4 border border-indigo-100 space-y-2">
                    <h3 class="font-bold uppercase tracking-wider text-indigo-500 text-[10px]">Tài khoản Ngân hàng nhận Refund</h3>
                    <p class="font-extrabold text-sm text-slate-900">{{ $refund->bank_code }} - {{ $refund->bank_account_name }}</p>
                    <p class="font-mono text-base font-black text-indigo-700">{{ $refund->bank_account_number }}</p>
                </div>
            </div>

            {{-- Lý do yêu cầu hoàn tiền --}}
            <div class="rounded-2xl border border-slate-200 p-4 space-y-1.5 text-xs">
                <h3 class="font-bold text-slate-700">Lý do yêu cầu hoàn tiền từ Học viên:</h3>
                <p class="text-slate-800 leading-relaxed italic bg-slate-50 p-3 rounded-xl border border-slate-100">"{{ $refund->reason }}"</p>
            </div>

            {{-- Khóa học trong đơn hàng --}}
            <div class="space-y-3 text-xs">
                <h3 class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Khóa học mua trong đơn hàng này</h3>
                <div class="divide-y divide-slate-100 rounded-2xl border border-slate-200 overflow-hidden">
                    @foreach($refund->order?->items ?? [] as $item)
                        <div class="p-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->course?->thumbnailUrl() ?? 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=100' }}" class="h-10 w-14 rounded-lg object-cover border border-slate-200">
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $item->course?->title ?? 'Khóa học' }}</span>
                                    <span class="text-[11px] text-slate-400">Giảng viên: {{ $item->course?->instructor?->name ?? 'FEA Academy' }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-slate-900">{{ number_format($item->price, 0, ',', '.') }}đ</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Kết quả xử lý Admin --}}
            @if (in_array($refund->status, ['approved', 'rejected'], true))
                <div class="rounded-2xl p-4 border text-xs space-y-1.5 {{ $refund->status === 'approved' ? 'bg-purple-50 border-purple-200 text-purple-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                    <p class="font-bold">Kết quả xử lý từ Ban Quản Trị:</p>
                    <p>Phương thức: <strong>{{ $refund->refund_method === 'payos_payout' ? 'PayOS Payout API Tự động' : 'Xác nhận thủ công' }}</strong></p>
                    @if($refund->transaction_reference)
                        <p class="font-mono">Mã tham chiếu giao dịch: <strong>{{ $refund->transaction_reference }}</strong></p>
                    @endif
                    @if($refund->admin_note)
                        <p>Ghi chú: <em>"{{ $refund->admin_note }}"</em></p>
                    @endif
                    <p class="text-[11px] opacity-75">Thời gian xử lý: {{ $refund->processed_at ? $refund->processed_at->format('d/m/Y H:i:s') : $refund->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
            @endif

        </div>

    </div>

</x-admin-layout>
