@extends('layouts.app')

@section('title', 'Website học online FEA')

@section('content')
<section class="bg-white py-4 dark:bg-slate-950">
    <div class="ui-container">
        <div class="relative min-h-[360px] overflow-hidden rounded-2xl border border-slate-200 bg-blue-50 shadow-sm dark:border-slate-800 dark:bg-slate-900 lg:min-h-[390px]">
            <img src="{{ asset('images/learning-hero-banner.png') }}" alt="Học trực tuyến cùng Website học online FEA" class="absolute inset-0 h-full w-full object-cover">

            <button type="button" class="absolute left-4 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition duration-200 hover:text-[#0056D2] md:flex" aria-label="Banner trước">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
            </button>
            <button type="button" class="absolute right-4 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition duration-200 hover:text-[#0056D2] md:flex" aria-label="Banner tiếp theo">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
            </button>

            <div class="relative z-10 flex min-h-[360px] items-center px-6 py-10 sm:px-10 lg:min-h-[390px] lg:px-24">
                <div class="max-w-xl">
                    <h1 class="mb-5 text-4xl font-bold leading-tight tracking-tight text-slate-950 sm:text-5xl">
                        {{ $banner['title'] ?? 'Nền tảng học tập chuyên nghiệp, đột phá tương lai' }}
                    </h1>
                    <p class="mb-6 max-w-lg text-base leading-7 text-slate-700">
                        {{ $banner['subtitle'] ?? 'Phát triển kỹ năng thế kỷ 21 cùng chương trình học trực tuyến hiện đại, rõ ràng và dễ theo dõi.' }}
                    </p>

                    <form method="GET" action="{{ route('home') }}#courses" class="mb-5 grid gap-3 sm:grid-cols-[minmax(0,1fr)_112px]">
                        <label class="relative">
                            <span class="sr-only">Tìm khóa học</span>
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                            <input type="search" name="search" value="{{ request('search') }}" placeholder="Bạn muốn học gì hôm nay?" class="h-12 w-full rounded-lg border border-slate-300 bg-white pl-12 pr-4 text-sm text-slate-900 outline-none transition duration-200 focus:border-[#0056D2] focus:ring-2 focus:ring-[#0056D2]">
                        </label>
                        <button type="submit" class="ui-button-primary h-12 px-5">Tìm kiếm</button>
                    </form>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="#courses" class="ui-button-primary">Khám phá khóa học</a>
                        <a href="{{ route('register.role', 'student') }}" class="ui-button-secondary bg-white">Bắt đầu học</a>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-[#0056D2]"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
            </div>
        </div>
    </div>
</section>

<section class="border-b border-slate-200 bg-white py-8 dark:border-slate-800 dark:bg-slate-950">
    <div class="ui-container">
        <p class="mb-5 text-center text-sm font-medium text-slate-500 dark:text-slate-400">Được thiết kế cho sinh viên, giảng viên và đội ngũ đào tạo hiện đại</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['title' => 'FEA Academy', 'desc' => 'Chương trình đào tạo chuyên sâu'],
                ['title' => 'Innovation Lab', 'desc' => 'Thực hành dự án công nghệ mới'],
                ['title' => 'Career Accelerator', 'desc' => 'Hỗ trợ định hướng và kết nối việc làm'],
                ['title' => 'Corporate Training', 'desc' => 'Giải pháp đào tạo cho doanh nghiệp'],
            ] as $partner)
                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[#0056D2] dark:bg-blue-950/50 dark:text-blue-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $partner['title'] }}</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $partner['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="border-b border-slate-200 bg-slate-50 py-8 dark:border-slate-800 dark:bg-slate-900">
    <div class="ui-container">
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="flex items-center justify-center gap-4 border-r border-slate-200 px-4 dark:border-slate-800">
                <div class="hidden h-12 w-12 items-center justify-center rounded-xl bg-white text-[#0056D2] shadow-sm sm:flex dark:bg-slate-950">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['courses'] }}+</div>
                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">Khóa học</div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-4 border-r border-slate-200 px-4 dark:border-slate-800">
                <div class="hidden h-12 w-12 items-center justify-center rounded-xl bg-white text-[#0056D2] shadow-sm sm:flex dark:bg-slate-950">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['students'] }}+</div>
                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">Học viên</div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-4 border-r border-slate-200 px-4 dark:border-slate-800">
                <div class="hidden h-12 w-12 items-center justify-center rounded-xl bg-white text-[#0056D2] shadow-sm sm:flex dark:bg-slate-950">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5Zm0 0 6.16-3.422a12.083 12.083 0 0 1 .665 6.479A11.952 11.952 0 0 0 12 20.055a11.952 11.952 0 0 0-6.824-2.998 12.078 12.078 0 0 1 .665-6.479L12 14Z"/></svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['instructors'] }}+</div>
                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">Giảng viên</div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-4 px-4">
                <div class="hidden h-12 w-12 items-center justify-center rounded-xl bg-white text-[#0056D2] shadow-sm sm:flex dark:bg-slate-950">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z"/></svg>
                </div>
                <div>
                    <div class="text-3xl font-bold text-slate-900 dark:text-white">{{ $categories->count() }}+</div>
                    <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">Danh mục</div>
                </div>
            </div>
        </div>
    </div>
