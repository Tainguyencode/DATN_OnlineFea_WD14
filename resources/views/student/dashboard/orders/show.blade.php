@php
    $paymentLabel = match($order->payment_method) { 'payos' => 'PayOS VietQR', 'bank_transfer' => 'Chuyển khoản ngân hàng', default => $order->payment_method ?: 'Chưa xác định' };
    $refund = $order->refunds->first();
@endphp
<x-student-layout title="Đơn {{ $order->order_code }}" page-title="Chi tiết đơn hàng" breadcrumb="Mã đơn: {{ $order->order_code }}" :back-url="route('student.orders')">
    <div data-refresh-on-history class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5 dark:border-slate-800"><div><h2 class="font-mono text-lg font-extrabold">{{ $order->order_code }}</h2><p class="mt-1 text-sm text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</p></div><x-student.dashboard.status-badge :status="$order->status" /></div>
            @if($order->status === 'pending')<div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-800 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-300">Đơn hàng chưa hoàn tất thanh toán</div>@endif
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($order->items as $item)
                    <div class="flex gap-4 p-5"><div class="flex h-14 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-blue-50 text-[#0056D2]">@if($item->course)<img src="{{ $item->course->thumbnailUrl() }}" alt="" class="h-full w-full object-cover" onerror="this.remove()">@else<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 5h16v14H4z"/></svg>@endif</div><div class="min-w-0 flex-1"><h3 class="line-clamp-2 font-bold" title="{{ $item->course?->title }}">{{ $item->course?->title ?? 'Khóa học không còn hiển thị' }}</h3><p class="mt-1 text-sm text-slate-500">{{ $item->course?->instructor?->name }}</p></div><strong class="shrink-0 text-sm">{{ number_format((float)$item->price, 0, ',', '.') }}đ</strong></div>
                @empty
                    <p class="p-5 text-sm text-slate-500">Không có dòng sản phẩm.</p>
                @endforelse
            </div>
        </section>

        <aside class="space-y-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"><h2 class="font-extrabold">Thanh toán</h2><dl class="mt-4 space-y-3 text-sm"><div class="flex justify-between gap-3"><dt class="text-slate-500">Phương thức</dt><dd class="font-semibold text-right">{{ $paymentLabel }}</dd></div><div class="flex justify-between gap-3"><dt class="text-slate-500">Tạm tính</dt><dd>{{ number_format((float)$order->subtotal, 0, ',', '.') }}đ</dd></div><div class="flex justify-between gap-3"><dt class="text-slate-500">Giảm giá</dt><dd>-{{ number_format((float)$order->discount_amount, 0, ',', '.') }}đ</dd></div><div class="flex justify-between gap-3 border-t border-slate-100 pt-3 text-base dark:border-slate-800"><dt class="font-bold">Tổng cộng</dt><dd class="font-extrabold text-[#0056D2]">{{ number_format((float)$order->total_amount, 0, ',', '.') }}đ</dd></div></dl></section>

            @if($order->canCancel())
                <form method="POST" action="{{ route('student.orders.cancel', $order) }}" x-data="{ submitting:false }" x-on:submit="if (submitting || !confirm('Bạn chắc chắn muốn hủy đơn hàng này?')) { $event.preventDefault(); } else { submitting = true; }">@csrf @method('DELETE')<button type="submit" :disabled="submitting" class="min-h-11 w-full rounded-xl border border-rose-200 bg-white px-4 text-sm font-bold text-rose-600 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-50 hover:shadow active:translate-y-0 active:scale-95 disabled:opacity-60 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-400 dark:hover:bg-rose-950/30">Hủy đơn hàng</button></form>
            @elseif($order->status === 'cancelled')
                <p role="status" class="rounded-xl bg-slate-100 p-4 text-sm text-slate-700">Đơn hàng đã được hủy.</p>
            @elseif($order->status === 'refunded')
                <div class="rounded-2xl border border-violet-200 bg-violet-50 p-4 text-sm font-semibold text-violet-800 dark:border-violet-900 dark:bg-violet-950/30 dark:text-violet-300">Đã hoàn tiền{{ $refund?->amount ? ': '.number_format((float)$refund->amount, 0, ',', '.').'đ' : '' }}</div>
            @endif
        </aside>
    </div>
    @if($order->status === 'paid')
        @include('student.dashboard.orders.partials.refund')
    @endif
</x-student-layout>
