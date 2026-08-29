@php
    $courseNames = $order->items->map(fn($item) => $item->course?->title)->filter();
    $paymentLabel = match($order->payment_method) { 'payos' => 'PayOS VietQR', 'bank_transfer' => 'Chuyển khoản', default => $order->payment_method ?: 'Chưa xác định' };
@endphp
<article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <div class="flex items-start justify-between gap-3"><div><p class="font-mono text-sm font-extrabold text-slate-900 dark:text-white">{{ $order->order_code }}</p><time class="mt-1 block text-xs text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</time></div><x-student.dashboard.status-badge :status="$order->status" /></div>
    <p class="mt-4 line-clamp-2 text-sm font-semibold text-slate-700 dark:text-slate-200" title="{{ $courseNames->join(', ') }}">{{ $courseNames->join(', ') ?: 'Khóa học không còn hiển thị' }}</p>
    <dl class="mt-4 grid grid-cols-2 gap-3 text-xs"><div><dt class="text-slate-500">Tổng tiền</dt><dd class="mt-1 text-sm font-extrabold text-slate-900 dark:text-white">{{ number_format((float)$order->total_amount, 0, ',', '.') }}đ</dd></div><div><dt class="text-slate-500">Phương thức</dt><dd class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $paymentLabel }}</dd></div></dl>
    <a href="{{ route('student.orders.show', $order) }}" class="mt-4 inline-flex min-h-10 w-full items-center justify-center rounded-xl border border-blue-200 text-sm font-bold text-[#0056D2] hover:bg-blue-50 dark:border-blue-900 dark:text-blue-300">Xem chi tiết</a>
</article>
