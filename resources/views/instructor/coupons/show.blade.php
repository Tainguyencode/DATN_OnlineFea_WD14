<x-instructor-layout :title="'Chi tiết mã giảm giá - '.$coupon->code" page-title="Chi tiết mã giảm giá" :breadcrumb="$coupon->code">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? '0đ' : number_format((float) $value, 0, ',', '.').'đ';
    $isPercent = $coupon->type === 'percent';
    $discountDisplay = $isPercent ? number_format((float) $coupon->value).'%' : $formatPrice($coupon->value);
    $isValid = $coupon->isValid();
@endphp

<div class="space-y-6">
    {{-- Header / Summary Card --}}
    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-xl bg-indigo-50 px-4 py-2 font-mono text-xl font-extrabold text-indigo-700 ring-1 ring-indigo-200">
                        {{ $coupon->code }}
                    </span>
                    @if($coupon->is_active)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Đang hoạt động
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                            Đã tắt
                        </span>
                    @endif

                    @if($coupon->expires_at && $coupon->expires_at->isPast())
                        <span class="inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">
                            Hết hạn
                        </span>
                    @endif

                    @if($coupon->is_private)
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200">
                            Mã riêng tư
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-700 ring-1 ring-sky-200">
                            Mã công khai
                        </span>
                    @endif
                </div>

                <p class="text-sm text-slate-600">
                    Giảm <strong class="text-indigo-600">{{ $discountDisplay }}</strong> cho đơn hàng đủ điều kiện.
                </p>
            </div>

            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('instructor.coupons.index') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    ← Danh sách mã
                </a>
                <a href="{{ route('instructor.coupons.edit', $coupon) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Chỉnh sửa
                </a>
                <form method="POST" action="{{ route('instructor.coupons.toggle-status', $coupon) }}">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        {{ $coupon->is_active ? 'Tắt mã' : 'Bật mã' }}
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Details & Metrics Grid --}}
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Mức giảm giá</span>
            <strong class="mt-2 block text-2xl font-extrabold text-indigo-600">{{ $discountDisplay }}</strong>
            <span class="mt-1 block text-xs text-slate-400">{{ $isPercent ? 'Giảm theo phần trăm' : 'Giảm số tiền cố định' }}</span>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Đơn hàng tối thiểu</span>
            <strong class="mt-2 block text-2xl font-extrabold text-slate-950">
                {{ (float) $coupon->min_order_amount > 0 ? $formatPrice($coupon->min_order_amount) : 'Không yêu cầu' }}
            </strong>
            <span class="mt-1 block text-xs text-slate-400">Điều kiện giá trị đơn</span>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Lượt đã sử dụng</span>
            <strong class="mt-2 block text-2xl font-extrabold text-slate-950">
                {{ number_format($coupon->used_count) }} <span class="text-base font-normal text-slate-400">/ {{ $coupon->max_uses ? number_format($coupon->max_uses) : '∞' }}</span>
            </strong>
            <span class="mt-1 block text-xs text-slate-400">{{ $coupon->max_uses ? 'Giới hạn số lần' : 'Không giới hạn lượt' }}</span>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Hạn sử dụng</span>
            <strong class="mt-2 block text-base font-bold text-slate-950">
                {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'Vô thời hạn' }}
            </strong>
            <span class="mt-1 block text-xs text-slate-400">
                Bắt đầu: {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y H:i') : 'Ngay lập tức' }}
            </span>
        </div>
    </section>

    {{-- Scope & Applicability --}}
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-base font-bold text-slate-950">Phạm vi áp dụng</h3>
        <div class="mt-4">
            @if($coupon->course)
                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="h-16 w-24 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                        @if($coupon->course->thumbnail)
                            <img src="{{ asset('storage/'.$coupon->course->thumbnail) }}" alt="{{ $coupon->course->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-slate-800 text-xs font-bold text-white">Khóa học</div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-xs font-bold uppercase text-indigo-600">Chỉ áp dụng cho khóa học cụ thể:</div>
                        <h4 class="mt-1 text-sm font-bold text-slate-900">{{ $coupon->course->title }}</h4>
                        <div class="mt-1 flex items-center gap-3 text-xs text-slate-500">
                            <span>Giá: {{ $formatPrice($coupon->course->discount_price ?? $coupon->course->price) }}</span>
                            <a href="{{ route('instructor.courses.show', $coupon->course) }}" class="font-semibold text-indigo-600 hover:underline">Xem khóa học →</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-sm text-emerald-900">
                    <div class="flex items-center gap-2 font-bold">
                        <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Áp dụng cho tất cả khóa học của bạn</span>
                    </div>
                    <p class="mt-1 text-xs text-emerald-700">Học viên có thể sử dụng mã này cho bất kỳ khóa học nào do bạn xuất bản.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Usage History / Orders --}}
    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-950">Lịch sử đơn hàng đã sử dụng mã</h3>
            <span class="text-xs text-slate-500 font-semibold">{{ $orders->total() }} đơn hàng</span>
        </div>

        @if($orders->isEmpty())
            <div class="rounded-lg border border-dashed border-slate-200 p-8 text-center text-sm text-slate-400">
                Chưa có đơn hàng nào áp dụng mã giảm giá này.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Mã đơn</th>
                            <th class="px-4 py-3">Học viên</th>
                            <th class="px-4 py-3 text-right">Số tiền giảm</th>
                            <th class="px-4 py-3 text-right">Tổng thanh toán</th>
                            <th class="px-4 py-3 text-center">Trạng thái</th>
                            <th class="px-4 py-3 text-right">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $order->code ?? '#'.$order->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900">{{ $order->user?->name ?? 'Học viên' }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->user?->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-emerald-600">
                                    -{{ $formatPrice($order->discount_amount ?? 0) }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-slate-900">
                                    {{ $formatPrice($order->total_amount ?? $order->final_price ?? 0) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $order->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $order->status === 'paid' ? 'Đã thanh toán' : $order->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-slate-500">
                                    {{ $order->created_at?->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif
    </section>
</div>

</x-instructor-layout>
