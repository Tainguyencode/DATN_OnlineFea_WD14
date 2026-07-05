@php
    $studentHub = $studentHub ?? false;
    $currentUser = $user ?? auth()->user();
    $emailVerified = $emailVerified ?? (bool) $currentUser?->hasVerifiedEmail();
    $canUseStudentActions = $studentHub && $emailVerified;
@endphp

@extends('layouts.app')

@section('title', ($studentHub ? 'Khu vực học viên' : 'Xác thực email').' - Website học online FEA')

@section('content')
@if($studentHub)
    <div class="bg-slate-50 py-8 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="grid gap-6 p-6 lg:grid-cols-[1fr_auto] lg:items-center lg:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <img src="{{ $currentUser->avatarUrl() }}" alt="{{ $currentUser->name }}" class="h-20 w-20 rounded-2xl object-cover ring-4 ring-slate-100 dark:ring-slate-800">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-2xl font-extrabold text-slate-950 dark:text-white sm:text-3xl">Xin chào, {{ $currentUser->name }}</h1>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $emailVerified ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-200' }}">
                                    {{ $emailVerified ? 'Đã xác thực email' : 'Chưa xác thực email' }}
                                </span>
                            </div>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $currentUser->email }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row lg:justify-end">
                        @unless($emailVerified)
                            <form method="POST" action="{{ route('verification.send') }}" x-data="{ seconds: {{ session('resend_after', 0) }}, loading: false, init() { if (this.seconds > 0) setInterval(() => { if (this.seconds > 0) this.seconds-- }, 1000) } }" x-on:submit="loading = true">
                                @csrf
                                <button type="submit" :disabled="loading || seconds > 0" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#0056D2] px-5 text-sm font-bold text-white transition hover:bg-[#0046B8] disabled:cursor-not-allowed disabled:opacity-60">
                                    <span x-show="seconds === 0 && !loading">Gửi lại email</span>
                                    <span x-show="loading">Đang gửi...</span>
                                    <span x-show="seconds > 0">Gửi lại sau <span x-text="seconds"></span>s</span>
                                </button>
                            </form>
                        @endunless

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-bold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>

                @unless($emailVerified)
                    <div class="border-t border-amber-200 bg-amber-50 px-6 py-4 text-sm font-medium text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-200 lg:px-8">
                        Tài khoản cần xác thực email để dùng các thao tác lưu dữ liệu như thanh toán, cập nhật hồ sơ và quản lý yêu thích.
                    </div>
                @endunless
            </section>

            <nav class="mt-6 flex gap-2 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                @foreach([
                    ['href' => '#overview', 'label' => 'Tổng quan'],
                    ['href' => '#courses', 'label' => 'Khóa học'],
                    ['href' => '#cart', 'label' => 'Giỏ hàng'],
                    ['href' => '#wishlist', 'label' => 'Yêu thích'],
                    ['href' => '#certificates', 'label' => 'Chứng chỉ'],
                    ['href' => '#orders', 'label' => 'Đơn hàng'],
                    ['href' => '#profile', 'label' => 'Hồ sơ'],
                ] as $item)
                    <a href="{{ $item['href'] }}" class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <section id="overview" class="scroll-mt-24 pt-8">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach([
                        ['label' => 'Khóa đã đăng ký', 'value' => $stats['enrolled'], 'tone' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-200'],
                        ['label' => 'Đang học', 'value' => $stats['in_progress'], 'tone' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-200'],
                        ['label' => 'Hoàn thành', 'value' => $stats['completed'], 'tone' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-200'],
                        ['label' => 'Chứng chỉ', 'value' => $stats['certificates'], 'tone' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/40 dark:text-violet-200'],
                    ] as $stat)
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</p>
                            <div class="mt-4 flex items-end justify-between">
                                <p class="text-3xl font-extrabold text-slate-950 dark:text-white">{{ $stat['value'] }}</p>
                                <span class="rounded-xl px-3 py-2 text-xs font-bold {{ $stat['tone'] }}">FEA</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-950 dark:text-white">Tiến độ học tập trung bình</h2>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ number_format($avgProgress, 0) }}% trên các khóa học đang hoạt động</p>
                        </div>
                        <span class="text-4xl font-extrabold text-[#0056D2] dark:text-blue-300">{{ number_format($avgProgress, 0) }}%</span>
                    </div>
                    <div class="mt-5 h-3 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-full rounded-full bg-[#0056D2]" style="width: {{ min(100, $avgProgress) }}%"></div>
                    </div>
                </div>
            </section>

            <section id="courses" class="scroll-mt-24 pt-8">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Khóa học của tôi</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $courseEnrollments->count() }} khóa học gần nhất</p>
                    </div>
                    <a href="{{ route('courses.index') }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Khám phá thêm</a>
                </div>

                @if($courseEnrollments->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                        Bạn chưa đăng ký khóa học nào.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($courseEnrollments as $enrollment)
                            @php
                                $course = $enrollment->course;
                                $progress = (float) ($enrollment->progress_percent ?? 0);
                            @endphp
                            @continue(! $course)

                            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                                <a href="{{ route('courses.show', $course->slug) }}" class="block aspect-video overflow-hidden bg-slate-900">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-4xl font-extrabold text-white/70">FEA</div>
                                    @endif
                                </a>
                                <div class="p-5">
                                    <div class="flex items-center justify-between gap-3">
                                        <span class="truncate text-xs font-bold uppercase text-[#0056D2] dark:text-blue-300">{{ $course->category?->name ?? 'Khóa học' }}</span>
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-200 dark:ring-emerald-900">Đang học</span>
                                    </div>
                                    <h3 class="mt-3 line-clamp-2 text-lg font-extrabold leading-snug text-slate-950 dark:text-white">{{ $course->title }}</h3>
                                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Giảng viên: {{ $course->instructor?->name ?? 'FEA Instructor' }}</p>
                                    <div class="mt-5">
                                        <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                                            <span>Tiến độ</span>
                                            <span>{{ number_format($progress, 0) }}%</span>
                                        </div>
                                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                            <div class="h-full rounded-full bg-[#0056D2]" style="width: {{ min(100, $progress) }}%"></div>
                                        </div>
                                    </div>
                                    <a href="{{ route('courses.show', $course->slug) }}" class="mt-5 flex h-11 w-full items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white transition hover:bg-[#0056D2] dark:bg-white dark:text-slate-950 dark:hover:bg-blue-100">
                                        Tiếp tục học
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section id="cart" class="scroll-mt-24 pt-8">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Giỏ hàng</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $stats['cart_items'] }} khóa học trong giỏ</p>
                    </div>
                </div>

                @if($cart->items->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                        Giỏ hàng trống.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <div class="space-y-4 lg:col-span-2">
                            @foreach($cart->items as $item)
                                @php
                                    $course = $item->course;
                                    $price = $course ? ($course->discount_price ?? $course->sale_price ?? $course->price) : 0;
                                @endphp
                                @continue(! $course)

                                <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-blue-50 font-bold text-[#0056D2] dark:bg-blue-950/40 dark:text-blue-200">
                                        {{ strtoupper(substr($course->title, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="truncate font-bold text-slate-950 dark:text-white">{{ $course->title }}</h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $course->instructor?->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-extrabold text-[#0056D2] dark:text-blue-300">{{ number_format($price, 0, ',', '.') }}đ</p>
                                        <form method="POST" action="{{ route('student.cart.remove', $course->id) }}" class="mt-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" @if(! $canUseStudentActions) disabled @endif class="text-xs font-semibold text-rose-600 hover:underline disabled:cursor-not-allowed disabled:opacity-50">Xóa</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="h-fit rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <h3 class="text-lg font-extrabold text-slate-950 dark:text-white">Thanh toán</h3>
                            <div class="mt-5 space-y-3 text-sm">
                                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                    <span>Tạm tính</span>
                                    <span>{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
                                </div>
                                <div class="flex justify-between border-t border-slate-200 pt-4 text-lg font-extrabold text-slate-950 dark:border-slate-800 dark:text-white">
                                    <span>Tổng cộng</span>
                                    <span class="text-[#0056D2] dark:text-blue-300">{{ number_format($cartTotal, 0, ',', '.') }}đ</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('student.cart.checkout') }}" class="mt-6 space-y-4">
                                @csrf
                                <input type="text" name="coupon_code" placeholder="Mã giảm giá" @if(! $canUseStudentActions) disabled @endif class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2] disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:disabled:bg-slate-800">
                                <select name="payment_method" required @if(! $canUseStudentActions) disabled @endif class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2] disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:disabled:bg-slate-800">
                                    <option value="vnpay">VNPay</option>
                                    <option value="momo">MoMo</option>
                                    <option value="bank_transfer">Chuyển khoản</option>
                                </select>
                                <button type="submit" @if(! $canUseStudentActions) disabled @endif class="h-11 w-full rounded-xl bg-[#0056D2] text-sm font-bold text-white transition hover:bg-[#0046B8] disabled:cursor-not-allowed disabled:opacity-60">
                                    Thanh toán ngay
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </section>

            <section id="wishlist" class="scroll-mt-24 pt-8">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Khóa học yêu thích</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $stats['wishlist'] }} khóa học đã lưu</p>
                    </div>
                </div>

                @if($wishlistItems->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                        Chưa có khóa học yêu thích.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($wishlistItems as $item)
                            @php $course = $item->course; @endphp
                            @continue(! $course)

                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                                <span class="text-xs font-bold uppercase text-[#0056D2] dark:text-blue-300">{{ $course->category?->name ?? 'Khóa học' }}</span>
                                <h3 class="mt-2 line-clamp-2 text-lg font-extrabold text-slate-950 dark:text-white">{{ $course->title }}</h3>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $course->instructor?->name ?? 'FEA Instructor' }}</p>
                                <div class="mt-5 flex gap-3">
                                    <a href="{{ route('courses.show', $course->slug) }}" class="flex h-10 flex-1 items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white transition hover:bg-[#0056D2] dark:bg-white dark:text-slate-950 dark:hover:bg-blue-100">Chi tiết</a>
                                    <form method="POST" action="{{ route('student.wishlist.toggle', $course->id) }}">
                                        @csrf
                                        <button type="submit" @if(! $canUseStudentActions) disabled @endif class="h-10 rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">Bỏ lưu</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section id="certificates" class="scroll-mt-24 pt-8">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Chứng chỉ</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Các chứng chỉ đã được cấp</p>
                    </div>
                </div>

                @if($certificates->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-500 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400">
                        Hoàn thành khóa học để nhận chứng chỉ.
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        @foreach($certificates as $cert)
                            <article class="relative overflow-hidden rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-900/60 dark:bg-amber-950/30">
                                <span class="text-xs font-extrabold uppercase text-amber-700 dark:text-amber-200">Chứng chỉ hoàn thành</span>
                                <h3 class="mt-2 text-xl font-extrabold text-slate-950 dark:text-white">{{ $cert->course?->title }}</h3>
                                <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Mã: <span class="font-mono font-bold">{{ $cert->certificate_code }}</span></p>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Cấp ngày: {{ $cert->issued_at?->format('d/m/Y') }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section id="orders" class="scroll-mt-24 pt-8">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Đơn hàng</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $stats['orders'] }} giao dịch đã tạo</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    @if($orders->isEmpty())
                        <div class="p-12 text-center text-slate-500 dark:text-slate-400">Chưa có đơn hàng nào.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-950">
                                    <tr>
                                        <th class="px-5 py-4 text-left font-bold text-slate-600 dark:text-slate-300">Mã đơn</th>
                                        <th class="px-5 py-4 text-left font-bold text-slate-600 dark:text-slate-300">Khóa học</th>
                                        <th class="px-5 py-4 text-left font-bold text-slate-600 dark:text-slate-300">Tổng tiền</th>
                                        <th class="px-5 py-4 text-left font-bold text-slate-600 dark:text-slate-300">Trạng thái</th>
                                        <th class="px-5 py-4 text-left font-bold text-slate-600 dark:text-slate-300">Ngày</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($orders as $order)
                                        @php
                                            $orderTitles = collect($order->items ?? [])->pluck('title')->filter()->join(', ');
                                        @endphp
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                            <td class="px-5 py-4 font-mono font-bold text-[#0056D2] dark:text-blue-300">{{ $order->order_code }}</td>
                                            <td class="px-5 py-4 text-slate-700 dark:text-slate-200">{{ $orderTitles ?: 'Khóa học' }}</td>
                                            <td class="px-5 py-4 font-bold text-slate-950 dark:text-white">{{ number_format($order->total_amount, 0, ',', '.') }}đ</td>
                                            <td class="px-5 py-4">
                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $order->status === 'paid' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-950/50 dark:text-amber-200' }}">
                                                    {{ $order->status === 'paid' ? 'Đã thanh toán' : ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ $order->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>

            <section id="profile" class="scroll-mt-24 pt-8">
                <div class="mb-4">
                    <h2 class="text-xl font-extrabold text-slate-950 dark:text-white">Hồ sơ</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Thông tin tài khoản học viên</p>
                </div>

                <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Họ tên</label>
                            <input id="name" name="name" value="{{ old('name', $currentUser->name) }}" required @if(! $canUseStudentActions) disabled @endif class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2] disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:disabled:bg-slate-800">
                        </div>
                        <div>
                            <label for="username" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Tên đăng nhập</label>
                            <input id="username" name="username" value="{{ old('username', $currentUser->username) }}" required @if(! $canUseStudentActions) disabled @endif class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2] disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:disabled:bg-slate-800">
                        </div>
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Số điện thoại</label>
                            <input id="phone" name="phone" value="{{ old('phone', $currentUser->phone) }}" @if(! $canUseStudentActions) disabled @endif class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2] disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:disabled:bg-slate-800">
                        </div>
                        <div>
                            <label for="avatar" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Ảnh đại diện</label>
                            <input id="avatar" type="file" name="avatar" accept="image/*" @if(! $canUseStudentActions) disabled @endif class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none file:mr-4 file:rounded-lg file:border-0 file:bg-slate-950 file:px-3 file:py-1.5 file:text-sm file:font-bold file:text-white disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:file:bg-white dark:file:text-slate-950 dark:disabled:bg-slate-800">
                        </div>
                        <div class="md:col-span-2">
                            <label for="bio" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Giới thiệu</label>
                            <textarea id="bio" name="bio" rows="4" @if(! $canUseStudentActions) disabled @endif class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#0056D2] disabled:bg-slate-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:disabled:bg-slate-800">{{ old('bio', $currentUser->bio) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" @if(! $canUseStudentActions) disabled @endif class="h-11 rounded-xl bg-[#0056D2] px-6 text-sm font-bold text-white transition hover:bg-[#0046B8] disabled:cursor-not-allowed disabled:opacity-60">
                            Lưu hồ sơ
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@else
    <div class="bg-white dark:bg-slate-950">
        <div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-4 py-12">
            <div x-data="{ seconds: {{ session('resend_after', 0) }}, loading: false, init() { if (this.seconds > 0) setInterval(() => { if (this.seconds > 0) this.seconds-- }, 1000) } }" class="ui-card w-full max-w-2xl p-8 text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                    <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>

                <div class="mx-auto mb-6 h-2 max-w-sm overflow-hidden rounded-full bg-slate-200 dark:bg-white/10">
                    <div class="h-full w-2/3 rounded-full bg-emerald-500"></div>
                </div>

                <h1 class="text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white">Kiểm tra email của bạn</h1>
                <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                    Chúng tôi đã gửi liên kết xác thực tới <strong>{{ $currentUser->email }}</strong>. Sau khi xác thực, bạn sẽ được chuyển vào dashboard tương ứng.
                </p>

                @if(session('success'))
                    <div class="ui-alert-success mx-auto mt-6 max-w-lg">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <form method="POST" action="{{ route('verification.send') }}" x-on:submit="loading = true">
                        @csrf
                        <button type="submit" :disabled="loading || seconds > 0" class="ui-button-primary">
                            <span x-show="seconds === 0 && !loading">Gửi lại email</span>
                            <span x-show="loading">Đang gửi...</span>
                            <span x-show="seconds > 0">Gửi lại sau <span x-text="seconds"></span>s</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="ui-button-secondary">Đăng xuất</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
