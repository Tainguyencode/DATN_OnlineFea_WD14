@extends('layouts.app')

@section('title', 'Website học online FEA')

@section('content')
    <section class="bg-white dark:bg-slate-950">
        <div class="relative overflow-hidden shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 dark:focus-visible:ring-offset-slate-950" id="banner-slider" tabindex="0" aria-roledescription="carousel" aria-label="FEA Learning Highlights Carousel">
            <style>
                .banner-img-custom {
                    width: 100%;
                    object-fit: cover;
                    /* Sử dụng tỷ lệ gốc của ảnh banner1 để không bị cắt xén (1942x809) */
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
                        <a href="#courses" class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-5 py-2 sm:px-7 sm:py-2.5 transition duration-200 shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            Khám phá khóa học
                        </a>
                    </div>
                </div>
                <div class="banner-slide relative w-full shrink-0">
                    <img src="{{ asset('images/banner3.png') }}" alt="FEA Learning Banner 2 - Lộ trình học tập chuyên biệt tối ưu sự nghiệp" class="block banner-img-custom">
                    <div class="absolute inset-x-0 bottom-6 left-6 sm:bottom-10 sm:left-12 lg:bottom-12 lg:left-16 flex flex-col items-start justify-end">
                        <h2 class="sr-only">Lộ trình học tập chuyên biệt định hướng công việc</h2>
                        <a href="#paths" class="ui-button-primary px-5 py-2 sm:px-7 sm:py-2.5 rounded-xl font-bold shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2">
                            Xem lộ trình học
                        </a>
                    </div>
                </div>
                <div class="banner-slide relative w-full shrink-0">
                    <img src="{{ asset('images/banner2.png') }}" alt="FEA Learning Banner 3 - Hệ thống vinh danh, điểm số và bảng xếp hạng tuần" class="block banner-img-custom">
                    <div class="absolute inset-x-0 bottom-6 left-6 sm:bottom-10 sm:left-12 lg:bottom-12 lg:left-16 flex flex-col items-start justify-end">
                        <h2 class="sr-only">Hệ thống vinh danh, tích lũy điểm thưởng và bảng xếp hạng tuần</h2>
                        <a href="#instructors" class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-5 py-2 sm:px-7 sm:py-2.5 transition duration-200 shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                            Xem bảng xếp hạng
                        </a>
                    </div>
                </div>
                {{-- Clone slide 1 để loop vô tận không giật --}}
                <div class="banner-slide relative w-full shrink-0" aria-hidden="true">
                    <img src="{{ asset('images/banner1.png') }}" alt="FEA Learning Banner 1" class="block banner-img-custom">
                    <div class="absolute inset-x-0 bottom-6 left-6 sm:bottom-10 sm:left-12 lg:bottom-12 lg:left-16 flex flex-col items-start justify-end">
                        <a href="#courses" class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-5 py-2 sm:px-7 sm:py-2.5 transition duration-200 shadow-sm hover:shadow-md text-xs sm:text-sm md:text-base focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
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
<section class="bg-white py-10 sm:py-12 lg:py-16 dark:bg-slate-950">
        <div class="ui-container">
            <p class="mb-8 text-center text-sm font-semibold text-slate-500 dark:text-slate-400">Được thiết kế cho sinh
                viên, giảng viên và đội ngũ đào tạo hiện đại</p>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
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
                <div
                    class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
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
                <div
                    class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
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
                <div
                    class="flex items-start gap-4 p-2 transition duration-300 hover:-translate-y-1">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0056D2] dark:bg-blue-900/20 dark:text-blue-400 shadow-sm">
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


    <section class="border-y border-slate-200/60 bg-slate-50 py-10 sm:py-12 dark:border-slate-800/60 dark:bg-slate-900/40">
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
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $stats['courses'] }}<span
                                class="text-[#0056D2]">+</span></div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Khóa học</div>
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
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $stats['students'] }}<span
                                class="text-[#0056D2]">+</span></div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Học viên</div>
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
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">
                            {{ $stats['instructors'] }}<span class="text-[#0056D2]">+</span></div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Giảng viên</div>
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
                        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ $categories->count() }}<span
                                class="text-[#0056D2]">+</span></div>
                        <div class="mt-1 text-sm font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Danh mục</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($featuredCourses->isNotEmpty())
        <section class="ui-section">
            <div class="ui-container">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Khóa học nổi bật</h2>
                    <p class="mt-2 text-slate-500 dark:text-slate-400">Tuyển chọn các khóa học được đánh giá cao nhất trên
                        hệ thống.</p>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredCourses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="categories" class="bg-slate-50/50 py-16 sm:py-20 dark:bg-slate-950/30 border-t border-b border-slate-200/60 dark:border-slate-800/50">
        <div class="ui-container">
            <div class="mb-12 text-center sm:text-left relative">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white sm:text-3xl inline-block relative">
                    Danh mục môn học
                    <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 sm:left-0 sm:translate-x-0 h-1 w-12 bg-gradient-to-r from-[#0056D2] to-blue-400 rounded-full"></span>
                </h2>
                <p class="mt-4 text-slate-500 dark:text-slate-400">Khám phá các môn học giúp bạn phát triển kỹ năng chuyên môn.</p>
            </div>

            @if($categories->isNotEmpty())
                @php
                    $bentoCategories = $categories->take(4);
                    $categoryStyles = [
                        0 => [ // Lập trình & Phát triển
                            'iconBg' => 'bg-blue-50/80 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 group-hover:bg-blue-600 group-hover:text-white',
                            'badge' => 'bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400',
                            'textHover' => 'group-hover:text-blue-600 dark:group-hover:text-blue-400',
                            'borderHover' => 'hover:border-blue-300 dark:hover:border-blue-800',
                            'glow' => 'from-blue-500/5 to-transparent',
                            'cta' => 'text-blue-600 dark:text-blue-400 group-hover:text-blue-700'
                        ],
                        1 => [ // Kinh doanh
                            'iconBg' => 'bg-purple-50/80 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white',
                            'badge' => 'bg-purple-50 dark:bg-purple-950/30 text-purple-600 dark:text-purple-400',
                            'textHover' => 'group-hover:text-purple-600 dark:group-hover:text-purple-400',
                            'borderHover' => 'hover:border-purple-300 dark:hover:border-purple-800',
                            'glow' => 'from-purple-500/5 to-transparent',
                            'cta' => 'text-purple-600 dark:text-purple-400 group-hover:text-purple-700'
                        ],
                        2 => [ // Tài chính & Kế toán
                            'iconBg' => 'bg-emerald-50/80 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-600 group-hover:text-white',
                            'badge' => 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400',
                            'textHover' => 'group-hover:text-emerald-600 dark:group-hover:text-emerald-400',
                            'borderHover' => 'hover:border-emerald-300 dark:hover:border-emerald-800',
                            'glow' => 'from-emerald-500/5 to-transparent',
                            'cta' => 'text-emerald-600 dark:text-emerald-400 group-hover:text-emerald-700'
                        ],
                        3 => [ // Công nghệ thông tin & Phần mềm
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
                    @foreach ($bentoCategories as $index => $category)
                        @php
                            $style = $categoryStyles[$index] ?? $categoryStyles[0];
                        @endphp
                        
                        <a href="{{ route('courses.category', $category->slug) }}"
                            class="group block relative overflow-hidden rounded-[24px] border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 dark:border-slate-800 dark:bg-slate-900/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0056D2] {{ $style['borderHover'] }}">
                            
                            <!-- Ambient Glow Corner Effect -->
                            <div class="absolute -right-12 -bottom-12 h-32 w-32 rounded-full bg-gradient-to-tr {{ $style['glow'] }} blur-2xl pointer-events-none transition-all duration-500 group-hover:scale-125"></div>

                            <div class="flex flex-col justify-between h-full min-h-[220px] relative z-10">
                                <div>
                                    <!-- Icon Box -->
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl transition-all duration-300 mb-5 shadow-sm {{ $style['iconBg'] }}">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    
                                    <!-- Title & Description -->
                                    <h3 class="text-lg font-bold text-slate-900 transition-colors duration-300 dark:text-white {{ $style['textHover'] }}">
                                        {{ $category->name }}
                                    </h3>
                                    <p class="text-sm text-slate-500 line-clamp-2 dark:text-slate-400 mt-2 leading-relaxed">
                                        {{ $category->description ?? 'Khóa học chất lượng do đội ngũ giảng viên biên soạn.' }}
                                    </p>
                                </div>

                                <!-- Course Count Badge & CTA -->
                                <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80 pt-4 mt-5">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full transition-colors duration-300 {{ $style['badge'] }}">
                                        {{ $category->children->sum('courses_count') }} khóa học
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm font-bold transition-colors duration-200 {{ $style['cta'] }}">
                                        Khám phá
                                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section id="courses" class="ui-section border-t border-slate-200 dark:border-slate-800">
        <div class="ui-container">
            <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Tất cả khóa học</h2>
                    <p class="mt-1 text-slate-500 dark:text-slate-400">Tìm kiếm và phân loại khóa học phù hợp với bạn.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('home') }}#courses"
                class="mb-8 rounded-2xl bg-slate-50 p-6 dark:bg-slate-900/60">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="lg:col-span-1">
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Tìm kiếm</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Tên khóa học..." class="ui-input">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Danh mục</label>
                        <select name="category" class="ui-select cursor-pointer">
                            <option value="">Tất cả</option>
                            @foreach ($categories as $parent)
                                <option value="{{ $parent->slug }}" @selected($selectedCategory?->id === $parent->id)>Tất cả
                                     {{ $parent->name }}</option>
                                @if ($parent->children->isNotEmpty())
                                    <optgroup label="{{ $parent->name }}">
                                        @foreach ($parent->children as $cat)
                                            <option value="{{ $cat->slug }}" @selected($selectedCategory?->id === $cat->id)>
                                                {{ $cat->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Trình độ</label>
                        <select name="level" class="ui-select cursor-pointer">
                            <option value="">Tất cả</option>
                            <option value="beginner" @selected(request('level') == 'beginner')>Cơ bản</option>
                            <option value="intermediate" @selected(request('level') == 'intermediate')>Trung cấp</option>
                            <option value="advanced" @selected(request('level') == 'advanced')>Nâng cao</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Sắp xếp
                            theo</label>
                        <select name="sort" class="ui-select cursor-pointer">
                            <option value="newest" @selected(request('sort', 'newest') == 'newest')>Mới nhất</option>
                            <option value="rating" @selected(request('sort') == 'rating')>Đánh giá cao</option>
                            <option value="popular" @selected(request('sort') == 'popular')>Phổ biến</option>
                            <option value="price_asc" @selected(request('sort') == 'price_asc')>Giá tăng dần</option>
                            <option value="price_desc" @selected(request('sort') == 'price_desc')>Giá giảm dần</option>
                        </select>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-200/60 pt-4 dark:border-slate-800/60">
                    <a href="{{ route('home') }}#courses"
                        class="text-sm font-semibold text-slate-500 transition duration-200 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                        Xóa bộ lọc
                    </a>
                    <button type="submit" class="ui-button-primary shadow-sm hover:shadow-md">
                        Áp dụng lọc
                    </button>
                </div>
            </form>

            @if ($courses->isEmpty())
                <div class="ui-empty">
                    <svg class="mx-auto mb-3 h-12 w-12 text-slate-400" fill="none" stroke="currentColor"
                        stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <p class="text-base font-bold text-slate-900 dark:text-white">Không tìm thấy khóa học nào</p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Vui lòng thay đổi tiêu chí tìm kiếm và thử
                        lại.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($courses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
                <div class="mt-10">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </section>

    @if ($learningPaths->isNotEmpty())
        <section id="paths" class="border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950">
            <div class="ui-container">
                <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                    <div class="max-w-2xl">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Lộ trình học tập chuyên biệt</h2>
                        <p class="mt-2 text-slate-500 dark:text-slate-400">Học theo trình tự bài bản, giúp tiết kiệm thời gian
                            và định hướng rõ ràng mục tiêu công việc hoặc đồ án.</p>
                    </div>
                    <a href="{{ route('learning-paths.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200">
                        Xem tất cả lộ trình
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 8 4 4m0 0-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($learningPaths as $path)
                        <div
                            class="flex flex-col rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                            <div class="mb-4">
                                <span class="ui-badge-primary">
                                    {{ $path->courses()->count() }} khóa học
                                </span>
                            </div>
                            <h3 class="mb-2 text-lg font-bold text-slate-900 dark:text-white">{{ $path->title }}</h3>
                            <p class="flex-grow text-sm leading-6 text-slate-500 dark:text-slate-400">
                                {{ $path->description ?? 'Lộ trình bài bản giúp sinh viên củng cố kiến thức từ nền tảng đến chuyên sâu.' }}
                            </p>
                            <a href="{{ route('learning-paths.show', $path->slug) }}"
                                class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200">
                                Xem chi tiết lộ trình
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m17 8 4 4m0 0-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    <section id="instructors" class="ui-section border-t border-slate-200 dark:border-slate-800">
        <div class="ui-container">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
                <div>
                    <h2 class="mb-2 text-2xl font-bold text-slate-900 dark:text-white">Hệ thống vinh danh</h2>
                    <p class="mb-6 text-slate-500 dark:text-slate-400">Tích lũy điểm số và mở khóa các danh hiệu học tập.
                    </p>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($badges as $badge)
                            <div
                                class="flex items-start gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 15.5 8.5 18l1.2-4.1-3.3-2.5 4.2-.1L12 7.2l1.4 4.1 4.2.1-3.3 2.5 1.2 4.1-3.5-2.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $badge->name }}
                                    </h3>
                                    <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">
                                        {{ $badge->description }}</p>
                                    <span
                                        class="mt-2 inline-block rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $badge->points_required }} điểm
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <div
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <div
                            class="border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-800/60">
                            <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                                Bảng xếp hạng tuần
                            </h3>
                        </div>
                        <div class="divide-y divide-slate-200 p-2 dark:divide-slate-800">
                            @forelse($weeklyLeaderboard as $index => $student)
                                @php
                                    $isTop1 = $index === 0;
                                    $rankBg = $isTop1 ? 'bg-amber-500 text-white' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';
                                    $itemBg = $isTop1 ? 'bg-[#fffbeb] dark:bg-amber-900/20' : '';
                                @endphp
                                <div class="flex items-center justify-between p-3 rounded-lg {{ $itemBg }}">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $rankBg }} text-sm font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $student->avatarUrl() }}" alt="Avatar" class="h-6 w-6 rounded-full border border-slate-200 dark:border-slate-700 object-cover">
                                            <span class="text-sm {{ $isTop1 ? 'font-bold' : 'font-medium' }} text-slate-900 dark:text-white">
                                                {{ $student->name }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold {{ $isTop1 ? 'text-amber-600 dark:text-amber-300' : 'text-slate-500 dark:text-slate-400' }}">
                                        {{ $student->total_points }} pts
                                    </span>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-slate-500 dark:text-slate-400">
                                    Chưa có dữ liệu tuần này.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="business" class="border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950">
        <div class="ui-container">
            <div
                class="grid items-center gap-8 rounded-2xl border border-slate-200 bg-slate-50 p-8 dark:border-slate-700 dark:bg-slate-900 lg:grid-cols-[1fr_360px]">
                <div>
                    <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-[#0056D2] dark:text-blue-300">Doanh
                        nghiệp</p>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Nâng cao năng lực đội ngũ với lộ trình
                        học tập có cấu trúc</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Tổ chức khóa học, quản
                        lý tiến độ và theo dõi kết quả học tập trong một nền tảng thống nhất.</p>
                </div>
                <a href="{{ route('register.role', 'instructor') }}" class="ui-button-primary justify-center">Bắt đầu
                    triển khai</a>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-16 dark:bg-slate-900">
        <div class="ui-container">
            <div class="mb-8 max-w-2xl">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Học viên nói gì về FEA Learning</h2>
                <p class="mt-2 text-slate-500 dark:text-slate-400">Các phản hồi ngắn giúp bạn hình dung trải nghiệm học tập
                    trên nền tảng.</p>
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ([['name' => 'Minh Anh', 'text' => 'Nội dung rõ ràng, dễ theo dõi và giúp mình biết nên học gì tiếp theo.'], ['name' => 'Quốc Huy', 'text' => 'Dashboard tiến độ rất hữu ích khi học nhiều khóa cùng lúc.'], ['name' => 'Lan Phương', 'text' => 'Giao diện gọn, tập trung vào bài học và không gây rối mắt.']] as $testimonial)
                    <div
                        class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-950">
                        <p class="text-sm leading-6 text-slate-600 dark:text-slate-400">“{{ $testimonial['text'] }}”</p>
                        <p class="mt-4 text-sm font-semibold text-slate-900 dark:text-white">{{ $testimonial['name'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($faqs->isNotEmpty())
        <section id="faq" class="border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 text-center">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Câu hỏi thường gặp</h2>
                    <p class="mt-2 text-slate-500 dark:text-slate-400">Giải đáp nhanh các thắc mắc về lớp học trực tuyến.
                    </p>
                </div>
                <div class="space-y-3">
                    @foreach ($faqs as $faq)
                        <details
                            class="group rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900">
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between px-5 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $faq->question }}
                                <svg class="h-5 w-5 text-slate-500 transition-transform group-open:rotate-180 dark:text-slate-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="m19 9-7 7-7-7" />
                                </svg>
                            </summary>
                            <div class="px-5 pb-4 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                {{ $faq->answer }}
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @guest
        <section class="bg-[#0056D2] py-16 text-white dark:bg-blue-800">
            <div class="mx-auto max-w-4xl px-4 text-center">
                <h2 class="mb-4 text-3xl font-bold">Sẵn sàng nâng tầm kiến thức của bạn?</h2>
                <p class="mx-auto mb-8 max-w-xl text-white/85">Đăng ký tài khoản sinh viên miễn phí trên nền tảng của chúng tôi
                    ngay hôm nay để trải nghiệm môi trường học tập hiện đại.</p>
                <a href="{{ route('register.role', 'student') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-white bg-white px-8 py-3 font-medium text-[#0056D2] transition duration-200 hover:bg-blue-50">
                    Tạo tài khoản miễn phí
                </a>
            </div>
        </section>
    @endguest



    

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Hero Slider ---
            const slider = document.getElementById('banner-slider');
            const track = document.getElementById('banner-track');
            const prevBtn = document.getElementById('prev-banner');
            const nextBtn = document.getElementById('next-banner');

            if (track) {
                const slides = track.querySelectorAll('.banner-slide');
                const totalSlides = slides.length - 1; // 2 slides + 1 clone
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
                        }, 700); // match transition duration
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
                        // force reflow
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
                    autoPlayTimer = setInterval(nextSlide, 4000);
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

                // Keyboard support for Hero Slider
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

                    // Pause on focus/hover, resume on blur/leave
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
            // --- AJAX Course Filtering & Pagination ---
            const coursesSection = document.getElementById('courses');
            const filterForm = coursesSection ? coursesSection.querySelector('form') : null;

            if (coursesSection && filterForm) {
                let currentQueryString = window.location.search;

                function loadCoursesContent(url, updateHistory = true) {
                    // Visual transition indicators
                    coursesSection.classList.add('transition-opacity', 'duration-300');
                    coursesSection.style.opacity = '0.4';
                    coursesSection.style.pointerEvents = 'none';

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newCoursesContent = doc.getElementById('courses');

                        if (newCoursesContent) {
                            coursesSection.innerHTML = newCoursesContent.innerHTML;
                            rebindFormListeners();
                            
                            // Smoothly scroll back to the top of the course catalog section
                            coursesSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

                            // Update track state
                            currentQueryString = new URL(url, window.location.origin).search;

                            // Maintain URL state in browser history
                            if (updateHistory) {
                                history.pushState(null, '', url);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading filtered courses via AJAX:', error);
                    })
                    .finally(() => {
                        coursesSection.style.opacity = '1';
                        coursesSection.style.pointerEvents = 'auto';
                    });
                }

                function rebindFormListeners() {
                    const currentForm = coursesSection.querySelector('form');
                    if (currentForm) {
                        currentForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const formData = new FormData(currentForm);
                            const params = new URLSearchParams();
                            
                            // Only append non-empty form elements
                            for (const [key, value] of formData.entries()) {
                                if (value !== '') {
                                    params.set(key, value);
                                }
                            }
                            
                            const action = currentForm.getAttribute('action') || window.location.pathname;
                            const actionUrl = new URL(action, window.location.origin);
                            
                            params.forEach((value, key) => {
                                actionUrl.searchParams.set(key, value);
                            });
                            actionUrl.hash = 'courses';

                            loadCoursesContent(actionUrl.toString());
                        });
                    }
                }

                rebindFormListeners();

                // Handle pagination & filter reset link clicks
                coursesSection.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    if (link && link.getAttribute('href')) {
                        const href = link.getAttribute('href');
                        if (href.includes('page=') || href.includes('search=') || href.includes('category=') || href.includes('level=') || href.includes('sort=') || href.endsWith('#courses') || href.includes('/home#courses')) {
                              const url = new URL(href, window.location.origin);
                            if (url.origin === window.location.origin) {
                                e.preventDefault();
                                url.hash = 'courses';
                                loadCoursesContent(url.toString());
                            }
                        }
                    }
                });

                // Listen for browser back/forward buttons
                window.addEventListener('popstate', function() {
                    const newUrl = new URL(window.location.href);
                    // Do not load courses if the user only navigated to an anchor section
                    if (newUrl.hash === '#paths' || newUrl.hash === '#instructors' || newUrl.hash === '#business' || newUrl.hash === '#faq' || newUrl.hash === '#categories') {
                        return;
                    }
                    if (newUrl.search !== currentQueryString) {
                        loadCoursesContent(window.location.href, false);
                    }
                });

                // Smooth scroll for page section anchors (preserving search parameters)
                document.addEventListener('click', function(e) {
                    const anchor = e.target.closest('a');
                    if (anchor && anchor.getAttribute('href')) {
                        const href = anchor.getAttribute('href');
                        if (href.startsWith('#') || href.includes('#paths') || href.includes('#instructors') || href.includes('#business') || href.includes('#faq') || href.includes('#categories')) {
                            const hashIndex = href.indexOf('#');
                            if (hashIndex !== -1) {
                                const targetId = href.substring(hashIndex + 1);
                                if (targetId !== 'courses') {
                                    const targetElement = document.getElementById(targetId);
                                    if (targetElement) {
                                        e.preventDefault();
                                        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                        const newUrl = window.location.pathname + window.location.search + '#' + targetId;
                                        history.pushState(null, '', newUrl);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
