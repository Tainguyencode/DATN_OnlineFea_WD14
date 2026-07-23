<x-student-layout title="Voucher của tôi" page-title="Voucher của tôi" breadcrumb="Chỉ hiển thị voucher đã được lưu hoặc cấp cho tài khoản của bạn.">
    <div class="mb-5 flex flex-wrap gap-2" aria-label="Lọc voucher">
        @foreach(['all' => 'Tất cả', 'active' => 'Còn hiệu lực', 'used' => 'Đã sử dụng', 'expired' => 'Hết hạn'] as $key => $label)
            <a href="{{ route('student.vouchers.index', ['status' => $key]) }}" @if($status === $key) aria-current="page" @endif class="inline-flex min-h-10 items-center gap-2 rounded-xl px-4 text-sm font-bold shadow-sm transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 {{ $status === $key ? 'bg-[#0056D2] text-white shadow-blue-500/20 hover:bg-[#0046B8]' : 'border border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}"><span>{{ $label }}</span><span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts[$key] }}</span></a>
        @endforeach
    </div>

    @if($userCoupons->isEmpty())
        <x-student.dashboard.empty-state title="Không có voucher ở trạng thái này" description="Voucher thuộc tài khoản của bạn sẽ xuất hiện tại đây." />
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($userCoupons as $userCoupon)
                @php $coupon = $userCoupon->coupon; @endphp
                <article class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="grid min-h-44 grid-cols-[7rem_minmax(0,1fr)] sm:grid-cols-[9rem_minmax(0,1fr)]">
                        <div class="flex flex-col items-center justify-center border-r border-dashed border-blue-200 bg-blue-50 p-3 text-center text-[#0056D2] dark:border-blue-900 dark:bg-blue-950/30 dark:text-blue-300"><strong class="text-2xl font-extrabold">{{ $coupon->type === 'percent' ? number_format((float)$coupon->value).'%' : number_format((float)$coupon->value, 0, ',', '.').'đ' }}</strong><span class="mt-1 text-xs font-bold uppercase tracking-wide">Giảm giá</span></div>
                        <div class="min-w-0 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2"><code class="rounded-lg bg-slate-100 px-2.5 py-1 font-bold text-slate-900 dark:bg-slate-800 dark:text-white">{{ $coupon->code }}</code><x-student.dashboard.status-badge :status="$userCoupon->computed_status" /></div>
                            <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">{{ $userCoupon->scope_label }}</p>
                            <dl class="mt-3 space-y-1 text-xs text-slate-500"><div class="flex justify-between gap-2"><dt>Đơn tối thiểu</dt><dd class="font-semibold text-slate-700 dark:text-slate-200">{{ $coupon->min_order_amount > 0 ? number_format((float)$coupon->min_order_amount, 0, ',', '.').'đ' : 'Không yêu cầu' }}</dd></div><div class="flex justify-between gap-2"><dt>Hạn dùng</dt><dd class="font-semibold text-slate-700 dark:text-slate-200">{{ $coupon->expires_at?->format('d/m/Y H:i') ?? 'Không giới hạn' }}</dd></div></dl>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-student-layout>
