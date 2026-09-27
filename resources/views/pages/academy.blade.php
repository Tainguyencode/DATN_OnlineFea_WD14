@extends('layouts.app')

@section('title', 'FEA Academy - Chương trình đào tạo chuyên sâu | FEA Learning')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-slate-950 py-16 sm:py-20 text-white border-b border-slate-800">
        <div class="pointer-events-none absolute -top-40 right-0 h-96 w-96 rounded-full bg-blue-600/15 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 left-0 h-72 w-72 rounded-full bg-indigo-600/15 blur-3xl"></div>

        <div class="ui-container relative z-10">
            <div class="mb-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-300 hover:text-white transition" aria-label="Quay lại trang chủ">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        FEA Academy
                    </span>

                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl leading-tight">
                        Chương trình đào tạo chuyên sâu chuẩn quốc tế
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                        Được xây dựng bởi đội ngũ chuyên gia công nghệ hàng đầu, FEA Academy mang đến các khóa học chuyên sâu từ lý thuyết nền tảng đến dự án thực chiến chuẩn doanh nghiệp.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('courses.index') }}" class="ui-button-primary px-6 py-3 font-bold shadow-lg shadow-blue-500/25">
                            Khám phá khóa học ngay
                        </a>
                        <a href="{{ route('learning-paths.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-900/80 px-6 py-3 text-sm font-semibold text-slate-200 hover:bg-slate-800 hover:text-white transition">
                            Xem lộ trình học tập
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 sm:p-8 shadow-2xl backdrop-blur">
                    <h3 class="text-lg font-bold text-white">Điểm nổi bật của FEA Academy</h3>
                    <ul class="mt-5 space-y-4 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">✓</span>
                            <span>Giáo trình bám sát yêu cầu tuyển dụng thực tế của doanh nghiệp.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">✓</span>
                            <span>Thực hành trên các bài toán và hệ thống quy mô lớn.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">✓</span>
                            <span>Đội ngũ giảng viên, Mentor hỗ trợ giải đáp 24/7.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs">✓</span>
                            <span>Cấp chứng chỉ hoàn thành có mã định danh trực tuyến.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Pillars --}}
    <section class="py-16 sm:py-20 bg-white dark:bg-slate-950">
        <div class="ui-container">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                    Phương pháp đào tạo khác biệt tại FEA Academy
                </h2>
                <p class="mt-3 text-slate-600 dark:text-slate-400">
                    Kết hợp giữa đào tạo bài bản, thực hành dự án và công nghệ AI đồng hành học tập.
                </p>
            </div>

            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-[#0056D2] dark:bg-blue-900/30 dark:text-blue-400 mb-5">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Project-based Learning</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        80% thời lượng dành cho việc code và xây dựng sản phẩm thực tế, giúp học viên tích lũy kinh nghiệm làm việc ngay trong quá trình học.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 mb-5">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Trợ lý AI Đồng hành</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Hệ thống AI thông minh hỗ trợ giải đáp thắc mắc, phân tích lỗi code và gợi ý lộ trình học tập phù hợp theo tiến độ cá nhân.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 mb-5">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Chứng nhận Năng lực Uy tín</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Chứng chỉ số được xác thực trực tuyến qua mã QR và URL bảo mật, dễ dàng đính kèm vào hồ sơ xin việc hoặc LinkedIn.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Courses Preview --}}
    @if($featuredCourses->isNotEmpty())
        <section class="py-16 bg-slate-50 dark:bg-slate-900/40 border-t border-slate-200 dark:border-slate-800">
            <div class="ui-container">
                <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                            Khóa học tiêu biểu của Academy
                        </h2>
                        <p class="mt-2 text-slate-600 dark:text-slate-400">Khám phá các khóa học chất lượng cao được đánh giá tốt nhất.</p>
                    </div>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-400">
                        <span>Xem toàn bộ khóa học</span>
                        <span>→</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredCourses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
