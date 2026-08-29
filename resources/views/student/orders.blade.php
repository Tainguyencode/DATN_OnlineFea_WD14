@extends('layouts.app')

@section('title', 'Lịch sử giao dịch & Đơn hàng - Website học online FEA')

@section('content')
@include('partials.financial-clean-icons')
<div class="bg-slate-50 py-8 dark:bg-slate-950 min-h-[calc(100vh-16rem)]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- BREADCRUMB & LINK QUAY LẠI KHU VỰC HỌC VIÊN -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('student.dashboard') }}" class="hover:text-indigo-600 font-medium">Khu vực học viên</a>
                <span>/</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">Lịch sử giao dịch & đơn hàng</span>
            </div>

            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                ← Quay lại Trang học viên
            </a>
        </div>

        <!-- TIÊU ĐỀ VÀ BỘ LỌC TÌM KIẾM -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-950 dark:text-white">Lịch sử mua hàng & Đơn hàng</h1>
                <p class="mt-1 text-xs text-slate-500">Quản lý và tra cứu chi tiết tất cả các giao dịch mua khóa học của bạn</p>
            </div>

            <form method="GET" action="{{ route('student.orders') }}" class="flex flex-wrap items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã đơn hoặc tên khóa học..." class="h-10 w-64 sm:w-72 rounded-xl border border-slate-200 bg-white pl-9 pr-4 text-xs font-medium text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-200">
                    <svg class="absolute left-3 top-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="h-10 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white transition hover:bg-indigo-700 shadow-sm">
                    Tìm kiếm
                </button>
            </form>
        </div>

        <!-- TABS LỌC TRẠNG THÁI -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            @php
                $currentStatus = request('status', '');
                $tabs = [
                    '' => 'Tất cả đơn hàng',
                    'paid' => 'Đã thanh toán',
                    'pending' => 'Chờ thanh toán',
                    'cancelled' => 'Đã hủy / Thất bại',
                    'refunded' => 'Đã hoàn tiền',
                ];
            @endphp

            @foreach($tabs as $key => $label)
                <a href="{{ route('student.orders', array_merge(request()->except('status', 'page'), $key ? ['status' => $key] : [])) }}"
                   class="rounded-xl px-4 py-2 text-xs font-bold transition {{ $currentStatus === $key ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- DANH SÁCH ĐƠN HÀNG -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
            @if($orders->isEmpty())
                <div class="p-16 text-center text-slate-500">
                    <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="font-bold text-base text-slate-700 dark:text-slate-300">Không tìm thấy đơn hàng nào</p>
                    <p class="mt-1 text-xs text-slate-400">Bạn chưa mua khóa học nào hoặc không có đơn hàng khớp với bộ lọc.</p>
                    <a href="{{ route('courses.index') }}" class="mt-4 inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white transition hover:bg-indigo-700">
                        Khám phá khóa học ngay
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-950">
                            <tr>
                                <th class="px-6 py-4">Mã đơn hàng</th>
                                <th class="px-6 py-4">Khóa học</th>
                                <th class="px-6 py-4">Tổng tiền</th>
                                <th class="px-6 py-4">Thanh toán</th>
                                <th class="px-6 py-4">Trạng thái</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($orders as $order)
                                <tr class="transition hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('student.orders.show', $order) }}" class="font-mono text-xs font-bold text-indigo-600 hover:underline dark:text-indigo-400">
                                            #{{ $order->order_code }}
                                        </a>
                                        <div class="mt-0.5 text-[11px] text-slate-400">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="space-y-1.5">
                                            @foreach($order->items ?? [] as $item)
                                                @php
                                                    $isModel = $item instanceof \App\Models\OrderItem;
                                                    $course = $isModel ? $item->course : null;
                                                @endphp
                                                <div class="flex items-center gap-2.5">
                                                    <div class="h-9 w-14 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100 dark:border-slate-800">
                                                        <img src="{{ $course?->thumbnailUrl() ?? 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=100&auto=format&fit=crop&q=80' }}" alt="{{ $course?->title }}" class="h-full w-full object-cover" loading="lazy">
                                                    </div>
                                                    <div class="min-w-0">
                                                        <a href="{{ $course ? route('courses.show', $course->slug) : '#' }}" class="line-clamp-1 text-xs font-bold text-slate-900 hover:text-indigo-600 dark:text-white dark:hover:text-indigo-400">
                                                            {{ $course?->title ?? ($item['title'] ?? 'Khóa học') }}
                                                        </a>
                                                        <span class="text-[11px] text-slate-400">GV: {{ $course?->instructor?->name ?? 'FEA Instructor' }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-950 dark:text-white">
                                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                        </div>
                                        @if($order->coupon)
                                            <span class="inline-flex items-center text-[10px] font-semibold text-emerald-600 dark:text-emerald-400">
                                                Mã giảm: {{ $order->coupon->code }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            <span class="rounded bg-blue-100 px-2 py-0.5 text-[10px] font-bold text-blue-700">PayOS VietQR</span>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($order->status === 'paid')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Đã thanh toán
                                            </span>
                                        @elseif($order->status === 'pending')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 border border-amber-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Chờ thanh toán
                                            </span>
                                        @elseif($order->status === 'refunded')
                                            <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-xs font-bold text-purple-700 border border-purple-200">
                                                Đã hoàn tiền
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 border border-rose-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                {{ match ($order->status) {
                                                    'cancelled' => 'Đã hủy',
                                                    'failed' => 'Thanh toán thất bại',
                                                    default => ucfirst($order->status),
                                                } }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($order->status === 'pending')
                                                <a href="{{ route('student.checkout.pay', $order->order_code) }}" class="inline-flex h-8 items-center rounded-lg bg-amber-500 px-3 text-xs font-bold text-white transition hover:bg-amber-600 shadow-sm">
                                                    Thanh toán
                                                </a>
                                            @endif
                                            <a href="{{ route('student.orders.show', $order) }}" class="inline-flex h-8 items-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                                Chi tiết
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="border-t border-slate-100 p-4 dark:border-slate-800">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
