<x-student-layout title="PayOS Sandbox - {{ $order->order_code }}" page-title="Thanh toán VietQR" breadcrumb="PayOS Sandbox">
    <div class="mx-auto max-w-lg rounded-3xl bg-white p-8 shadow-xl">
        <div class="mb-6 text-center">
            <div class="text-sm font-bold uppercase tracking-widest text-blue-600">PayOS Sandbox</div>
            <h1 class="mt-2 text-2xl font-black">Thanh toán VietQR</h1>
            <p class="mt-2 text-sm text-slate-500">Chỉ hiển thị trong môi trường local/testing.</p>
        </div>

        <dl class="mb-6 space-y-3 rounded-2xl bg-slate-50 p-5 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Mã đơn hàng</dt>
                <dd class="font-bold">{{ $order->order_code }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Số tiền</dt>
                <dd class="font-black text-blue-600">{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</dd>
            </div>
        </dl>

        <div class="mb-6 flex justify-center">
            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&amp;data=payos%3A%2F%2Fpay%3Famount%3D{{ (int) $order->total_amount }}%26orderId%3D{{ urlencode($order->order_code) }}"
                alt="PayOS VietQR sandbox"
                class="h-56 w-56 rounded-2xl border p-3"
            >
        </div>

        <div class="grid grid-cols-2 gap-3">
            <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}">
                @csrf
                <input type="hidden" name="status" value="failed">
                <button class="w-full rounded-xl border border-slate-300 px-4 py-3 font-bold text-slate-700">Giả lập thất bại</button>
            </form>
            <form method="POST" action="{{ route('student.checkout.simulate', $order->order_code) }}">
                @csrf
                <input type="hidden" name="status" value="success">
                <button class="w-full rounded-xl bg-blue-600 px-4 py-3 font-bold text-white">Giả lập thành công</button>
            </form>
        </div>
</div>
</x-student-layout>
