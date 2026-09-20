<x-student-layout :title="'Chi tiết voucher - '.$coupon->code" page-title="Chi tiết voucher" :breadcrumb="$coupon->code">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? '0đ' : number_format((float) $value, 0, ',', '.').'đ';
    $isPercent = $coupon->type === 'percent';
    $discountDisplay = $isPercent ? number_format((float)$coupon->value).'%' : $formatPrice($coupon->value);
@endphp

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('student.vouchers.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#0056D2] hover:underline dark:text-blue-400">
            ← Quay lại danh sách voucher
        </a>
    </div>

    {{-- Main Voucher Card --}}
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="grid min-h-48 md:grid-cols-[240px_minmax(0,1fr)]">
            {{-- Left Banner --}}
            <div class="flex flex-col items-center justify-center border-b border-dashed border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50/50 p-6 text-center text-[#0056D2] md:border-b-0 md:border-r dark:border-blue-900 dark:bg-gradient-to-br dark:from-blue-950/40 dark:to-indigo-950/20 dark:text-blue-300">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Voucher giảm giá</span>
                <strong class="mt-2 text-4xl font-extrabold tracking-tight">{{ $discountDisplay }}</strong>
                <span class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                    {{ $isPercent ? 'Giảm trên tổng đơn' : 'Giảm trực tiếp vào đơn' }}
                </span>
            </div>

            {{-- Right Details --}}
            <div class="p-6 md:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <code class="rounded-xl border border-dashed border-blue-300 bg-blue-50/70 px-3.5 py-1.5 font-mono text-lg font-extrabold text-blue-800 dark:border-blue-800 dark:bg-blue-950/60 dark:text-blue-200">
                            {{ $coupon->code }}
                        </code>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Đã sao chép mã {{ $coupon->code }}!');" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 cursor-pointer">
                            Sao chép
                        </button>
                    </div>
                    <x-student.dashboard.status-badge :status="$computedStatus" />
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800/60">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Đơn tối thiểu</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">
                            {{ (float) $coupon->min_order_amount > 0 ? $formatPrice($coupon->min_order_amount) : 'Không yêu cầu đơn tối thiểu' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800/60">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Hạn sử dụng</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">
                            {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'Vô thời hạn' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800/60">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Thời gian bắt đầu</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">
                            {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y H:i') : 'Có hiệu lực ngay' }}
                        </dd>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800/60">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Trạng thái áp dụng</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">
                            @if($computedStatus === 'used')
                                <span class="text-slate-500">Đã sử dụng</span>
                            @elseif($computedStatus === 'expired')
                                <span class="text-rose-600 dark:text-rose-400">Đã hết hạn</span>
                            @else
                                <span class="text-emerald-600 dark:text-emerald-400">Sẵn sàng sử dụng</span>
                            @endif
                        </dd>
                    </div>
                </div>

                {{-- Scope description --}}
                <div class="mt-6 border-t border-slate-100 pt-5 dark:border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Phạm vi áp dụng</h4>
                    <p class="mt-1 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $scopeLabel }}</p>
                </div>
            </div>
        </div>
    </article>

    {{-- Applied Course Card if applicable --}}
    @if($coupon->course)
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">Khóa học áp dụng mã này</h3>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="aspect-video w-full sm:w-48 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-slate-800">
                    @if($coupon->course->thumbnail)
                        <img src="{{ asset('storage/'.$coupon->course->thumbnail) }}" alt="{{ $coupon->course->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-indigo-900 text-xs font-bold text-white">OnlineFEA</div>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <h4 class="text-base font-bold text-slate-900 dark:text-white">{{ $coupon->course->title }}</h4>
                    <div class="mt-2 flex items-center gap-3 text-sm">
                        <span class="font-extrabold text-[#0056D2] dark:text-blue-400">
                            {{ $formatPrice($coupon->course->discount_price ?? $coupon->course->price) }}
                        </span>
                        @if($coupon->course->discount_price && $coupon->course->discount_price < $coupon->course->price)
                            <span class="text-xs text-slate-400 line-through">{{ $formatPrice($coupon->course->price) }}</span>
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('courses.show', $coupon->course->slug) }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#0046B8]">
                            Xem chi tiết khóa học →
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Cách sử dụng mã giảm giá</h3>
            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                Khi chọn khóa học và tiến hành thanh toán tại giỏ hàng, hãy nhập mã <code class="font-bold text-[#0056D2] dark:text-blue-400">{{ $coupon->code }}</code> vào ô mã giảm giá để được khấu trừ trực tiếp vào đơn hàng.
            </p>
            <div class="mt-4">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#0056D2] px-4 py-2 text-xs font-bold text-white transition hover:bg-[#0046B8]">
                    Khám phá các khóa học →
                </a>
            </div>
        </section>
    @endif
</div>

</x-student-layout>
