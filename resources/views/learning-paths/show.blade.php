@extends('layouts.app')

@section('title', $learningPath->title . ' - FEA Learning')

@section('content')
    @php
        $skillsList = is_array($learningPath->skills) ? $learningPath->skills : (json_decode($learningPath->skills ?? '[]', true) ?: []);
        $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
    @endphp

    {{-- Hero Section (Matching site-wide Dark Slate-950 banner) --}}
    <section class="bg-slate-950 text-white relative overflow-hidden border-b border-slate-800 py-12 lg:py-16">
        <div class="pointer-events-none absolute -top-40 right-0 h-96 w-96 rounded-full bg-indigo-600/10 blur-3xl"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="mb-4">
                <button type="button" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('learning-paths.index') }}'; }" class="inline-flex items-center gap-2 text-sm sm:text-base font-bold text-indigo-300 hover:text-white cursor-pointer transition py-1">
                    ← Quay lại
                </button>
            </div>

            {{-- Breadcrumb --}}
            <nav class="mb-5 flex items-center gap-2 text-xs font-semibold text-slate-400">
                <a href="{{ route('home') }}" class="hover:text-white transition">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('learning-paths.index') }}" class="hover:text-white transition">Lộ trình học tập</a>
                <span>/</span>
                <span class="text-indigo-300 truncate max-w-xs">{{ $learningPath->title }}</span>
            </nav>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_360px] lg:items-center">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $levelLabel = match($learningPath->level) {
                                'beginner' => '🌱 Cơ bản (Beginner)',
                                'intermediate' => '⚡ Trung cấp (Intermediate)',
                                'advanced' => '🚀 Nâng cao (Advanced)',
                                default => 'Mọi trình độ'
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-full border border-indigo-400/30 bg-indigo-500/10 px-3 py-1 text-xs font-bold text-indigo-200">
                            {{ $levelLabel }}
                        </span>
                        @if($learningPath->target_role)
                            <span class="inline-flex items-center gap-1 rounded-full border border-blue-400/30 bg-blue-500/10 px-3 py-1 text-xs font-bold text-blue-200">
                                🎯 Vị trí: {{ $learningPath->target_role }}
                            </span>
                        @endif
                    </div>


                    <h1 class="text-3xl font-extrabold text-white sm:text-4xl lg:text-5xl leading-tight">
                        {{ $learningPath->title }}
                    </h1>

                    <p class="text-sm leading-relaxed text-slate-300 sm:text-base max-w-2xl">
                        {{ $learningPath->description ?? 'Lộ trình bài bản giúp bạn chinh phục từng kỹ năng chuyên môn từ cơ bản đến chuyên sâu.' }}
                    </p>

                    {{-- Key Metrics Pills --}}
                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-300 pt-2">
                        <div class="flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5">
                            <svg class="h-4 w-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>{{ $learningPath->courses->count() }} môn học</span>
                        </div>
                        <div class="flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5">
                            <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $learningPath->courses->sum('lessons_count') }} bài học</span>
                        </div>
                        @if($learningPath->estimated_duration)
                            <div class="flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5">
                                <svg class="h-4 w-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $learningPath->estimated_duration }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Skill Badges Grid --}}
                    @if(!empty($skillsList))
                        <div class="pt-2">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Kỹ năng đạt được:</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($skillsList as $skill)
                                    <span class="rounded-lg bg-indigo-500/10 border border-indigo-400/20 px-2.5 py-1 text-xs font-semibold text-indigo-200">
                                        ✓ {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- User Personal Progress Card --}}
                <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow-2xl backdrop-blur space-y-4">
                    @auth
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-300">Tiến độ cá nhân</span>
                            <span class="rounded-full bg-indigo-500/20 px-2.5 py-0.5 text-xs font-bold text-indigo-200 border border-indigo-400/30">
                                {{ $completedCoursesCount }}/{{ $learningPath->courses->count() }} Môn
                            </span>
                        </div>
                        <div class="flex items-baseline justify-between">
                            <span class="text-3xl font-extrabold text-white">{{ $overallProgress }}%</span>
                            <span class="text-xs text-slate-300 font-semibold">Tỷ lệ hoàn thành</span>
                        </div>
                        <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-900/80 p-0.5 border border-white/10">
                            <div class="h-full bg-gradient-to-r from-indigo-500 via-blue-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ $overallProgress }}%"></div>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed font-medium">
                            @if($overallProgress >= 100)
                                🎉 Chúc mừng! Bạn đã hoàn thành toàn bộ lộ trình này!
                            @elseif($overallProgress > 0)
                                💪 Tiếp tục hoàn thành bài học tiếp theo nhé.
                            @else
                                🚀 Chọn môn học đầu tiên để bắt đầu ngay!
                            @endif
                        </p>
                    @else
                        <div class="text-center py-2 space-y-3">
                            <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-300 ring-1 ring-indigo-400/30">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-white">Đăng nhập để theo dõi tiến độ</h3>
                            <p class="text-xs text-slate-300">Lưu tiến độ bài học, đánh giá phần trăm hoàn thành từng giai đoạn.</p>
                            <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-indigo-700 shadow-lg">
                                Đăng nhập tài khoản
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Section: Detailed Description & Career Highlights --}}
    <section class="border-b border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-[#161615]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-4">
                    <div class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
                        📋 Tổng quan chi tiết
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white">
                        Mô Tả Chi Tiết Lộ Trình & Giá Trị Đầu Ra
                    </h2>
                    <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                        <p class="text-base font-medium leading-relaxed">
                            {{ $learningPath->description }}
                        </p>
                    </div>
                </div>

                {{-- Highlights Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">
                    <div class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 font-bold dark:bg-indigo-950 dark:text-indigo-300">
                            🎓
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-950 dark:text-white">Chứng chỉ hoàn thành</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Nhận chứng chỉ định danh có giá trị khẳng định năng lực sau khi hoàn thành tất cả môn học.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-bold dark:bg-emerald-950 dark:text-emerald-300">
                            ⚡
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-950 dark:text-white">Học theo tốc độ cá nhân</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Quyền truy cập trọn đời, tự do sắp xếp lịch học phù hợp với công việc cá nhân.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700 font-bold dark:bg-blue-950 dark:text-blue-300">
                            💻
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-950 dark:text-white">Đồ án thực chiến</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Thực hành xây dựng sản phẩm thật, tích lũy kinh nghiệm làm việc và Portfolio chuyên nghiệp.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 font-bold dark:bg-amber-950 dark:text-amber-300">
                            🎯
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-950 dark:text-white">Định hướng tuyển dụng</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Hỗ trợ viết CV chuẩn ngành, luyện tập câu hỏi phỏng vấn kỹ thuật trực tiếp.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stage-Based Visual Roadmap Section --}}
    <section class="bg-slate-50 py-12 dark:bg-[#0a0a0a]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 text-center">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Roadmap Tree</span>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-950 dark:text-white sm:text-3xl">
                    Cấu Trúc Các Giai Đoạn Đào Tạo
                </h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-xl mx-auto">
                    Mỗi giai đoạn tập trung vào từng khối kiến thức cụ thể, sắp xếp theo logic từ cơ bản tới ứng dụng thực chiến.
                </p>
            </div>

            @if($learningPath->courses->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-800 dark:bg-[#161615]">
                    <p class="text-base font-bold text-slate-800 dark:text-slate-200">Lộ trình này hiện đang cập nhật môn học.</p>
                </div>
            @else
                <div class="space-y-8 max-w-4xl mx-auto">
                    @php $stepGlobalCounter = 1; @endphp
                    @foreach($groupedCourses as $stageName => $coursesInStage)
                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#161615]">
                            {{-- Stage Header --}}
                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50/70 p-5 dark:border-slate-800 dark:bg-slate-900/60">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white font-extrabold text-sm shadow-md">
                                        {{ $loop->iteration }}
                                    </div>
                                    <div>
                                        <h3 class="text-base font-extrabold text-slate-950 dark:text-white">{{ $stageName }}</h3>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $coursesInStage->count() }} môn học trong giai đoạn này</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Courses in Stage --}}
                            <div class="p-5 space-y-4">
                                @foreach($coursesInStage as $course)
                                    @php
                                        $enrollment = $userEnrollments->get($course->id);
                                        $isEnrolled = $enrollment !== null;
                                        $progress = $enrollment ? (float) $enrollment->progress_percent : 0;
                                        $isCompleted = $progress >= 100 || ($enrollment && $enrollment->completed_at !== null);
                                        $currentStepNumber = $stepGlobalCounter++;
                                    @endphp

                                    <div x-data="{ openPreview: false }" class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition duration-200 hover:border-indigo-300 hover:bg-white hover:shadow-md dark:border-slate-800 dark:bg-slate-900/40 dark:hover:bg-slate-900">
                                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg font-extrabold text-xs shadow-sm
                                                    {{ $isCompleted ? 'bg-emerald-600 text-white' : ($isEnrolled ? 'bg-amber-500 text-white' : 'bg-indigo-600 text-white') }}">
                                                    @if($isCompleted)
                                                        ✓
                                                    @else
                                                        Bước {{ $currentStepNumber }}
                                                    @endif
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                                        @if($course->category)
                                                            <span class="rounded-md bg-indigo-50 border border-indigo-100 px-2 py-0.5 text-[11px] font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300 dark:border-indigo-800">
                                                                {{ $course->category->name }}
                                                            </span>
                                                        @endif

                                                        @if($isCompleted)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                                                ✔ Đã hoàn thành
                                                            </span>
                                                        @elseif($isEnrolled)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                                                ⚡ Đang học ({{ round($progress) }}%)
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-200/70 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                                                Chưa đăng ký
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <h4 class="text-base font-extrabold text-slate-950 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-300 transition">
                                                        <a href="{{ route('courses.show', $course->slug) }}">
                                                            {{ $course->title }}
                                                        </a>
                                                    </h4>

                                                    <div class="mt-1.5 flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                                                        <div class="flex items-center gap-1.5">
                                                            <img src="{{ $course->instructor?->avatarUrl() }}" alt="{{ $course->instructor?->name }}" class="h-4 w-4 rounded-full object-cover">
                                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $course->instructor?->name ?? 'Giảng viên' }}</span>
                                                        </div>
                                                        <span>•</span>
                                                        <div class="flex items-center gap-1">
                                                            <svg class="h-3.5 w-3.5 text-amber-400 fill-amber-400" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($course->rating_avg, 1) }}</span>
                                                        </div>
                                                        <span>•</span>
                                                        <span>{{ $course->lessons_count }} bài học</span>

                                                        @php $curriculumSections = $course->curriculumSections(); @endphp
                                                        @if($curriculumSections->isNotEmpty())
                                                            <span>•</span>
                                                            <button type="button" @click="openPreview = !openPreview" class="font-bold text-indigo-600 hover:underline dark:text-indigo-400 flex items-center gap-1 cursor-pointer">
                                                                <span x-text="openPreview ? 'Ẩn xem trước' : 'Xem nội dung môn học'">Xem nội dung môn học</span>
                                                                <svg class="h-3.5 w-3.5 transition-transform" :class="openPreview ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Action CTA --}}
                                            <div class="shrink-0 flex items-center gap-2">
                                                @if($isEnrolled)
                                                    <a href="{{ route('courses.show', $course->slug) }}" class="h-10 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white shadow-md transition hover:bg-indigo-700">
                                                        {{ $isCompleted ? 'Xem lại' : 'Tiếp tục học' }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('courses.show', $course->slug) }}" class="h-10 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                                        Xem môn học
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Lesson Accordion Preview --}}
                                        @if($curriculumSections->isNotEmpty())
                                            <div x-show="openPreview" x-cloak x-transition class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800 space-y-3">
                                                <div class="text-xs font-bold text-slate-700 dark:text-slate-300">Cấu trúc bài học trong môn này:</div>
                                                <div class="space-y-2">
                                                    @foreach($curriculumSections as $section)
                                                        <div class="rounded-xl bg-white border border-slate-200 p-3 dark:bg-slate-800/80 dark:border-slate-700 text-xs shadow-sm">
                                                            <div class="font-bold text-slate-950 dark:text-white mb-1.5 flex items-center justify-between">
                                                                <span>📁 {{ $section->title }}</span>
                                                                <span class="text-[11px] text-slate-400 font-semibold">{{ $section->lessons->count() }} bài</span>
                                                            </div>
                                                            <ul class="space-y-1 pl-4 text-slate-600 dark:text-slate-400">
                                                                @foreach($section->lessons->take(4) as $lesson)
                                                                    <li class="flex items-center gap-2">
                                                                        <span class="text-indigo-600">•</span>
                                                                        <span class="truncate font-medium">{{ $lesson->title }}</span>
                                                                    </li>
                                                                @endforeach
                                                                @if($section->lessons->count() > 4)
                                                                    <li class="text-[11px] text-slate-400 font-semibold italic pl-3">
                                                                        + và {{ $section->lessons->count() - 4 }} bài học khác...
                                                                    </li>
                                                                @endif
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Section: Suggested & Complementary Courses (Chỉ gợi ý khóa học hiện có trên website) --}}
    @if(isset($relatedCourses) && $relatedCourses->isNotEmpty())
        <section class="border-t border-slate-200 bg-white py-12 dark:border-slate-800 dark:bg-[#161615]">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Gợi ý từ FEA Learning</span>
                        <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white">
                            Khóa Học Liên Quan Đang Mở Trên Website
                        </h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Các khóa học thực tế đã xuất bản trên hệ thống FEA Learning giúp bạn nâng cao tư duy thực chiến và mở rộng kỹ năng bổ trợ.
                        </p>
                    </div>
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200 shrink-0">
                        <span>Xem tất cả khóa học trên hệ thống</span>
                        <span>➔</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($relatedCourses as $course)
                        @php
                            $discountPrice = $course->discount_price ?? $course->sale_price;
                            $price = $discountPrice ?? $course->price;
                        @endphp

                        <div class="group flex h-full flex-col justify-between rounded-2xl border border-slate-200 bg-slate-50/50 p-5 transition duration-200 hover:-translate-y-1 hover:border-indigo-300 hover:bg-white hover:shadow-xl dark:border-slate-800 dark:bg-slate-900/60 dark:hover:bg-slate-900">
                            <div>
                                <div class="mb-3 flex items-center justify-between gap-2">
                                    @if($course->category)
                                        <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-bold text-indigo-600 dark:bg-indigo-950 dark:text-indigo-300">
                                            {{ $course->category->name }}
                                        </span>
                                    @endif
                                    <div class="flex items-center gap-1 text-xs">
                                        <svg class="h-3.5 w-3.5 text-amber-400 fill-amber-400" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($course->rating_avg, 1) }}</span>
                                    </div>
                                </div>

                                <h3 class="text-base font-extrabold text-slate-950 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition line-clamp-2 mb-2">
                                    <a href="{{ route('courses.show', $course->slug) }}">
                                        {{ $course->title }}
                                    </a>
                                </h3>

                                <p class="line-clamp-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400 mb-4">
                                    {{ $course->short_description ?: Str::limit($course->description, 100) }}
                                </p>
                            </div>

                            <div class="border-t border-slate-200/80 pt-3 dark:border-slate-800 flex items-center justify-between">
                                <div class="text-xs">
                                    <span class="block text-[11px] font-medium text-slate-400">Học phí:</span>
                                    <strong class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400">{{ $formatPrice($price) }}</strong>
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}" class="h-9 inline-flex items-center justify-center rounded-xl bg-slate-950 px-4 text-xs font-bold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-slate-950 dark:hover:bg-indigo-200">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

