@extends('layouts.app')

@section('title', 'Innovation Lab - Thực hành dự án công nghệ mới | FEA Learning')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-slate-950 py-16 sm:py-20 text-white border-b border-slate-800">
        <div class="pointer-events-none absolute -top-40 right-0 h-96 w-96 rounded-full bg-cyan-600/15 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 left-0 h-72 w-72 rounded-full bg-blue-600/15 blur-3xl"></div>

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
                    <span class="inline-flex items-center gap-2 rounded-full border border-cyan-400/30 bg-cyan-500/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-cyan-300">
                        <svg class="h-4 w-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                        Innovation Lab
                    </span>

                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl leading-tight">
                        Không gian ươm mầm & Thực hành dự án công nghệ mới
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                        Nơi sinh viên, lập trình viên và nhà nghiên cứu cùng tham gia vào các dự án công nghệ tiên phong: Generative AI, Cloud-Native, Big Data và kiến trúc microservices hiện đại.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('learning-paths.index') }}" class="ui-button-primary px-6 py-3 font-bold shadow-lg shadow-cyan-500/25">
                            Khám phá dự án & lộ trình
                        </a>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-900/80 px-6 py-3 text-sm font-semibold text-slate-200 hover:bg-slate-800 hover:text-white transition">
                            Xem khóa học công nghệ
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 sm:p-8 shadow-2xl backdrop-blur">
                    <h3 class="text-lg font-bold text-white">Mục tiêu Innovation Lab</h3>
                    <ul class="mt-5 space-y-4 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-cyan-400 font-bold text-xs">🚀</span>
                            <span>Ứng dụng công nghệ mới nhất vào giải quyết bài toán thực tế.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-cyan-400 font-bold text-xs">👥</span>
                            <span>Mô hình làm việc nhóm theo quy trình Agile/Scrum chuyên nghiệp.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-cyan-400 font-bold text-xs">💡</span>
                            <span>Hỗ trợ ươm tạo các ý tưởng Startup và sản phẩm mã nguồn mở.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-cyan-400 font-bold text-xs">🏆</span>
                            <span>Tổ chức các cuộc thi Hackathon và Tech Talk định kỳ.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Tech Tracks --}}
    <section class="py-16 sm:py-20 bg-white dark:bg-slate-950">
        <div class="ui-container">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                    Các hướng nghiên cứu & phát triển trọng tâm
                </h2>
                <p class="mt-3 text-slate-600 dark:text-slate-400">
                    Trang bị kỹ năng giải quyết các thách thức công nghệ trong thời đại số.
                </p>
            </div>

            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="text-3xl mb-4">🤖</div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Generative AI & LLM</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Xây dựng ứng dụng tích hợp RAG, Agent AI, xử lý ngôn ngữ tự nhiên và tự động hóa quy trình thông minh.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="text-3xl mb-4">☁️</div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cloud-Native & DevOps</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Triển khai hệ thống phân tán, CI/CD pipeline tự động, Docker, Kubernetes và giám sát hiệu năng với Prometheus/Grafana.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-7 dark:border-slate-800 dark:bg-slate-900/60 transition hover:shadow-md">
                    <div class="text-3xl mb-4">🛡️</div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Cybersecurity & FinTech</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Nghiên cứu kiến trúc bảo mật ứng dụng, mã hóa dữ liệu, xác thực đa yếu tố và cổng thanh toán trực tuyến an toàn.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
