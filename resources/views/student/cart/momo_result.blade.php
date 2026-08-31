<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $success ? 'Thanh toán thành công' : 'Kết quả thanh toán' }} - FEA Online</title>
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
<main class="flex min-h-screen items-center justify-center px-4 py-10">
    <section class="w-full max-w-xl text-center">
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full text-4xl text-white {{ $success ? 'bg-emerald-500' : 'bg-rose-500' }}">
            {{ $success ? '✓' : '×' }}
        </div>
        <h1 class="mt-6 text-2xl font-black">{{ $success ? 'Thanh toán thành công' : 'Thanh toán không thành công' }}</h1>
        <p class="mt-2 text-sm text-slate-500">{{ $message }}</p>

        <div class="mt-7 rounded-3xl border border-slate-200 bg-white p-6 text-left shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <dl class="grid grid-cols-2 gap-y-3 text-xs">
                <dt class="text-slate-500">Mã đơn hàng:</dt>
                <dd class="text-right font-mono font-bold">{{ $order->order_code }}</dd>
                <dt class="text-slate-500">Số tiền:</dt>
                <dd class="text-right font-bold text-pink-700">{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</dd>
                <dt class="text-slate-500">Phương thức:</dt>
                <dd class="text-right font-bold">MoMo</dd>
                <dt class="text-slate-500">Mã giao dịch:</dt>
                <dd class="break-all text-right font-mono font-bold">{{ $payment->transaction_id ?? 'N/A' }}</dd>
            </dl>
            <div class="mt-6 flex gap-3 border-t border-slate-100 pt-5">
                @if($success)
                    <x-payment-learning-links :order="$order" />
                @else
                    <a href="{{ route('student.checkout.pay', $order->order_code) }}" class="flex-1 rounded-xl bg-pink-700 px-4 py-3 text-center text-xs font-bold text-white">Thử thanh toán lại</a>
                @endif
                <a href="{{ route('home') }}" class="rounded-xl bg-slate-100 px-4 py-3 text-center text-xs font-bold dark:bg-slate-800">Trang chủ</a>
            </div>
        </div>
    </section>
</main>
</body>
</html>
