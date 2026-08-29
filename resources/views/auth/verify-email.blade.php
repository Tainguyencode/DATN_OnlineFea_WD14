@php
    $studentHub = $studentHub ?? false;
    $currentUser = $user ?? auth()->user();
    $emailVerified = $emailVerified ?? (bool) $currentUser?->hasVerifiedEmail();
    $canUseStudentActions = $studentHub && $emailVerified;
    $isVerificationNotice = ! $studentHub;
    $maskedEmail = $maskedEmail ?? app(\App\Services\EmailVerificationService::class)->maskEmail($currentUser->email);
    $resendAfter = $resendAfter ?? (int) session('resend_after', 0);
@endphp

@extends('layouts.app')

@section('title', ($studentHub ? 'Khu vực học viên' : 'Xác thực email').' - Website học online FEA')

@section('content')
@if($studentHub)
    <div class="bg-slate-50 py-8 dark:bg-slate-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
                            <form method="POST" action="{{ route('verification.send') }}" x-data="{ seconds: {{ $resendAfter }}, loading: false, init() { if (this.seconds > 0) setInterval(() => { if (this.seconds > 0) this.seconds-- }, 1000) } }" x-on:submit="loading = true">
                                @csrf
                                <button type="submit" :disabled="loading || seconds > 0" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#0056D2] px-5 text-sm font-bold text-white transition hover:bg-[#0046B8] disabled:cursor-not-allowed disabled:opacity-60">
                                    <span x-show="seconds === 0 && !loading">Gửi lại mã</span>
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

            @if(app()->environment('local') && ! $emailVerified)
                <div class="mt-6 rounded-2xl border border-blue-200 bg-blue-50/50 p-6 dark:border-blue-900/40 dark:bg-blue-950/20 text-left">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-blue-950 dark:text-blue-200">Hỗ trợ xác thực nhanh</h3>
                            <p class="text-xs text-blue-700/80 dark:text-blue-400/80">Bạn có thể bấm xác thực nhanh tài khoản tại đây để tiếp tục trải nghiệm.</p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <form method="POST" action="{{ route('verification.instant') }}">
                            @csrf
                            <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl bg-blue-600 px-4 text-xs font-bold text-white transition hover:bg-blue-700 shadow-sm">
                                Xác thực nhanh tài khoản này
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <nav class="mt-6 flex gap-2 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-2 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                @foreach([
                    ['href' => route('student.dashboard'), 'label' => 'Tổng quan', 'active' => true],
                    ['href' => route('student.courses'), 'label' => 'Khóa học'],
                    ['href' => route('student.recently-viewed.index'), 'label' => 'Đã xem gần đây'],
                    ['href' => route('student.cart'), 'label' => 'Giỏ hàng'],
                    ['href' => route('favorites.index'), 'label' => 'Yêu thích'],
                    ['href' => route('student.certificates'), 'label' => 'Chứng chỉ'],
                    ['href' => route('student.orders'), 'label' => 'Đơn hàng'],
                    ['href' => route('student.vouchers.index'), 'label' => 'Kho Voucher'],
                    ['href' => route('study-groups.index'), 'label' => 'Nhóm học tập'],
                    ['href' => route('student.profile'), 'label' => 'Hồ sơ'],
                ] as $item)
                    <a href="{{ $item['href'] }}" class="whitespace-nowrap rounded-xl px-4 py-2 text-sm font-bold transition {{ ($item['active'] ?? false) ? 'bg-blue-50 text-[#0056D2] dark:bg-slate-800 dark:text-blue-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
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

                <div class="mt-8">
                    <h2 class="text-xl font-bold text-slate-950 dark:text-white mb-4">Lối tắt học tập</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('student.courses') }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] transition group-hover:bg-[#0056D2] group-hover:text-white dark:bg-blue-950/40 dark:text-blue-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-slate-900 transition group-hover:text-[#0056D2] dark:text-white dark:group-hover:text-blue-300">Khóa học của tôi</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Xem {{ $stats['enrolled'] }} khóa học và tiếp tục bài học đang dang dở</p>
                            </div>
                        </a>

                        <a href="{{ route('student.recently-viewed.index') }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition group-hover:bg-indigo-600 group-hover:text-white dark:bg-indigo-950/40 dark:text-indigo-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-slate-900 transition group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-300">Đã xem gần đây</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Xem lại các khóa học bạn vừa truy cập</p>
                            </div>
                        </a>

                        <a href="{{ route('favorites.index') }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-600 transition group-hover:bg-rose-600 group-hover:text-white dark:bg-rose-950/40 dark:text-rose-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-slate-900 transition group-hover:text-rose-600 dark:text-white dark:group-hover:text-rose-300">Khóa học yêu thích</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $stats['wishlist'] }} khóa học bạn đã đánh dấu lưu</p>
                            </div>
                        </a>

                        <a href="{{ route('student.certificates') }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:bg-amber-600 group-hover:text-white dark:bg-amber-950/40 dark:text-amber-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-slate-900 transition group-hover:text-amber-600 dark:text-white dark:group-hover:text-amber-300">Chứng chỉ</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $stats['certificates'] }} chứng chỉ hoàn thành đã được cấp</p>
                            </div>
                        </a>

                        <a href="{{ route('student.orders') }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 transition group-hover:bg-emerald-600 group-hover:text-white dark:bg-emerald-950/40 dark:text-emerald-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-slate-900 transition group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-300">Đơn hàng & Giao dịch</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Theo dõi trạng thái và lịch sử thanh toán</p>
                            </div>
                        </a>

                        <a href="{{ route('student.vouchers.index') }}" class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600 transition group-hover:bg-purple-600 group-hover:text-white dark:bg-purple-950/40 dark:text-purple-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-extrabold text-slate-900 transition group-hover:text-purple-600 dark:text-white dark:group-hover:text-purple-300">Kho Voucher</h3>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Các mã ưu đãi giảm giá dành riêng cho bạn</p>
                            </div>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@else
    <div class="bg-white dark:bg-slate-950">
        <div class="flex min-h-[calc(100vh-16rem)] items-center justify-center px-4 py-12">
            <div
                x-data="{
                    seconds: {{ $resendAfter }},
                    loading: false,
                    code: '',
                    init() {
                        if (this.seconds > 0) {
                            setInterval(() => { if (this.seconds > 0) this.seconds-- }, 1000);
                        }
                        this.$nextTick(() => this.$refs.codeInput?.focus());
                    },
                    handleInput(event) {
                        this.code = event.target.value.replace(/\D/g, '').slice(0, 6);
                        event.target.value = this.code;
                    },
                    handlePaste(event) {
                        event.preventDefault();
                        const pasted = (event.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                        this.code = pasted;
                        this.$refs.codeInput.value = pasted;
                    }
                }"
                class="ui-card w-full max-w-2xl p-8"
            >
                <div class="text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>

                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-950 dark:text-white">Xác thực email</h1>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Chúng tôi đã gửi mã gồm 6 chữ số tới <strong>{{ $maskedEmail }}</strong>.
                        Mã có hiệu lực trong <strong>10 phút</strong>.
                    </p>
                    <p class="mx-auto mt-2 max-w-xl text-xs text-slate-500 dark:text-slate-400">
                        Sau khi xác thực, bạn sẽ được chuyển vào khu vực
                        @if($currentUser->isAdmin())
                            quản trị
                        @elseif($currentUser->isInstructor())
                            giảng viên
                        @elseif($currentUser->isStudent())
                            học viên
                        @else
                            học viên
                        @endif
                        tương ứng.
                    </p>
                </div>

                @if($errors->any())
                    <div class="ui-alert-error mx-auto mt-6 max-w-lg">
                        <ul class="space-y-1 text-left">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.code.verify') }}" class="mx-auto mt-8 max-w-md space-y-5" x-on:submit="loading = true">
                    @csrf
                    <div>
                        <label for="verification-code" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200">Mã xác thực 6 chữ số</label>
                        <input
                            id="verification-code"
                            x-ref="codeInput"
                            type="text"
                            name="code"
                            x-model="code"
                            x-on:input="handleInput($event)"
                            x-on:paste="handlePaste($event)"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            class="ui-input w-full text-center text-2xl font-bold tracking-[0.5em]"
                            placeholder="000000"
                        >
                    </div>

                    <button type="submit" :disabled="loading || code.length !== 6" class="ui-button-primary w-full disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-show="!loading">Xác thực</span>
                        <span x-show="loading">Đang xác thực...</span>
                    </button>
                </form>

                <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                    <form method="POST" action="{{ route('verification.send') }}" x-on:submit="loading = true">
                        @csrf
                        <button type="submit" :disabled="loading || seconds > 0" class="ui-button-primary">
                            <span x-show="seconds === 0 && !loading">Gửi lại mã</span>
                            <span x-show="loading">Đang gửi...</span>
                            <span x-show="seconds > 0">Gửi lại sau <span x-text="seconds"></span>s</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="ui-button-secondary">Đăng xuất</button>
                    </form>
                </div>

                @if(app()->environment('local'))
                    <div class="mt-8 rounded-xl border border-blue-200 bg-blue-50/50 p-6 dark:border-blue-900/40 dark:bg-blue-950/20 text-left">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </span>
                            <div>
                                <h3 class="text-sm font-bold text-blue-950 dark:text-blue-200">Hỗ trợ xác thực nhanh</h3>
                                <p class="text-xs text-blue-700/80 dark:text-blue-400/80">Bạn có thể kích hoạt nhanh email tại đây để tiếp tục trải nghiệm.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <form method="POST" action="{{ route('verification.instant') }}">
                                @csrf
                                <button type="submit" class="inline-flex h-9 items-center justify-center rounded-xl bg-blue-600 px-4 text-xs font-bold text-white transition hover:bg-blue-700 shadow-sm">
                                    Xác thực nhanh tài khoản
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
@endsection
