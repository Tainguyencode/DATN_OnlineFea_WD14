@extends('layouts.app')

@section('title', 'Kho Voucher của tôi - OnlineFEA')

@section('content')
<div class="bg-slate-50 py-8 dark:bg-slate-950 min-h-[calc(100vh-16rem)]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- BREADCRUMB & LINK QUAY LẠI KHU VỰC HỌC VIÊN -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <a href="{{ route('student.dashboard') }}" class="hover:text-indigo-600 font-medium">Khu vực học viên</a>
                <span>/</span>
                <span class="font-bold text-slate-800 dark:text-slate-200">Kho Voucher</span>
            </div>

            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                ← Quay lại Trang học viên
            </a>
        </div>

        <!-- Header banner / Intro -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 p-6 text-white shadow-xl shadow-indigo-500/10">
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black tracking-tight flex items-center gap-2">
                        <span>🎁</span> Kho Voucher Cá Nhân
                    </h2>
                    <p class="mt-1 text-sm text-indigo-100/90 max-w-xl">
                        Danh sách tất cả các mã giảm giá dành riêng cho tài khoản của bạn.
                    </p>
                </div>
                <div class="flex items-center gap-3 self-start sm:self-auto rounded-xl bg-white/10 backdrop-blur-md px-4 py-2.5 border border-white/20">
                    <svg class="h-6 w-6 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <div>
                        <div class="text-xs text-indigo-200 uppercase font-semibold">Voucher khả dụng</div>
                        <div class="text-lg font-bold text-white">{{ $counts['active'] ?? 0 }} mã</div>
                    </div>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="absolute -right-8 -bottom-12 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-3 dark:border-slate-800">
            @php
                $tabs = [
                    'active' => ['label' => 'Còn hiệu lực', 'count' => $counts['active'], 'icon' => '🟢'],
                    'all' => ['label' => 'Tất cả', 'count' => $counts['all'], 'icon' => '📋'],
                    'used' => ['label' => 'Đã sử dụng', 'count' => $counts['used'], 'icon' => '⚫'],
                    'expired' => ['label' => 'Hết hạn', 'count' => $counts['expired'], 'icon' => '🔴'],
                ];
            @endphp

            @foreach ($tabs as $key => $tab)
                @php
                    $isActive = $currentFilter === $key;
                @endphp
                <a href="{{ route('student.vouchers.index', ['status' => $key]) }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition-all duration-200 {{ $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20 dark:bg-indigo-500' : 'bg-white text-slate-600 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800' }}">
                    <span>{{ $tab['icon'] }}</span>
                    <span>{{ $tab['label'] }}</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-extrabold {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        <!-- Vouchers List / Empty State -->
        @if($userCoupons->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center dark:border-slate-800 dark:bg-slate-900 shadow-sm">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/50 dark:text-indigo-400">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-base font-bold text-slate-900 dark:text-white">Không tìm thấy voucher</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-sm">
                    Bạn hiện chưa có mã giảm giá nào thuộc danh mục này.
                </p>
                <a href="{{ route('student.vouchers.index', ['status' => 'all']) }}" 
                   class="mt-5 inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition">
                    Xem tất cả voucher
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($userCoupons as $userCoupon)
                    @php
                        $coupon = $userCoupon->coupon;
                        $status = $userCoupon->computed_status ?? 'expired';
                        $statusLabel = $userCoupon->status_label ?? 'Hết hạn';
                    @endphp

                    @if($coupon)
                        <div class="relative flex overflow-hidden rounded-2xl border bg-white shadow-sm transition hover:shadow-md dark:bg-slate-900 {{ $status === 'active' ? 'border-indigo-200 dark:border-indigo-900/50' : 'border-slate-200 opacity-75 dark:border-slate-800' }}">
                            
                            <!-- Left Ticket Notch & Discount Value Banner -->
                            <div class="w-1/3 flex flex-col items-center justify-center p-4 text-center text-white relative {{ $status === 'active' ? 'bg-gradient-to-br from-indigo-600 to-purple-600' : 'bg-gradient-to-br from-slate-600 to-slate-700' }}">
                                <!-- Discount Text -->
                                <div class="font-black text-2xl tracking-tight">
                                    @if($coupon->type === 'percent')
                                        {{ (int) $coupon->value }}%
                                    @else
                                        {{ number_format($coupon->value, 0, ',', '.') }}đ
                                    @endif
                                </div>
                                <div class="text-[11px] font-bold tracking-wider uppercase opacity-90 mt-0.5">
                                    GIẢM GIÁ
                                </div>

                                <!-- Creator Tag -->
                                <div class="mt-2 inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-[10px] font-bold tracking-wide backdrop-blur-sm truncate max-w-full">
                                    {{ $userCoupon->creator_tag ?? 'Hệ thống' }}
                                </div>

                                <!-- Dashed separator simulation -->
                                <div class="absolute right-0 top-0 bottom-0 border-r-2 border-dashed border-white/40"></div>
                            </div>

                            <!-- Right Ticket Content Details -->
                            <div class="w-2/3 p-4 flex flex-col justify-between space-y-3">
                                <div>
                                    <!-- Header Code & Status Badge -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="font-mono text-sm font-black tracking-wider text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 select-all">
                                            {{ $coupon->code }}
                                        </div>

                                        @if($status === 'active')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                <span>🟢</span> Còn hiệu lực
                                            </span>
                                        @elseif($status === 'used')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                <span>⚫</span> Đã sử dụng
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-700 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                <span>🔴</span> Hết hạn
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Conditions List -->
                                    <div class="mt-3 space-y-1 text-xs text-slate-600 dark:text-slate-400">
                                        @if(($userCoupon->source === 'admin' || $userCoupon->source === 'leaderboard') && $userCoupon->reason)
                                            <div class="flex items-center gap-1.5 text-amber-700 dark:text-amber-400 font-medium">
                                                <span>🎁</span>
                                                <span>Lý do: <strong>{{ $userCoupon->reason }}</strong></span>
                                            </div>
                                        @endif

                                        <!-- Minimum order amount -->
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2z"/>
                                            </svg>
                                            <span>
                                                @if($coupon->min_order_amount > 0)
                                                    Đơn tối thiểu: <strong class="text-slate-800 dark:text-slate-200">{{ number_format($coupon->min_order_amount, 0, ',', '.') }}đ</strong>
                                                @else
                                                    Không quy định đơn tối thiểu
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Scope display -->
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            <span class="truncate" title="{{ $userCoupon->scope_label ?? 'Tất cả khóa học trên hệ thống' }}">
                                                Áp dụng: <strong class="text-slate-800 dark:text-slate-200">{{ $userCoupon->scope_label ?? 'Tất cả khóa học trên hệ thống' }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expiry Footer -->
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                    <span>
                                        @if($coupon->expires_at)
                                            Hạn dùng: {{ $coupon->expires_at->format('d/m/Y H:i') }}
                                        @else
                                            Hạn dùng: Vĩnh viễn
                                        @endif
                                    </span>
                                    @if($userCoupon->saved_at)
                                        <span class="font-medium text-slate-400">
                                            Lưu: {{ $userCoupon->saved_at->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
