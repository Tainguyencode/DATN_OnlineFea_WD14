<x-student-layout title="Đơn hàng" page-title="Lịch sử giao dịch">

<div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
    @if($orders->isEmpty())
        <div class="p-16 text-center text-slate-500">Chưa có đơn hàng nào.</div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-slate-600">Mã đơn</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-600">Khóa học</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-600">Tổng tiền</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-600">Trạng thái</th>
                        <th class="text-left px-6 py-4 font-semibold text-slate-600">Ngày</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($orders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-mono text-[#0056D2]">{{ $order->order_code }}</td>
                            <td class="px-6 py-4">{{ $order->items->pluck('course.title')->join(', ') }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $order->status === 'paid' ? 'Đã thanh toán' : ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">{{ $orders->links() }}</div>
    @endif
</div>

</x-student-layout>
