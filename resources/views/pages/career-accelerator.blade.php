@extends('layouts.app')

@section('title', 'Career Accelerator - Định hướng & Kết nối việc làm | FEA Learning')

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-slate-950 py-16 sm:py-20 text-white border-b border-slate-800">
        <div class="pointer-events-none absolute -top-40 right-0 h-96 w-96 rounded-full bg-amber-600/15 blur-3xl"></div>
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
                    <span class="inline-flex items-center gap-2 rounded-full border border-amber-400/30 bg-amber-500/10 px-3.5 py-1 text-xs font-bold uppercase tracking-wider text-amber-300">
                        <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Career Accelerator
                    </span>

                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl leading-tight">
                        Chương trình bứt phá & Định hướng sự nghiệp công nghệ
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">
                        Cầu nối vững chắc giữa người học và thị trường lao động. Hỗ trợ tối ưu hồ sơ ứng tuyển, luyện phỏng vấn kỹ thuật và kết nối trực tiếp với mạng lưới doanh nghiệp đối tác uy tín.
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('learning-paths.index') }}" class="ui-button-primary px-6 py-3 font-bold shadow-lg shadow-amber-500/25">
                            Xem lộ trình sự nghiệp
                        </a>
                        <a href="{{ route('instructors.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-700 bg-slate-900/80 px-6 py-3 text-sm font-semibold text-slate-200 hover:bg-slate-800 hover:text-white transition">
                            Gặp gỡ chuyên gia Mentor
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-6 sm:p-8 shadow-2xl backdrop-blur">
                    <h3 class="text-lg font-bold text-white">Dịch vụ hỗ trợ nghề nghiệp</h3>
                    <ul class="mt-5 space-y-4 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-amber-400 font-bold text-xs">⭐</span>
                            <span>Tư vấn và chuẩn hóa CV/Portfolio kỹ thuật theo tiêu chuẩn quốc tế.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-amber-400 font-bold text-xs">⭐</span>
                            <span>Mock Interview 1-1 với Tech Lead và chuyên gia nhân sự.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-amber-400 font-bold text-xs">⭐</span>
                            <span>Kết nối cơ hội việc làm và thực tập tại các công ty công nghệ.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-500/20 text-amber-400 font-bold text-xs">⭐</span>
                            <span>Workshop phát triển kỹ năng mềm và tác phong làm việc chuyên nghiệp.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- 4-Step Career Roadmap --}}
    <section class="py-16 sm:py-20 bg-white dark:bg-slate-950">
        <div class="ui-container">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">
                    Lộ trình 4 bước chinh phục nhà tuyển dụng
                </h2>
                <p class="mt-3 text-slate-600 dark:text-slate-400">
                    Được thiết kế bài bản giúp bạn tự tin ứng tuyển vị trí mong muốn.
                </p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 dark:border-slate-800 dark:bg-slate-900/60">
                    <div class="text-xl font-extrabold text-blue-600 dark:text-blue-400 mb-3">01. Đánh giá năng lực</div>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Khảo sát kỹ năng hiện tại, xác định điểm mạnh và lộ trình lấp đầy các khoảng trống kiến thức.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 dark:border-slate-800 dark:bg-slate-900/60">
                    <div class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-3">02. Hoàn thiện Portfolio</div>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Xây dựng ít nhất 2 dự án thực chiến chuẩn chỉnh, deploy sản phẩm thật và tối ưu mã nguồn trên GitHub.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 dark:border-slate-800 dark:bg-slate-900/60">
                    <div class="text-xl font-extrabold text-amber-600 dark:text-amber-400 mb-3">03. Luyện phỏng vấn</div>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Luyện tập giải quyết các câu hỏi thuật toán, kiến trúc hệ thống và trả lời phỏng vấn hành vi.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 dark:border-slate-800 dark:bg-slate-900/60">
                    <div class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 mb-3">04. Kết nối việc làm</div>
                    <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                        Giới thiệu hồ sơ trực tiếp đến các nhà tuyển dụng đối tác và nhận hỗ trợ đàm phán đãi ngộ.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
