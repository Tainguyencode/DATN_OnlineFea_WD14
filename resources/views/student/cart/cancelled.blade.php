<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kết quả hủy thanh toán - FEA Learning</title>
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 dark:bg-slate-950 dark:text-white">
    <main data-refresh-on-history class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl space-y-6 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M6 18L18 6"/></svg>
            </div>
            <h1 class="text-2xl font-black">Hủy đơn hàng thành công!</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Đơn hàng đã được hủy. Khóa học chưa được kích hoạt từ đơn này.
            </p>
            <section class="space-y-4 rounded-3xl border border-slate-200 bg-white p-6 text-left shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <h2 class="font-bold">Thông tin đơn hàng</h2>
                <div class="flex justify-between gap-4 text-sm"><span>Mã đơn hàng</span><strong>{{ $order->order_code }}</strong></div>
                <div class="flex justify-between gap-4 text-sm"><span>Giá trị đơn hàng</span><strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></div>
                <div class="flex justify-between gap-4 text-sm"><span>Trạng thái</span><strong>Đã hủy</strong></div>
            </section>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('student.orders') }}" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white">Xem đơn hàng</a>
                <a href="{{ route('student.cart') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold">Quay về giỏ hàng</a>
            </div>
        </div>
    </main>
</body>
</html>
