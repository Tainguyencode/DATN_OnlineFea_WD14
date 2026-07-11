@extends('layouts.app')

@section('title', 'Khóa học - Fea LMS')

@section('content')
@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
    $pricingOptions = [
        '' => 'Tất cả mức giá',
        'free' => 'Miễn phí',
        'paid' => 'Trả phí',
    ];
    $categoryCount = $categories->sum(fn ($parent) => $parent->children->count());
@endphp

<section class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:px-8 lg:py-16">
        <div>
            <span class="inline-flex rounded-full border border-indigo-400/30 bg-indigo-500/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-indigo-200">
                Fea Course Catalog
            </span>
            <h1 class="mt-5 max-w-3xl text-4xl font-extrabold tracking-tight sm:text-5xl">
                Khám phá khóa học đang mở trên Fea LMS
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                Tìm khóa học phù hợp theo danh mục, trình độ và mức giá. Tất cả khóa học tại đây đã được admin duyệt và sẵn sàng cho học viên đăng ký.
            </p>
            <div class="mt-7 flex flex-wrap gap-3 text-sm text-slate-300">
                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">{{ $courses->total() }} khóa học đã xuất bản</span>
                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">{{ $categoryCount }} danh mục</span>
                <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">Học thử với bài preview</span>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/10 p-5 shadow-2xl shadow-indigo-950/40 backdrop-blur">
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-white/10 p-4">
                    <span class="text-xs font-semibold uppercase tracking-wide text-indigo-200">Learning</span>
                    <strong class="mt-2 block text-2xl">Online</strong>
                </div>
                <div class="rounded-xl bg-white/10 p-4">
                    <span class="text-xs font-semibold uppercase tracking-wide text-emerald-200">Progress</span>
                    <strong class="mt-2 block text-2xl">Theo dõi</strong>
                </div>
                <div class="col-span-2 rounded-xl bg-slate-950/60 p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-200">Khóa học mới cập nhật</span>
                        <span class="text-indigo-200">Live</span>
                    </div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-white/10">
                        <div class="h-full w-3/4 rounded-full bg-gradient-to-r from-indigo-400 to-violet-400"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-10 dark:bg-[#0a0a0a]">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('courses.index') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-[#161615] sm:p-5">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1.4fr)_minmax(180px,.8fr)_minmax(170px,.7fr)_minmax(160px,.7fr)_auto]">
                <label class="block">
                    <span class="sr-only">Tìm kiếm khóa học</span>
                    <input type="search" name="search" value="{{ $search }}"
                           placeholder="Tìm theo tên khóa học..."
                           class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                </label>

                <select name="category" class="h-11 rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $parent)
                        <option value="{{ $parent->slug }}" @selected($selectedCategory?->id === $parent->id)>
                            Tất cả {{ $parent->name }}
                        </option>
                        <optgroup label="{{ $parent->name }}">
                            @foreach($parent->children as $category)
                                <option value="{{ $category->slug }}" @selected($selectedCategory?->id === $category->id)>
                                    {{ $category->name }} ({{ $category->courses_count }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>

                <select name="level" class="h-11 rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                    <option value="">Tất cả trình độ</option>
                    @foreach($levelOptions as $value => $label)
                        <option value="{{ $value }}" @selected($level === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="pricing" class="h-11 rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-slate-800 dark:bg-slate-950 dark:text-white">
                    @foreach($pricingOptions as $value => $label)
                        <option value="{{ $value }}" @selected($pricing === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="h-11 flex-1 rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 lg:flex-none">
                        Lọc
                    </button>
                    <a href="{{ route('courses.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-800 dark:text-slate-300 dark:hover:bg-slate-900">
                        Xóa
                    </a>
                </div>
            </div>
        </form>

        <div class="mt-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-950 dark:text-white">Danh sách khóa học</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Hiển thị {{ $courses->firstItem() ?? 0 }}-{{ $courses->lastItem() ?? 0 }} trong {{ $courses->total() }} khóa học phù hợp.
                </p>
            </div>
        </div>

        @if($courses->isEmpty())
            <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-800 dark:bg-[#161615]">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-950 dark:text-white">Chưa tìm thấy khóa học phù hợp</h3>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Thử đổi từ khóa, danh mục hoặc mức giá để xem thêm lựa chọn.</p>
                <a href="{{ route('courses.index') }}" class="mt-5 inline-flex rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700">
                    Xem tất cả khóa học
                </a>
            </div>
        @else
            <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($courses as $course)
                    @php
                        $discountPrice = $course->discount_price ?? $course->sale_price;
                        $price = $discountPrice ?? $course->price;
                        $originalPrice = $discountPrice ? $course->price : null;
                        $lessonCount = $course->lessons_count ?? 0;
                        $levelLabel = $levelOptions[$course->level] ?? 'Mọi trình độ';
                        $isFavorited = (bool) ($course->is_favorited ?? false);
                    @endphp
                    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl hover:shadow-indigo-500/10 dark:border-slate-800 dark:bg-[#161615] dark:hover:border-indigo-500/50">
                        <div class="relative aspect-video overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-900 to-violet-800">
                            <a href="{{ route('courses.show', $course->slug) }}" class="block h-full" aria-label="Xem chi tiết {{ $course->title }}">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-3xl font-extrabold text-white/80">Fea</div>
                                @endif
                            </a>
                            <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-bold text-slate-900 backdrop-blur">
                                {{ $levelLabel }}
                            </span>
                            <x-favorite-button :course="$course" :favorited="$isFavorited" class="absolute right-3 top-3 z-20" />
                        </div>

                        <div class="flex flex-1 flex-col p-5">
                            @if($course->category)
                                <a href="{{ route('courses.category', $course->category->slug) }}" class="text-xs font-bold uppercase tracking-wide text-indigo-600 transition hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                    {{ $course->category->full_name }}
                                </a>
                            @endif
                            <h3 class="mt-2 line-clamp-2 text-lg font-extrabold leading-snug text-slate-950 transition group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-300">
                                <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
                            </h3>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                                {{ $course->short_description ?: Str::limit($course->description, 120) }}
                            </p>

                            <div class="mt-4 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                    {{ strtoupper(substr($course->instructor?->name ?? 'F', 0, 1)) }}
                                </div>
                                <span class="truncate">{{ $course->instructor?->name ?? 'Giảng viên Fea' }}</span>
                            </div>

                            <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-900/60">
                                    <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Bài học</span>
                                    <strong class="mt-1 block text-slate-950 dark:text-white">{{ $lessonCount }}</strong>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-900/60">
                                    <span class="block text-xs font-semibold text-slate-500 dark:text-slate-400">Giá</span>
                                    <strong class="mt-1 block text-slate-950 dark:text-white">{{ $formatPrice($price) }}</strong>
                                    @if($originalPrice && (float) $originalPrice > (float) $price)
                                        <span class="text-xs text-slate-400 line-through">{{ $formatPrice($originalPrice) }}</span>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('courses.show', $course->slug) }}" class="mt-5 inline-flex h-11 items-center justify-center rounded-xl bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-slate-950 dark:hover:bg-indigo-200">
                                Xem chi tiết
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
