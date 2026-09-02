@extends('layouts.app')

@section('title', 'Website học online FEA - Nền tảng học trực tuyến chất lượng cao')

@section('content')
    {{-- Hero: giữ banner hiện tại, chỉ ưu tiên tải ảnh LCP đầu tiên. --}}
    <section class="bg-slate-50 py-3 sm:py-5 dark:bg-slate-950">
        <div class="mx-auto w-full max-w-[1680px] px-3 sm:px-5 lg:px-6">
            <div class="relative aspect-video overflow-hidden rounded-2xl bg-slate-200 shadow-sm sm:aspect-[1942/809] dark:bg-slate-900"
                 x-data="{ active: 0, timer: null, start() { this.stop(); this.timer = setInterval(() => this.next(), 5000) }, stop() { if (this.timer) clearInterval(this.timer) }, next() { this.active = (this.active + 1) % 3 }, previous() { this.active = (this.active + 2) % 3 } }"
                 x-init="start()" @mouseenter="stop()" @mouseleave="start()" @focusin="stop()" @focusout="start()"
                 @keydown.left.prevent="previous()" @keydown.right.prevent="next()"
                 role="region" aria-roledescription="carousel" aria-label="Điểm nổi bật của FEA Learning" tabindex="0">
                <div class="flex h-full transition-transform duration-700 ease-out" :style="`transform: translateX(-${active * 100}%)`">
                    <div class="relative h-full w-full shrink-0">
                        <img src="{{ asset('images/banner1.png') }}" alt="Học mọi lúc mọi nơi cùng FEA Learning" width="1942" height="809" fetchpriority="high" decoding="async" class="h-full w-full object-cover">
                        <a href="{{ route('courses.index') }}" class="absolute bottom-[12%] left-[21%] inline-flex rounded-lg bg-[#F59E0B] px-3 py-2 text-[10px] font-extrabold text-slate-950 shadow-sm transition hover:bg-amber-400 sm:px-5 sm:text-sm">Khám phá khóa học</a>
                    </div>
                    <div class="relative h-full w-full shrink-0">
                        <img src="{{ asset('images/banner3.png') }}" alt="Lộ trình học tập chuyên biệt tại FEA Learning" width="1734" height="907" loading="lazy" decoding="async" class="h-full w-full object-cover">
                        <a href="{{ route('learning-paths.index') }}" class="absolute bottom-[12%] left-[7%] inline-flex rounded-lg bg-[#0D5BD7] px-3 py-2 text-[10px] font-extrabold text-white shadow-sm transition hover:bg-blue-700 sm:px-5 sm:text-sm">Xem lộ trình học</a>
                    </div>
                    <div class="relative h-full w-full shrink-0">
                        <img src="{{ asset('images/banner2.png') }}" alt="Đội ngũ giảng viên FEA Learning" width="1942" height="809" loading="lazy" decoding="async" class="h-full w-full object-cover">
                        <a href="{{ route('instructors.index') }}" class="absolute bottom-[12%] left-[7%] inline-flex rounded-lg bg-[#F59E0B] px-3 py-2 text-[10px] font-extrabold text-slate-950 shadow-sm transition hover:bg-amber-400 sm:px-5 sm:text-sm">Đội ngũ giảng viên</a>
                    </div>
                </div>
                <button type="button" @click="previous()" class="absolute left-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white/95 text-slate-700 shadow-sm sm:left-4 sm:h-10 sm:w-10" aria-label="Banner trước"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7" /></svg></button>
                <button type="button" @click="next()" class="absolute right-2 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white/95 text-slate-700 shadow-sm sm:right-4 sm:h-10 sm:w-10" aria-label="Banner tiếp theo"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7" /></svg></button>
                <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-2 sm:bottom-4" aria-label="Chọn banner">
                    @for($bannerIndex = 0; $bannerIndex < 3; $bannerIndex++)
                        <button type="button" @click="active = {{ $bannerIndex }}" class="h-2 rounded-full transition-all" :class="active === {{ $bannerIndex }} ? 'w-6 bg-[#0D5BD7]' : 'w-2 bg-white/90'" aria-label="Mở banner {{ $bannerIndex + 1 }}"></button>
                    @endfor
                </div>
            </div>
        </div>
    </section>

    @php
        $features = [
            ['url' => route('pages.academy'), 'title' => 'FEA Academy', 'text' => 'Chương trình đào tạo chuyên sâu', 'path' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['url' => route('pages.innovation-lab'), 'title' => 'Innovation Lab', 'text' => 'Thực hành dự án công nghệ mới', 'path' => 'm8 9-3 3 3 3m8-6 3 3-3 3m-2-10-4 14'],
            ['url' => route('pages.career-accelerator'), 'title' => 'Career Accelerator', 'text' => 'Định hướng và kết nối việc làm', 'path' => 'M13 10V3L4 14h7v7l9-11h-7Z'],
            ['url' => route('pages.corporate-training'), 'title' => 'Corporate Training', 'text' => 'Giải pháp đào tạo doanh nghiệp', 'path' => 'M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m-1-14h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5'],
        ];
        $statItems = [
            ['value' => $stats['courses'], 'label' => 'Khóa học', 'icon' => 'courses'],
            ['value' => $stats['students'], 'label' => 'Học viên', 'icon' => 'students'],
            ['value' => $stats['instructors'], 'label' => 'Giảng viên', 'icon' => 'instructors'],
            ['value' => $stats['categories'], 'label' => 'Danh mục', 'icon' => 'categories'],
        ];
    @endphp

    <section class="border-b border-slate-100 bg-white py-4 dark:border-slate-800 dark:bg-slate-950">
        <div class="ui-container grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($features as $feature)
                <a href="{{ $feature['url'] }}" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-slate-50 dark:hover:bg-slate-900">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0D5BD7] transition group-hover:bg-[#0D5BD7] group-hover:text-white dark:bg-blue-950/50 dark:text-blue-300"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['path'] }}" /></svg></span>
                    <span class="min-w-0"><strong class="block text-sm text-slate-900 dark:text-white">{{ $feature['title'] }}</strong><span class="line-clamp-1 text-[11px] text-slate-500 dark:text-slate-400">{{ $feature['text'] }}</span></span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-5 dark:bg-slate-950">
        <div class="ui-container">
            <div class="grid grid-cols-2 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-sm sm:grid-cols-4 dark:border-slate-800 dark:bg-slate-900/60">
                @foreach($statItems as $stat)
                    <div class="flex flex-col items-center justify-center gap-2 border-slate-200 px-3 py-5 text-center odd:border-r sm:border-r sm:last:border-r-0 dark:border-slate-800">
                        <span data-home-stat-icon="{{ $stat['icon'] }}" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-[#0D5BD7] dark:bg-blue-950/50 dark:text-blue-300" aria-hidden="true">
                            @switch($stat['icon'])
                                @case('courses')
                                    <svg class="h-7 w-7 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" focusable="false">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25m0-13C13.17 5.48 14.75 5 16.5 5S19.83 5.48 21 6.25v13C19.83 18.48 18.25 18 16.5 18s-3.33.48-4.5 1.25" />
                                    </svg>
                                    @break
                                @case('students')
                                    <svg class="h-7 w-7 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" focusable="false">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87m-2-11.99a4 4 0 0 1 0 7.75" />
                                    </svg>
                                    @break
                                @case('instructors')
                                    <svg class="h-7 w-7 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" focusable="false">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 9 9-5 9 5-9 5-9-5Zm4 2.22V16c0 1.1 2.24 2 5 2s5-.9 5-2v-4.78M21 9v6" />
                                    </svg>
                                    @break
                                @case('categories')
                                    <svg class="h-7 w-7 overflow-visible" fill="none" stroke="currentColor" viewBox="0 0 24 24" focusable="false">
                                        <rect x="3.5" y="3.5" width="6.5" height="6.5" rx="1" stroke-width="1.8" />
                                        <rect x="14" y="3.5" width="6.5" height="6.5" rx="1" stroke-width="1.8" />
                                        <rect x="3.5" y="14" width="6.5" height="6.5" rx="1" stroke-width="1.8" />
                                        <rect x="14" y="14" width="6.5" height="6.5" rx="1" stroke-width="1.8" />
                                    </svg>
                                    @break
                            @endswitch
                        </span>
                        <span><strong class="block text-xl font-black tabular-nums text-slate-950 dark:text-white">{{ number_format((int) $stat['value']) }}+</strong><span class="text-[10px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ $stat['label'] }}</span></span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.home.categories')
    @include('partials.home.course-section', ['sectionId' => 'free-courses', 'courses' => $freeCourses, 'variant' => 'free', 'title' => 'Khóa học miễn phí', 'subtitle' => 'Bắt đầu học ngay với những khóa học chất lượng hoàn toàn miễn phí.', 'alternate' => true])
    @include('partials.home.course-section', ['sectionId' => 'courses', 'courses' => $featuredCourses, 'variant' => 'featured', 'title' => 'Khóa học nổi bật', 'subtitle' => 'Các khóa học tiêu biểu được học viên quan tâm và đánh giá cao.', 'alternate' => false])
    @include('partials.home.testimonials')

    <section id="business" class="bg-white py-10 sm:py-14 lg:py-16 dark:bg-slate-950">
        <div class="ui-container">
            <div class="relative overflow-hidden rounded-3xl border border-blue-100 bg-gradient-to-r from-blue-50 via-white to-slate-50 p-6 shadow-sm sm:p-8 lg:p-10 dark:border-blue-900/50 dark:from-blue-950/50 dark:via-slate-900 dark:to-slate-900">
                <div class="absolute -right-16 -top-20 h-52 w-52 rounded-full bg-[#0D5BD7]/10 blur-3xl" aria-hidden="true"></div>
                <div class="relative grid items-center gap-7 lg:grid-cols-[1fr_auto]">
                    <div><p class="text-xs font-black uppercase tracking-widest text-[#0D5BD7] dark:text-blue-300">Doanh nghiệp & Đào tạo</p><h2 class="mt-2 max-w-3xl text-2xl font-black tracking-tight text-slate-950 sm:text-3xl dark:text-white">Nâng cao năng lực đội ngũ với lộ trình học tập có cấu trúc</h2><p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">Tổ chức khóa học, quản lý tiến độ và theo dõi kết quả học tập trong một nền tảng thống nhất, hiện đại và bảo mật.</p></div>
                    <a href="{{ route('pages.corporate-training') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#0D5BD7] px-6 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">Khám phá giải pháp đào tạo</a>
                </div>
            </div>
        </div>
    </section>
@endsection
