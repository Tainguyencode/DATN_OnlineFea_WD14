@extends('layouts.app')

@section('title', 'Corporate Training - Giải pháp đào tạo doanh nghiệp | FEA Learning')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-slate-950 py-16 sm:py-20 text-white border-b border-slate-800">
        <div class="pointer-events-none absolute -top-40 right-0 h-96 w-96 rounded-full bg-blue-600/15 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 left-0 h-72 w-72 rounded-full bg-slate-600/15 blur-3xl"></div>

        <div class="ui-container relative z-10">
            <div class="mb-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-300 hover:text-white transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Trang chủ</span>
                </a>
            </div>

            <div class="grid gap-10 lg:grid-cols-[1fr_420px] items-center">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full border border-blue-400/30 bg-blue-500/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-blue-300">
                        <svg class="h-4 w-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Corporate Training
                    </span>

                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl leading-tight">
                        Giải pháp đào tạo & Nâng cao năng lực số cho doanh nghiệp
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                        Tùy biến chương trình đào tạo theo nhu cầu thực tế của từng tổ chức. Cung cấp nền tảng quản lý tiến độ học tập, báo cáo hiệu suất và đánh giá kỹ năng chuyên sâu cho đội ngũ nhân sự.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('register.role', 'instructor') }}" class="ui-button-primary px-6 py-3 font-bold shadow-lg shadow-blue-500/25">
                            Đăng ký đào tạo doanh nghiệp
                        </a>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-900/80 px-6 py-3 text-sm font-semibold text-slate-200 hover:bg-slate-800 hover:text-white transition">
                            Xem danh mục khóa học
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 sm:p-8 shadow-2xl backdrop-blur">
                    <h3 class="text-lg font-bold text-white">Lợi ích cho tổ chức</h3>
                    <ul class="mt-5 space-y-4 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">💼</span>
                            <span>Thiết kế bài giảng theo đúng Tech Stack và văn hóa của doanh nghiệp.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">💼</span>
                            <span>Báo cáo tiến độ và mức độ hoàn thành khóa học theo thời gian thực.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">💼</span>
                            <span>Học mọi lúc mọi nơi trên nền tảng Cloud tối ưu và bảo mật.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">💼</span>
                            <span>Chính sách chiết khấu linh hoạt theo số lượng nhân sự tham gia.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Corporate Solutions --}}
    <section class="py-16 sm:py-20 bg-white dark:bg-slate-950">
        <div class="ui-container">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                    Các gói giải pháp đào tạo toàn diện
                </h2>
                <p class="mt-3 text-slate-600 dark:text-slate-400">
                    Phù hợp cho cả Startup, Doanh nghiệp vừa & nhỏ (SME) và Tập đoàn lớn.
                </p>
            </div>

            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-[#0056D2] dark:bg-blue-900/30 dark:text-blue-400 mb-5">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Onboarding & Fresher Training</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Rút ngắn thời gian hòa nhập của nhân sự mới bằng các lộ trình chuẩn bị sẵn kiến thức nền tảng và quy chuẩn mã nguồn của công ty.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 mb-5">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Upskill & Reskill Công nghệ</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Cập nhật các công nghệ tiên tiến nhất như AI, DevOps, Microservices cho đội ngũ kỹ sư hiện tại nhằm tăng năng suất phát triển sản phẩm.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 mb-5">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Kiểm tra & Đánh giá Kỹ năng</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Bộ câu hỏi trắc nghiệm, bài tập code tự động chấm điểm giúp quản lý đánh giá khách quan trình độ và mức độ tiến bộ của từng nhân sự.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
