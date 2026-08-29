@extends('layouts.app')

@section('title', 'Website học online FEA - Nền tảng học trực tuyến chất lượng cao')

@section('content')
    {{-- HERO BANNER CAROUSEL --}}
    <section class="bg-white dark:bg-slate-950">
        <div class="relative overflow-hidden shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 dark:focus-visible:ring-offset-slate-950" id="banner-slider" tabindex="0" aria-roledescription="carousel" aria-label="FEA Learning Highlights Carousel">
            <style>
                .banner-img-custom {
                    width: 100%;
                    object-fit: cover;
                    aspect-ratio: 1942 / 809;
                }

                @media (max-width: 768px) {
                    .banner-img-custom {
                        aspect-ratio: 16 / 9;
                    }
                }
            </style>
            {{-- Slides --}}
            <div class="banner-track flex transition-transform duration-700 ease-in-out" id="banner-track">
                <div class="banner-slide relative w-full shrink-0">
                    <img src="{{ asset('images/banner1.png') }}" alt="FEA Learning Banner 1 - Học mọi lúc mọi nơi cùng nền tảng trực tuyến" class="block banner-img-custom">
                    <div class="absolute inset-x-0 bottom-6 left-6 sm:bottom-10 sm:left-12 lg:bottom-12 lg:left-16 flex flex-col items-start justify-end">
                        <h2 class="sr-only">Nền tảng học trực tuyến FEA Learning - Học mọi lúc mọi nơi</h2>
                        <p class="sr-only">Học tập chuyên nghiệp với đa dạng khóa học chất lượng.</p>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-5 py-2 sm:px-7 sm:py-2.5 transition duration-200 shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            Khám phá khóa học
                        </a>
                    </div>
                </div>
                <div class="banner-slide relative w-full shrink-0">
                    <img src="{{ asset('images/banner3.png') }}" alt="FEA Learning Banner 2 - Lộ trình học tập chuyên biệt tối ưu sự nghiệp" class="block banner-img-custom">
                    <div class="absolute inset-x-0 bottom-6 left-6 sm:bottom-10 sm:left-12 lg:bottom-12 lg:left-16 flex flex-col items-start justify-end">
                        <h2 class="sr-only">Lộ trình học tập chuyên biệt định hướng công việc</h2>
                        <a href="{{ route('learning-paths.index') }}" class="ui-button-primary px-5 py-2 sm:px-7 sm:py-2.5 rounded-xl font-bold shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2">
                            Xem lộ trình học
                        </a>
                    </div>
                </div>
                <div class="banner-slide relative w-full shrink-0">
                    <img src="{{ asset('images/banner2.png') }}" alt="FEA Learning Banner 3 - Đội ngũ giảng viên và chuyên gia đào tạo chất lượng cao" class="block banner-img-custom">
                    <div class="absolute inset-x-0 bottom-6 left-6 sm:bottom-10 sm:left-12 lg:bottom-12 lg:left-16 flex flex-col items-start justify-end">
                        <h2 class="sr-only">Đội ngũ giảng viên và chuyên gia đào tạo chất lượng cao</h2>
                        <a href="{{ route('instructors.index') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-5 py-2 sm:px-7 sm:py-2.5 transition duration-200 shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            Đội ngũ giảng viên
                        </a>
                    </div>
                </div>
                {{-- Clone slide 1 để loop vô tận không giật --}}
                <div class="banner-slide relative w-full shrink-0" aria-hidden="true">
                    <img src="{{ asset('images/banner1.png') }}" alt="FEA Learning Banner 1" class="block banner-img-custom">
                    <div class="absolute inset-x-0 bottom-6 left-6 sm:bottom-10 sm:left-12 lg:bottom-12 lg:left-16 flex flex-col items-start justify-end">
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-5 py-2 sm:px-7 sm:py-2.5 transition duration-200 shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            Khám phá khóa học
                        </a>
                    </div>
                </div>
            </div>

            <button id="prev-banner"
                class="absolute left-4 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 md:h-12 md:w-12 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500"
                aria-label="Previous slide">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="next-banner"
                class="absolute right-4 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 md:h-12 md:w-12 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500"
                aria-label="Next slide">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </section>

    {{-- FEATURES HIGHLIGHTS --}}
    <section class="bg-white py-10 sm:py-12 lg:py-16 dark:bg-slate-950">
        <div class="ui-container">
            <p class="mb-8 text-center text-sm font-semibold text-slate-500 dark:text-slate-400">Được thiết kế cho sinh viên, giảng viên và đội ngũ đào tạo hiện đại</p>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">FEA Academy</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Chương trình đào tạo chuyên sâu</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">Innovation Lab</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Thực hành dự án công nghệ mới</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">Career Accelerator</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Định hướng và kết nối việc làm</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">Corporate Training</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Giải pháp đào tạo doanh nghiệp</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- STATS SECTION WITH COUNTER ANIMATION (STU-FE-02, STU-BE-01) --}}
    <section class="border-y border-slate-200/60 bg-slate-50 py-10 sm:py-12 dark:border-slate-800/60 dark:bg-slate-900/40"
             x-data="feaStatsCounter({
                 courses: {{ (int)$stats['courses'] }},
                 students: {{ (int)$stats['students'] }},
                 instructors: {{ (int)$stats['instructors'] }},
                 categories: {{ (int)$stats['categories'] }}
             })"
             x-init="initObserver()"
             id="home-stats-counter">
        <div class="ui-container">
            <div class="grid grid-cols-2 divide-x divide-slate-200/60 sm:grid-cols-4 dark:divide-slate-800/60">
                <div class="flex flex-col items-center justify-center gap-3 px-4 text-center">
                    <div class="text-[#0056D2]">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white tabular-nums tracking-tight">
                            <span x-text="display.courses">{{ $stats['courses'] }}</span><span class="text-[#0056D2]">+</span>
                        </div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Khóa học
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center gap-3 px-4 text-center">
                    <div class="text-[#0056D2]">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white tabular-nums tracking-tight">
                            <span x-text="display.students">{{ $stats['students'] }}</span><span class="text-[#0056D2]">+</span>
                        </div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Học viên
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center gap-3 px-4 pt-6 text-center sm:pt-0">
                    <div class="text-[#0056D2]">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 14l9-5-9-5-9 5 9 5Zm0 0 6.16-3.422a12.083 12.083 0 0 1 .665 6.479A11.952 11.952 0 0 0 12 20.055a11.952 11.952 0 0 0-6.824-2.998 12.078 12.078 0 0 1 .665-6.479L12 14Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white tabular-nums tracking-tight">
                            <span x-text="display.instructors">{{ $stats['instructors'] }}</span><span class="text-[#0056D2]">+</span>
                        </div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Giảng viên
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center justify-center gap-3 px-4 pt-6 text-center sm:pt-0">
                    <div class="text-[#0056D2]">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white tabular-nums tracking-tight">
                            <span x-text="display.categories">{{ $stats['categories'] }}</span><span class="text-[#0056D2]">+</span>
                        </div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Danh mục
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KHÓA HỌC NỔI BẬT (STU-FE-04, STU-BE-01) --}}
    <section id="courses" class="ui-section border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950">
        <div class="ui-container">
            <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl inline-block relative">
                        Khóa học nổi bật
                        <span class="absolute -bottom-2 left-0 h-1 w-12 bg-gradient-to-r from-[#0056D2] to-blue-400 rounded-full"></span>
                    </h2>
                    <p class="mt-4 text-slate-500 dark:text-slate-400">Tuyển chọn các khóa học tiêu biểu được đánh giá cao nhất trên hệ thống FEA Learning.</p>
                </div>
                <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-400 dark:hover:text-blue-300 group">
                    <span>Xem tất cả khóa học</span>
                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            @if ($featuredCourses->isEmpty())
                <div class="ui-empty py-16 text-center rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                    <svg class="mx-auto mb-3 h-12 w-12 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="text-base font-bold text-slate-900 dark:text-white">Hiện chưa có khóa học nổi bật</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Vui lòng ghé thăm trang khám phá để tìm các khóa học phù hợp.</p>
                    <a href="{{ route('courses.index') }}" class="mt-4 ui-button-primary inline-flex">
                        Khám phá khóa học
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredCourses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
                <div class="mt-10 text-center">
                    <a href="{{ route('courses.index') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-7 py-3 text-sm font-bold text-slate-800 transition duration-200 hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                        Khám phá thêm các khóa học khác
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- CATEGORIES SECTION (COMPACT & EXPANDABLE) (STU-FE-03, STU-BE-01) --}}
    <section id="categories" class="bg-slate-50/50 py-16 sm:py-20 dark:bg-slate-950/30 border-t border-b border-slate-200/60 dark:border-slate-800/50" x-data="{ expanded: false }">
        <div class="ui-container">
            <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl inline-block relative">
                        Danh mục môn học
                        <span class="absolute -bottom-2 left-0 h-1 w-12 bg-gradient-to-r from-[#0056D2] to-blue-400 rounded-full"></span>
                    </h2>
                    <p class="mt-4 text-slate-500 dark:text-slate-400">Khám phá các môn học giúp bạn phát triển kỹ năng chuyên môn.</p>
                </div>
                @if($categories->count() > 4)
                    <button type="button" @click="expanded = !expanded" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 cursor-pointer">
                        <span x-text="expanded ? 'Thu gọn danh mục' : 'Xem tất cả {{ $categories->count() }} danh mục'"></span>
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                @endif
            </div>

            @if($categories->isNotEmpty())
                @php
                    $categoryStyles = [
                        0 => [ // Blue
                            'iconBg' => 'bg-blue-50/80 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white',
                            'badge' => 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400',
                            'textHover' => 'group-hover:text-blue-600 dark:group-hover:text-blue-400',
                            'borderHover' => 'hover:border-blue-300 dark:hover:border-blue-800',
                            'glow' => 'from-blue-500/5 to-transparent',
                            'cta' => 'text-blue-600 dark:text-blue-400 group-hover:text-blue-700'
                        ],
                        1 => [ // Purple
                            'iconBg' => 'bg-purple-50/80 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white',
                            'badge' => 'bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400',
                            'textHover' => 'group-hover:text-purple-600 dark:group-hover:text-purple-400',
                            'borderHover' => 'hover:border-purple-300 dark:hover:border-purple-800',
                            'glow' => 'from-purple-500/5 to-transparent',
                            'cta' => 'text-purple-600 dark:text-purple-400 group-hover:text-purple-700'
                        ],
                        2 => [ // Emerald
                            'iconBg' => 'bg-emerald-50/80 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white',
                            'badge' => 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400',
                            'textHover' => 'group-hover:text-emerald-600 dark:group-hover:text-emerald-400',
                            'borderHover' => 'hover:border-emerald-300 dark:hover:border-emerald-800',
                            'glow' => 'from-emerald-500/5 to-transparent',
                            'cta' => 'text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700'
                        ],
                        3 => [ // Orange
                            'iconBg' => 'bg-orange-50/80 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 group-hover:bg-orange-600 group-hover:text-white',
                            'badge' => 'bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400',
                            'textHover' => 'group-hover:text-orange-600 dark:group-hover:text-orange-400',
                            'borderHover' => 'hover:border-orange-300 dark:hover:border-orange-800',
                            'glow' => 'from-orange-500/5 to-transparent',
                            'cta' => 'text-orange-600 dark:text-orange-400 group-hover:text-orange-700'
                        ]
                    ];
                @endphp

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($categories as $index => $category)
                        @php
                            $style = $categoryStyles[$index % count($categoryStyles)] ?? $categoryStyles[0];
                            $courseCount = $category->children->sum('courses_count') ?: $category->courses_count;
                        @endphp
                        
                        <div x-show="expanded || {{ $index }} < 4" x-cloak
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100">
                            <a href="{{ route('courses.category', $category->slug) }}"
                                class="group block relative overflow-hidden rounded-[20px] border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:border-slate-800 dark:bg-slate-900/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] {{ $style['borderHover'] }}">
                                
                                <div class="absolute -right-10 -bottom-10 h-28 w-28 rounded-full bg-gradient-to-tr {{ $style['glow'] }} blur-xl pointer-events-none transition-all duration-500 group-hover:scale-125"></div>

                                <div class="flex flex-col justify-between h-full min-h-[190px] relative z-10">
                                    <div>
                                        <div class="flex h-11 w-11 items-center justify-center rounded-xl transition-all duration-300 mb-4 shadow-sm {{ $style['iconBg'] }}">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                        
                                        <h3 class="text-base font-bold text-slate-900 transition-colors duration-300 dark:text-white line-clamp-1 {{ $style['textHover'] }}" title="{{ $category->name }}">
                                            {{ $category->name }}
                                        </h3>
                                        <p class="text-xs text-slate-500 line-clamp-2 dark:text-slate-400 mt-1.5 leading-relaxed">
                                            {{ $category->description ?? 'Khóa học chất lượng do đội ngũ giảng viên biên soạn.' }}
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80 pt-3.5 mt-4">
                                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full transition-colors duration-300 {{ $style['badge'] }}">
                                            {{ $courseCount }} khóa học
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs font-bold transition-colors duration-200 {{ $style['cta'] }}">
                                            Khám phá
                                            <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    {{-- LEARNING PATHS SECTION --}}
    @if ($learningPaths->isNotEmpty())
        <section id="paths" class="border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950">
            <div class="ui-container">
                <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl inline-block relative">
                            Lộ trình học tập chuyên biệt
                            <span class="absolute -bottom-2 left-0 h-1 w-12 bg-gradient-to-r from-[#0056D2] to-blue-400 rounded-full"></span>
                        </h2>
                        <p class="mt-4 text-slate-500 dark:text-slate-400">Học theo trình tự bài bản, giúp tiết kiệm thời gian và định hướng rõ ràng mục tiêu công việc.</p>
                    </div>
                    <a href="{{ route('learning-paths.index') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200 group">
                        <span>Xem tất cả lộ trình</span>
                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 8 4 4m0 0-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($learningPaths as $path)
                        <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                            <div class="mb-4">
                                <span class="ui-badge-primary">
                                    {{ $path->courses_count }} khóa học
                                </span>
                            </div>
                            <h3 class="mb-2 text-lg font-bold text-slate-900 dark:text-white">{{ $path->title }}</h3>
                            <p class="flex-grow text-sm leading-6 text-slate-500 dark:text-slate-400 line-clamp-3">
                                {{ $path->description ?? 'Lộ trình bài bản giúp sinh viên củng cố kiến thức từ nền tảng đến chuyên sâu.' }}
                            </p>
                            <a href="{{ route('learning-paths.show', $path->slug) }}"
                                class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200 group">
                                <span>Xem chi tiết lộ trình</span>
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 8 4 4m0 0-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ENTERPRISE SECTION --}}
    <section id="business" class="border-t border-slate-200 bg-slate-50/50 py-16 dark:border-slate-800 dark:bg-slate-900/30">
        <div class="ui-container">
            <div class="grid items-center gap-8 rounded-2xl border border-slate-200 bg-white p-8 dark:border-slate-800 dark:bg-slate-900 lg:grid-cols-[1fr_360px] shadow-sm">
                <div>
                    <p class="mb-2 text-xs font-bold uppercase tracking-wider text-[#0056D2] dark:text-blue-400">Doanh nghiệp & Đào tạo</p>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Nâng cao năng lực đội ngũ với lộ trình học tập có cấu trúc</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Tổ chức khóa học, quản lý tiến độ và theo dõi kết quả học tập trong một nền tảng thống nhất và bảo mật.</p>
                </div>
                <a href="{{ route('register.role', 'instructor') }}" class="ui-button-primary justify-center text-center py-3">
                    Đăng ký giảng viên đào tạo
                </a>
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS / STUDENT REVIEWS (STU-FE-06) --}}
    <section class="bg-white py-16 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800">
        <div class="ui-container">
            <div class="mb-10 max-w-2xl">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl inline-block relative">
                    Học viên nói gì về FEA Learning
                    <span class="absolute -bottom-2 left-0 h-1 w-12 bg-gradient-to-r from-[#0056D2] to-blue-400 rounded-full"></span>
                </h2>
                <p class="mt-4 text-slate-500 dark:text-slate-400">Trải nghiệm và đánh giá thực tế từ các học viên đang học tập trên hệ thống.</p>
            </div>

            @if($testimonials->isNotEmpty())
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <div class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-slate-50/50 p-6 shadow-sm transition-all duration-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900/60">
                            <div>
                                {{-- Stars --}}
                                <div class="flex items-center gap-1 text-amber-400 mb-3" aria-label="{{ $testimonial->rating }} trên 5 sao">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= $testimonial->rating ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                    <span class="ml-1 text-xs font-bold text-slate-700 dark:text-slate-300">{{ number_format($testimonial->rating, 1) }}</span>
                                </div>

                                {{-- Comment text --}}
                                <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300 line-clamp-4">
                                    “{{ $testimonial->comment }}”
                                </p>
                            </div>

                            <div class="mt-5 flex items-center gap-3 border-t border-slate-200/60 dark:border-slate-800 pt-4">
                                @if($testimonial->user?->avatar)
                                    <img src="{{ $testimonial->user->avatarUrl() }}" alt="{{ $testimonial->user->name }}" class="h-10 w-10 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                                @else
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-[#0056D2] dark:bg-blue-900/30 dark:text-blue-300">
                                        {{ mb_strtoupper(mb_substr($testimonial->user?->name ?? 'H', 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                        {{ $testimonial->user?->name ?? 'Học viên FEA' }}
                                    </h4>
                                    @if($testimonial->course)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate" title="{{ $testimonial->course->title }}">
                                            {{ $testimonial->course->title }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="flex items-center gap-1 text-amber-400 mb-3">
                            @for($i=1;$i<=5;$i++) <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> @endfor
                        </div>
                        <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">“Nội dung các bài học được cấu trúc rõ ràng, video mượt và hệ thống theo dõi tiến độ rất hữu ích.”</p>
                        <div class="mt-5 flex items-center gap-3 border-t border-slate-200/60 dark:border-slate-800 pt-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-[#0056D2]">MA</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Minh Anh</h4>
                                <p class="text-xs text-slate-500">Học viên lập trình</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="flex items-center gap-1 text-amber-400 mb-3">
                            @for($i=1;$i<=5;$i++) <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> @endfor
                        </div>
                        <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">“Trợ lý AI hỗ trợ giải đáp câu hỏi trong từng bài học giúp mình hiểu sâu hơn và tiết kiệm nhiều thời gian.”</p>
                        <div class="mt-5 flex items-center gap-3 border-t border-slate-200/60 dark:border-slate-800 pt-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">QH</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Quốc Huy</h4>
                                <p class="text-xs text-slate-500">Học viên công nghệ</p>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/60">
                        <div class="flex items-center gap-1 text-amber-400 mb-3">
                            @for($i=1;$i<=5;$i++) <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> @endfor
                        </div>
                        <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">“Giao diện hiện đại, tinh gọn, tốc độ tải nhanh và lộ trình học tập được chuẩn hóa rõ ràng.”</p>
                        <div class="mt-5 flex items-center gap-3 border-t border-slate-200/60 dark:border-slate-800 pt-4">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 text-xs font-bold text-purple-700">LP</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white">Lan Phương</h4>
                                <p class="text-xs text-slate-500">Học viên thiết kế</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- FAQS SECTION --}}
    @if ($faqs->isNotEmpty())
        <section id="faq" class="border-t border-slate-200 bg-slate-50/50 py-16 dark:border-slate-800 dark:bg-slate-900/30">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 text-center">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl">Câu hỏi thường gặp</h2>
                    <p class="mt-3 text-slate-500 dark:text-slate-400">Giải đáp nhanh các thắc mắc về khóa học và trải nghiệm học tập.</p>
                </div>
                <div class="space-y-3">
                    @foreach ($faqs as $faq)
                        <details class="group rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900 transition shadow-xs">
                            <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $faq->question }}
                                <svg class="h-5 w-5 text-slate-500 transition-transform duration-200 group-open:rotate-180 dark:text-slate-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="px-5 pb-4 text-sm leading-6 text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-800 pt-3">
                                {{ $faq->answer }}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- CTA GUEST SECTION --}}
    @guest
        <section class="bg-[#0056D2] py-16 text-white dark:bg-blue-800">
            <div class="mx-auto max-w-4xl px-4 text-center">
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight">Sẵn sàng nâng tầm kiến thức của bạn?</h2>
                <p class="mx-auto mb-8 max-w-xl text-white/85 text-sm sm:text-base leading-relaxed">Đăng ký tài khoản học viên miễn phí trên nền tảng của chúng tôi ngay hôm nay để trải nghiệm môi trường học tập hiện đại.</p>
                <a href="{{ route('register.role', 'student') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-white bg-white px-8 py-3.5 font-bold text-[#0056D2] transition duration-200 hover:bg-blue-50 shadow-md">
                    Tạo tài khoản miễn phí
                </a>
            </div>
        </section>
    @endguest

    {{-- SCRIPTS --}}
    <script>
        // Alpine helper for stats counter animation (STU-FE-02)
        function feaStatsCounter(targets) {
            return {
                targets: targets,
                display: {
                    courses: 0,
                    students: 0,
                    instructors: 0,
                    categories: 0
                },
                hasAnimated: false,
                initObserver() {
                    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    if (prefersReduced) {
                        this.display = { ...this.targets };
                        this.hasAnimated = true;
                        return;
                    }

                    const el = this.$el;
                    if (!el) return;

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && !this.hasAnimated) {
                                this.hasAnimated = true;
                                this.animateCounters();
                                observer.disconnect();
                            }
                        });
                    }, { threshold: 0.2 });

                    observer.observe(el);
                },
                animateCounters() {
                    const duration = 1200; // ms
                    const startTime = performance.now();

                    const step = (currentTime) => {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        // Ease out cubic
                        const ease = 1 - Math.pow(1 - progress, 3);

                        for (const key in this.targets) {
                            this.display[key] = Math.floor(ease * this.targets[key]);
                        }

                        if (progress < 1) {
                            requestAnimationFrame(step);
                        } else {
                            this.display = { ...this.targets };
                        }
                    };

                    requestAnimationFrame(step);
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            // --- Hero Banner Slider ---
            const slider = document.getElementById('banner-slider');
            const track = document.getElementById('banner-track');
            const prevBtn = document.getElementById('prev-banner');
            const nextBtn = document.getElementById('next-banner');

            if (track) {
                const slides = track.querySelectorAll('.banner-slide');
                const totalSlides = slides.length - 1; // Real slides + 1 clone
                let currentIndex = 0;
                let isAnimating = false;
                let autoPlayTimer = null;
                let isUserInteracting = false;

                function updateSlider(instant = false) {
                    if (instant) {
                        track.style.transition = 'none';
                    } else {
                        track.style.transition = 'transform 700ms ease-in-out';
                    }
                    track.style.transform = `translateX(-${currentIndex * 100}%)`;
                }

                function nextSlide() {
                    if (isAnimating) return;
                    isAnimating = true;
                    currentIndex++;
                    updateSlider();

                    if (currentIndex === totalSlides) {
                        setTimeout(() => {
                            currentIndex = 0;
                            updateSlider(true);
                            isAnimating = false;
                        }, 700);
                    } else {
                        setTimeout(() => {
                            isAnimating = false;
                        }, 700);
                    }
                    if (!isUserInteracting) resetAutoPlay();
                }

                function prevSlide() {
                    if (isAnimating) return;
                    isAnimating = true;

                    if (currentIndex === 0) {
                        currentIndex = totalSlides;
                        updateSlider(true);
                        track.offsetHeight;
                    }

                    currentIndex--;
                    updateSlider();

                    setTimeout(() => {
                        isAnimating = false;
                    }, 700);
                    if (!isUserInteracting) resetAutoPlay();
                }

                function startAutoPlay() {
                    clearInterval(autoPlayTimer);
                    autoPlayTimer = setInterval(nextSlide, 4500);
                }

                function stopAutoPlay() {
                    clearInterval(autoPlayTimer);
                }

                function resetAutoPlay() {
                    stopAutoPlay();
                    startAutoPlay();
                }

                if (nextBtn) nextBtn.addEventListener('click', function() {
                    isUserInteracting = true;
                    stopAutoPlay();
                    nextSlide();
                });

                if (prevBtn) prevBtn.addEventListener('click', function() {
                    isUserInteracting = true;
                    stopAutoPlay();
                    prevSlide();
                });

                if (slider) {
                    slider.addEventListener('keydown', function(e) {
                        if (e.key === 'ArrowLeft') {
                            e.preventDefault();
                            isUserInteracting = true;
                            stopAutoPlay();
                            prevSlide();
                        } else if (e.key === 'ArrowRight') {
                            e.preventDefault();
                            isUserInteracting = true;
                            stopAutoPlay();
                            nextSlide();
                        }
                    });

                    slider.addEventListener('mouseenter', function() {
                        isUserInteracting = true;
                        stopAutoPlay();
                    });
                    slider.addEventListener('mouseleave', function() {
                        isUserInteracting = false;
                        startAutoPlay();
                    });
                    slider.addEventListener('focusin', function() {
                        isUserInteracting = true;
                        stopAutoPlay();
                    });
                    slider.addEventListener('focusout', function() {
                        isUserInteracting = false;
                        startAutoPlay();
                    });
                }

                startAutoPlay();
            }
        });
    </script>
@endsection