</section>

@if($featuredCourses->isNotEmpty())
<section class="ui-section">
    <div class="ui-container">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Khóa học nổi bật</h2>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Tuyển chọn các khóa học được đánh giá cao nhất trên hệ thống.</p>
        </div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($featuredCourses as $course)
                <x-course-card :course="$course" />
            @endforeach
        </div>
    </div>
</section>
@endif

<section id="categories" class="bg-white py-16 dark:bg-slate-950">
    <div class="ui-container">
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Danh mục môn học</h2>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Khám phá các môn học giúp bạn phát triển kỹ năng chuyên môn.</p>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($categories as $category)
                <a href="{{ route('courses.category', $category->slug) }}"
                   class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:border-blue-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-900 dark:hover:border-blue-700">
                    <div class="mb-3 flex items-center gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[#0056D2] dark:bg-blue-950/50 dark:text-blue-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 transition duration-200 group-hover:text-[#0056D2] dark:text-white dark:group-hover:text-blue-300">{{ $category->name }}</h3>
                    </div>
                    <p class="flex-grow text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $category->description ?? 'Khóa học chất lượng do đội ngũ giảng viên biên soạn.' }}</p>
                </a>
            @endforeach
        </div>
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

        <form method="GET" action="{{ route('home') }}#courses" class="mb-8 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tên khóa học..."
                           class="ui-input">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Danh mục</label>
                    <select name="category" class="ui-select cursor-pointer">
                        <option value="">Tất cả</option>
                        @foreach($categories as $parent)
                            <option value="{{ $parent->slug }}" @selected($selectedCategory?->id === $parent->id)>Tất cả {{ $parent->name }}</option>
                            <optgroup label="{{ $parent->name }}">
                                @foreach($parent->children as $cat)
                                    <option value="{{ $cat->slug }}" @selected($selectedCategory?->id === $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
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
                    <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Sắp xếp theo</label>
                    <select name="sort" class="ui-select cursor-pointer">
                        <option value="newest" @selected(request('sort', 'newest') == 'newest')>Mới nhất</option>
                        <option value="rating" @selected(request('sort') == 'rating')>Đánh giá cao</option>
                        <option value="popular" @selected(request('sort') == 'popular')>Phổ biến</option>
                        <option value="price_asc" @selected(request('sort') == 'price_asc')>Giá tăng dần</option>
                        <option value="price_desc" @selected(request('sort') == 'price_desc')>Giá giảm dần</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-3 border-t border-slate-200 pt-4 dark:border-slate-800">
                <a href="{{ route('home') }}#courses" class="text-sm font-medium text-slate-500 transition duration-200 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200">
                    Xóa bộ lọc
                </a>
                <button type="submit" class="ui-button-primary">
                    Áp dụng lọc
                </button>
            </div>
        </form>

        @if($courses->isEmpty())
            <div class="ui-empty">
                <svg class="mx-auto mb-3 h-12 w-12 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                <p class="text-base font-bold text-slate-900 dark:text-white">Không tìm thấy khóa học nào</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Vui lòng thay đổi tiêu chí tìm kiếm và thử lại.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
            <div class="mt-10">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</section>

@if($learningPaths->isNotEmpty())
<section id="paths" class="border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950">
    <div class="ui-container">
        <div class="mb-10 max-w-2xl">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Lộ trình học tập chuyên biệt</h2>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Học theo trình tự bài bản, giúp tiết kiệm thời gian và định hướng rõ ràng mục tiêu công việc hoặc đồ án.</p>
        </div>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            @foreach($learningPaths as $path)
                <div class="flex flex-col rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
                    <div class="mb-4">
                        <span class="ui-badge-primary">
                            {{ is_array($path->course_ids) ? count($path->course_ids) : 0 }} khóa học
                        </span>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-slate-900 dark:text-white">{{ $path->title }}</h3>
                    <p class="flex-grow text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $path->description ?? 'Lộ trình bài bản giúp sinh viên củng cố kiến thức từ nền tảng đến chuyên sâu.' }}</p>
                    <a href="#" class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200">
                        Xem chi tiết lộ trình
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 8 4 4m0 0-4 4m4-4H3"></path></svg>
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
                <p class="mb-6 text-slate-500 dark:text-slate-400">Tích lũy điểm số và mở khóa các danh hiệu học tập.</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($badges as $badge)
                        <div class="flex items-start gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15.5 8.5 18l1.2-4.1-3.3-2.5 4.2-.1L12 7.2l1.4 4.1 4.2.1-3.3 2.5 1.2 4.1-3.5-2.5Z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $badge->name }}
                                </h3>
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $badge->description }}</p>
                                <span class="mt-2 inline-block rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $badge->points_required }} điểm
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="border-b border-slate-200 bg-slate-50 px-5 py-4 dark:border-slate-800 dark:bg-slate-800/60">
                        <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900 dark:text-white">
                            Bảng xếp hạng tuần
                        </h3>
                    </div>
                    <div class="divide-y divide-slate-200 p-2 dark:divide-slate-800">
                        <div class="flex items-center justify-between rounded-lg bg-amber-50 p-3 dark:bg-amber-900/30">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-amber-500 text-sm font-bold text-white">1</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-white">Nguyễn Hoàng Nam</span>
                            </div>
                            <span class="text-sm font-bold text-amber-600 dark:text-amber-300">450 pts</span>
                        </div>
                        <div class="flex items-center justify-between p-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-700 dark:bg-slate-700 dark:text-slate-200">2</span>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Trần Thị Lan</span>
                            </div>
                            <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">380 pts</span>
                        </div>
                        <div class="flex items-center justify-between p-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-700 dark:bg-slate-700 dark:text-slate-200">3</span>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Vũ Hoàng Long</span>
                            </div>
                            <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">320 pts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="business" class="border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950">
    <div class="ui-container">
        <div class="grid items-center gap-8 rounded-2xl border border-slate-200 bg-slate-50 p-8 dark:border-slate-700 dark:bg-slate-900 lg:grid-cols-[1fr_360px]">
            <div>
                <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-[#0056D2] dark:text-blue-300">Doanh nghiệp</p>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Nâng cao năng lực đội ngũ với lộ trình học tập có cấu trúc</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-400">Tổ chức khóa học, quản lý tiến độ và theo dõi kết quả học tập trong một nền tảng thống nhất.</p>
            </div>
            <a href="{{ route('register.role', 'instructor') }}" class="ui-button-primary justify-center">Bắt đầu triển khai</a>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-16 dark:bg-slate-900">
    <div class="ui-container">
        <div class="mb-8 max-w-2xl">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Học viên nói gì về FEA Learning</h2>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Các phản hồi ngắn giúp bạn hình dung trải nghiệm học tập trên nền tảng.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach([
                ['name' => 'Minh Anh', 'text' => 'Nội dung rõ ràng, dễ theo dõi và giúp mình biết nên học gì tiếp theo.'],
                ['name' => 'Quốc Huy', 'text' => 'Dashboard tiến độ rất hữu ích khi học nhiều khóa cùng lúc.'],
                ['name' => 'Lan Phương', 'text' => 'Giao diện gọn, tập trung vào bài học và không gây rối mắt.'],
            ] as $testimonial)
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-950">
                    <p class="text-sm leading-6 text-slate-600 dark:text-slate-400">“{{ $testimonial['text'] }}”</p>
                    <p class="mt-4 text-sm font-semibold text-slate-900 dark:text-white">{{ $testimonial['name'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if($faqs->isNotEmpty())
<section id="faq" class="border-t border-slate-200 bg-white py-16 dark:border-slate-800 dark:bg-slate-950">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Câu hỏi thường gặp</h2>
            <p class="mt-2 text-slate-500 dark:text-slate-400">Giải đáp nhanh các thắc mắc về lớp học trực tuyến.</p>
        </div>
        <div class="space-y-3">
            @foreach($faqs as $faq)
                <details class="group rounded-xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900">
                    <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 font-bold text-slate-900 dark:text-white">
                        {{ $faq->question }}
                        <svg class="h-5 w-5 text-slate-500 transition-transform group-open:rotate-180 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/></svg>
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
        <p class="mx-auto mb-8 max-w-xl text-white/85">Đăng ký tài khoản sinh viên miễn phí trên nền tảng của chúng tôi ngay hôm nay để trải nghiệm môi trường học tập hiện đại.</p>
        <a href="{{ route('register.role', 'student') }}" class="inline-flex items-center justify-center rounded-lg border border-white bg-white px-8 py-3 font-medium text-[#0056D2] transition duration-200 hover:bg-blue-50">
            Tạo tài khoản miễn phí
        </a>
    </div>
</section>
@endguest
@endsection
