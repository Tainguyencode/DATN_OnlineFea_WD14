@extends('layouts.app')

@section('title', $course->title . ' - Website học online FEA')

@section('content')
@php
    $discountPrice = $course->discount_price ?? $course->sale_price;
    $price = $discountPrice ?? $course->price;
    $originalPrice = $discountPrice ? $course->price : null;
    $isFree = (float) $price <= 0;
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $typeLabels = ['video' => 'Video', 'document' => 'Tài liệu', 'quiz' => 'Quiz', 'assignment' => 'Bài tập'];
    $formatDuration = function ($value) {
        if (! $value) {
            return null;
        }

        $seconds = (int) $value;

        return $seconds >= 3600 ? gmdate('H:i:s', $seconds) : gmdate('i:s', $seconds);
    };
    $languageLabel = $course->language === 'vi' ? 'Tiếng Việt' : strtoupper((string) $course->language);
@endphp

<section class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_360px] lg:px-8 lg:py-14">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                @if($course->category)
                    <a href="{{ route('courses.index', ['category' => $course->category_id]) }}" class="rounded-full bg-indigo-500/15 px-3 py-1 font-bold text-indigo-200 ring-1 ring-indigo-400/30">
                        {{ $course->category->name }}
                    </a>
                @endif
                <span class="rounded-full bg-white/10 px-3 py-1 font-semibold text-slate-200">{{ $levelLabels[$course->level] ?? 'Mọi trình độ' }}</span>
                <span class="rounded-full bg-white/10 px-3 py-1 font-semibold text-slate-200">{{ $languageLabel }}</span>
            </div>

            <h1 class="mt-5 max-w-4xl text-3xl font-extrabold leading-tight tracking-tight sm:text-5xl">{{ $course->title }}</h1>
            <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300 sm:text-lg">{{ $course->short_description }}</p>

            <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-300">
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-500 text-sm font-extrabold text-white">
                        {{ strtoupper(substr($course->instructor?->name ?? 'F', 0, 1)) }}
                    </div>
                    <span>Giảng viên <strong class="text-white">{{ $course->instructor?->name ?? 'Fea Instructor' }}</strong></span>
                </div>
                <span class="hidden text-slate-600 sm:inline">•</span>
                <span>{{ $totalSections }} chương</span>
                <span class="hidden text-slate-600 sm:inline">•</span>
                <span>{{ $totalLessons }} bài học</span>
                <span class="hidden text-slate-600 sm:inline">•</span>
                <span>{{ $previewLessons }} bài xem thử</span>
            </div>
        </div>

        <aside class="lg:row-span-2">
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white text-slate-950 shadow-2xl shadow-indigo-950/40 dark:bg-[#161615] dark:text-white">
                <div class="relative aspect-video bg-gradient-to-br from-slate-900 via-indigo-900 to-violet-800">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-4xl font-extrabold text-white/80">Fea</div>
                    @endif

                    @if($course->preview_video)
                        <a href="{{ $course->preview_video }}" target="_blank"
                           class="absolute inset-0 flex items-center justify-center bg-slate-950/35 text-white transition hover:bg-slate-950/45">
                            <span class="flex h-16 w-16 items-center justify-center rounded-full bg-white/90 text-indigo-700 shadow-xl">
                                <svg class="ml-1 h-8 w-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </span>
                        </a>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-end gap-2">
                        <span class="{{ $isFree ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-300' }} text-3xl font-extrabold">
                            {{ $formatPrice($price) }}
                        </span>
                        @if($originalPrice && (float) $originalPrice > (float) $price)
                            <span class="pb-1 text-base font-semibold text-slate-400 line-through">{{ $formatPrice($originalPrice) }}</span>
                        @endif
                    </div>

                    <div class="mt-5 space-y-2 text-sm text-slate-600 dark:text-slate-300">
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                            <span>Chương học</span>
                            <strong>{{ $totalSections }}</strong>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                            <span>Bài học</span>
                            <strong>{{ $totalLessons }}</strong>
                        </div>
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 dark:bg-slate-900/70">
                            <span>Ngôn ngữ</span>
                            <strong>{{ $languageLabel }}</strong>
                        </div>
                    </div>

                    <div class="mt-5">
                        @if($canManageCourse)
                            <a href="{{ route('instructor.courses.curriculum', $course) }}" class="flex h-12 w-full items-center justify-center rounded-xl bg-emerald-600 text-sm font-extrabold text-white transition hover:bg-emerald-700 cursor-pointer">
                                Quản lý khóa học
                            </a>
                            <p class="mt-3 text-center text-xs text-slate-500 dark:text-slate-400">Bạn là giảng viên sở hữu khóa học này.</p>
                        @elseif($isEnrolled)
                            <a href="{{ route('student.courses') }}" class="flex h-12 w-full items-center justify-center rounded-xl bg-emerald-600 text-sm font-extrabold text-white transition hover:bg-emerald-700 cursor-pointer">
                                Vào học
                            </a>
                            <x-favorite-button :course="$course" :favorited="$isFavorited" :label="true" :block="true" class="mt-3" />
                            <p class="mt-3 text-center text-xs text-slate-500 dark:text-slate-400">Bạn đã đăng ký khóa học này.</p>
                        @elseif(auth()->check())
                            @if(auth()->user()->isStudent())
                                @if($isFree)
                                    <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                        @csrf
                                        <button type="submit" class="flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 text-sm font-extrabold text-white transition hover:bg-indigo-700 cursor-pointer">
                                            Đăng ký học miễn phí
                                        </button>
                                    </form>
                                    <x-favorite-button :course="$course" :favorited="$isFavorited" :label="true" :block="true" class="mt-3" />
                                    <p class="mt-3 text-center text-xs text-slate-500 dark:text-slate-400">Bài học không preview sẽ mở sau khi bạn đăng ký.</p>
                                @else
                                    <form method="POST" action="{{ route('student.cart.add', $course) }}">
                                        @csrf
                                        <button type="submit" class="flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 text-sm font-extrabold text-white transition hover:bg-indigo-700 cursor-pointer">
                                            Thêm vào giỏ hàng
                                        </button>
                                    </form>
                                    <x-favorite-button :course="$course" :favorited="$isFavorited" :label="true" :block="true" class="mt-3" />
                                    <p class="mt-3 text-center text-xs text-slate-500 dark:text-slate-400">Thanh toán trong giỏ hàng để mở toàn bộ khóa học.</p>
                                @endif
                            @else
                                <a href="{{ auth()->user()->dashboardUrl() }}" class="flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 text-sm font-extrabold text-white transition hover:bg-indigo-700">
                                    Vào Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="flex h-12 w-full items-center justify-center rounded-xl bg-indigo-600 text-sm font-extrabold text-white transition hover:bg-indigo-700 cursor-pointer">
                                Đăng ký học
                            </a>
                            <x-favorite-button :course="$course" :favorited="false" :label="true" :block="true" class="mt-3" />
                            <p class="mt-3 text-center text-xs text-slate-500 dark:text-slate-400">
                                Đã có tài khoản?
                                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline dark:text-indigo-300">Đăng nhập</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>

<section class="bg-slate-50 py-10 dark:bg-[#0a0a0a]">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[minmax(0,1fr)_320px] lg:px-8">
        <div class="space-y-8">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#161615]">
                <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white">Giới thiệu khóa học</h2>
                <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $course->description }}</div>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#161615]">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white">Nội dung khóa học</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $totalSections }} chương • {{ $totalLessons }} bài học</p>
                    </div>
                    @unless($canAccessFullCourse)
                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30">
                            Chưa đăng ký: chỉ mở bài xem thử
                        </span>
                    @endunless
                </div>

                <div class="mt-5 space-y-4">
                    @forelse($curriculumSections as $section)
                        <div class="overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800">
                            <div class="flex flex-col gap-1 bg-slate-50 px-4 py-3 dark:bg-slate-900/70 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-950 dark:text-white">{{ $section->title }}</h3>
                                    @if($section->description)
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $section->description }}</p>
                                    @endif
                                </div>
                                <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">{{ $section->lessons->count() }} bài</span>
                            </div>

                            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($section->lessons as $lesson)
                                    @php
                                        $canAccessLesson = $canAccessFullCourse || $lesson->is_preview;
                                        $duration = $formatDuration($lesson->duration ?? $lesson->duration_seconds);
                                        $hasVideoSource = $lesson->type === 'video' && ($lesson->video_path || $lesson->video_url);
                                    @endphp
                                    <div class="px-4 py-4">
                                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="rounded-full border border-slate-200 px-2.5 py-1 text-xs font-bold text-slate-600 dark:border-slate-700 dark:text-slate-300">
                                                        {{ $typeLabels[$lesson->type] ?? $lesson->type }}
                                                    </span>
                                                    @if($lesson->is_preview)
                                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30">
                                                            Xem thử
                                                        </span>
                                                    @endif
                                                    @unless($canAccessLesson)
                                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                                            Khóa
                                                        </span>
                                                    @endunless
                                                </div>

                                                <h4 class="mt-2 font-bold text-slate-950 dark:text-white">{{ $lesson->title }}</h4>

                                                @if($canAccessLesson && $lesson->content)
                                                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $lesson->content }}</p>
                                                @elseif(! $canAccessLesson)
                                                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Bạn cần đăng ký khóa học để xem bài học này.</p>
                                                @endif
                                            </div>

                                            <div class="flex shrink-0 flex-wrap items-center gap-2 text-sm">
                                                @if($duration)
                                                    <span class="rounded-lg bg-slate-100 px-3 py-2 font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $duration }}</span>
                                                @endif

                                                @if($canAccessLesson && $hasVideoSource)
                                                    <a href="{{ route('courses.lessons.show', [$course, $lesson]) }}" class="rounded-lg border border-indigo-200 px-3 py-2 font-bold text-indigo-700 transition hover:bg-indigo-50 dark:border-indigo-500/30 dark:text-indigo-300 dark:hover:bg-indigo-500/10">
                                                        Xem video
                                                    </a>
                                                @endif

                                                @if($canAccessLesson && $lesson->type === 'quiz')
                                                    <a href="{{ route('learn.lessons.quiz.show', [$course->slug, $lesson]) }}" class="rounded-lg border border-violet-200 px-3 py-2 font-bold text-violet-700 transition hover:bg-violet-50 dark:border-violet-500/30 dark:text-violet-300 dark:hover:bg-violet-500/10">
                                                        Làm quiz
                                                    </a>
                                                @endif

                                                @if($canAccessLesson && $lesson->document_file)
                                                    <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="rounded-lg border border-sky-200 px-3 py-2 font-bold text-sky-700 transition hover:bg-sky-50 dark:border-sky-500/30 dark:text-sky-300 dark:hover:bg-sky-500/10">
                                                        Tài liệu
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-5 text-sm text-slate-500 dark:text-slate-400">Chương này chưa có bài học.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
                            Khóa học chưa có nội dung hiển thị.
                        </div>
                    @endforelse
                </div>
            </article>

            @if($reviews->isNotEmpty())
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#161615]">
                    <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white">Đánh giá từ học viên</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @foreach($reviews as $review)
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                                        {{ strtoupper(substr($review->user?->name ?? 'H', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-950 dark:text-white">{{ $review->user?->name ?? 'Học viên' }}</div>
                                        <div class="text-xs font-semibold text-amber-500">{{ $review->rating }}/5 sao</div>
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </article>
            @endif

            <section>
                <h2 class="mb-6 text-2xl font-bold text-slate-900 dark:text-white">Hỏi đáp khóa học</h2>
                <div class="space-y-3">
                    @foreach(['Khóa học này phù hợp với ai?', 'Tôi có thể học thử trước khi đăng ký không?', 'Sau khi hoàn thành có chứng chỉ không?'] as $question)
                        <details class="rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                            <summary class="cursor-pointer list-none px-5 py-4 text-sm font-semibold text-slate-900 dark:text-white">{{ $question }}</summary>
                            <div class="px-5 pb-4 text-sm leading-6 text-slate-600 dark:text-slate-400">
                                Thông tin chi tiết được hiển thị theo nội dung khóa học và chính sách học tập hiện có trên hệ thống.
                            </div>
                        </details>
                    @endforeach
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#161615]">
                <h3 class="text-lg font-extrabold text-slate-950 dark:text-white">Giảng viên</h3>
                <div class="mt-4 flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xl font-extrabold text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300">
                        {{ strtoupper(substr($course->instructor?->name ?? 'F', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-slate-950 dark:text-white">{{ $course->instructor?->name ?? 'Fea Instructor' }}</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">Instructor</div>
                    </div>
                </div>
                @if($course->instructor?->bio)
                    <p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $course->instructor->bio }}</p>
                @endif

                @auth
                    @if(auth()->user()->isStudent())
                        <div class="mt-6 space-y-3">
                            <form method="POST" action="{{ route('student.cart.add', $course) }}">
                                @csrf
                                <button type="submit" class="ui-button-primary w-full">
                                    Thêm vào giỏ hàng
                                </button>
                            </form>
                            <form method="POST" action="{{ route('student.wishlist.toggle', $course->id) }}">
                                @csrf
                                <button type="submit" class="ui-button-secondary flex w-full items-center justify-center gap-2">
                                    <svg class="h-5 w-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0Z"></path></svg>
                                    Yêu thích
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ auth()->user()->dashboardUrl() }}" class="ui-button-primary mt-6 w-full">
                            Vào Dashboard
                        </a>
                    @endif
                @else
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('register.role', 'student') }}" class="ui-button-primary w-full">
                            Đăng ký để học ngay
                        </a>
                        <p class="text-center text-sm text-slate-500 dark:text-slate-400">Đã có tài khoản? <a href="{{ route('login') }}" class="font-semibold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200">Đăng nhập</a></p>
                    </div>
                @endauth

                <hr class="my-5 border-slate-200 dark:border-slate-800">

                <h4 class="mb-3 text-sm font-bold text-slate-900 dark:text-white">Khóa học này bao gồm:</h4>
                <ul class="space-y-3 text-sm text-slate-600 dark:text-slate-400">
                    <li class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0 1 21 8.618v6.764a1 1 0 0 1-1.447.894L15 14M5 18h8a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2Z"></path></svg>
                        {{ $totalLessons }} bài giảng chất lượng cao
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.752 11.168-3.197-2.132A1 1 0 0 0 10 9.87v4.263a1 1 0 0 0 1.555.832l3.197-2.132a1 1 0 0 0 0-1.664Z"></path></svg>
                        {{ $previewLessons }} bài học thử miễn phí
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 12 2 2 4-4M7.835 4.697a3.42 3.42 0 0 0 1.946-.806 3.42 3.42 0 0 1 4.438 0 3.42 3.42 0 0 0 1.946.806 3.42 3.42 0 0 1 3.138 3.138 3.42 3.42 0 0 0 .806 1.946 3.42 3.42 0 0 1 0 4.438 3.42 3.42 0 0 0-.806 1.946 3.42 3.42 0 0 1-3.138 3.138 3.42 3.42 0 0 0-1.946.806 3.42 3.42 0 0 1-4.438 0 3.42 3.42 0 0 0-1.946-.806 3.42 3.42 0 0 1-3.138-3.138 3.42 3.42 0 0 0-.806-1.946 3.42 3.42 0 0 1 0-4.438 3.42 3.42 0 0 0 .806-1.946 3.42 3.42 0 0 1 3.138-3.138Z"></path></svg>
                        Cấp chứng chỉ hoàn thành
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
                        Sở hữu khóa học trọn đời
                    </li>
                </ul>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#161615]">
                <h3 class="text-lg font-extrabold text-slate-950 dark:text-white">Thông tin khóa học</h3>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Trình độ</dt>
                        <dd class="font-bold text-slate-950 dark:text-white">{{ $levelLabels[$course->level] ?? 'Mọi trình độ' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Ngôn ngữ</dt>
                        <dd class="font-bold text-slate-950 dark:text-white">{{ $languageLabel }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Cập nhật</dt>
                        <dd class="font-bold text-slate-950 dark:text-white">{{ $course->updated_at?->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500 dark:text-slate-400">Học viên</dt>
                        <dd class="font-bold text-slate-950 dark:text-white">{{ $course->enrollment_count ?? 0 }}</dd>
                    </div>
                </dl>
            </div>
        </aside>
    </div>

    @if($relatedCourses->isNotEmpty())
        <div class="mx-auto mt-12 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white">Khóa học liên quan</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Một vài lựa chọn cùng danh mục để bạn tham khảo thêm.</p>
                </div>
                <a href="{{ route('courses.index') }}" class="hidden text-sm font-bold text-indigo-600 hover:underline dark:text-indigo-300 sm:inline">Xem tất cả</a>
            </div>
            <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach($relatedCourses as $related)
                    <x-course-card :course="$related" />
                @endforeach
            </div>
        </div>
    @endif
</section>
@endsection
