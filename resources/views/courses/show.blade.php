@extends('layouts.app')

@section('title', $course->title . ' - Website học online FEA')

@section('content')
@php
    $price = $course->sale_price ?? $course->price;
    $originalPrice = $course->sale_price ? $course->price : null;
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
@endphp

<div class="border-b border-slate-800 bg-slate-900 text-white">
    <div class="ui-container py-10 lg:py-14">
        <div class="pr-0 lg:w-2/3 lg:pr-8">
            <div class="mb-4 text-sm font-semibold text-white/70">
                <a href="{{ route('home') }}" class="hover:text-white">Trang chủ</a>
                <span class="mx-2">/</span>
                <a href="{{ route('home') }}#courses" class="hover:text-white">Khóa học</a>
            </div>
            @if($course->category)
                <div class="mb-4">
                    <span class="ui-badge-primary">
                        {{ $course->category->name }}
                    </span>
                </div>
            @endif
            
            <h1 class="mb-5 mt-2 text-3xl font-bold leading-tight sm:text-4xl lg:text-5xl">{{ $course->title }}</h1>
            <p class="mb-6 text-lg leading-8 text-white/80">{{ Str::limit($course->description, 300) }}</p>

            <div class="flex flex-wrap items-center gap-x-5 gap-y-3 text-sm text-white/75">
                <div class="flex items-center gap-1">
                    <span class="mr-1 font-bold text-amber-400">{{ number_format($course->rating_avg, 1) }}</span>
                    <div class="flex items-center">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-4 w-4 {{ $i <= round($course->rating_avg) ? 'text-amber-400' : 'text-white/25' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <span class="ml-1">({{ $course->rating_count }} đánh giá)</span>
                </div>
                <span class="hidden text-white/30 sm:block">|</span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>{{ number_format($course->enrollment_count) }} học viên</span>
                </div>
                <span class="hidden text-white/30 sm:block">|</span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>{{ $levelLabels[$course->level] ?? $course->level }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ui-container py-10">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        
        <div class="space-y-10 lg:col-span-8">
            <section>
                <div class="relative flex aspect-video items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="text-center">
                            <svg class="mx-auto mb-3 h-16 w-16 text-[#0056D2] dark:text-blue-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Video giới thiệu khóa học</p>
                        </div>
                    @endif
                </div>
            </section>

            
            {{-- Giới thiệu & Mục tiêu --}}
            <section>
                <h2 class="mb-5 text-2xl font-bold text-slate-900 dark:text-white">Giới thiệu khóa học</h2>
                <div class="max-w-none whitespace-pre-line text-base leading-8 text-slate-700 dark:text-slate-300">{{ $course->description }}</div>
                
                @if($course->objectives)
                    <div class="mt-8 rounded-xl border border-slate-200 bg-slate-50 p-6 dark:border-slate-700 dark:bg-slate-900">
                        <h3 class="mb-3 text-lg font-bold text-slate-900 dark:text-white">Mục tiêu khóa học</h3>
                        <p class="whitespace-pre-line leading-7 text-slate-700 dark:text-slate-300">{{ $course->objectives }}</p>
                    </div>
                @endif
            </section>

            {{-- Nội dung bài giảng --}}
            <section>
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Nội dung khóa học</h2>
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $totalSections }} chương • {{ $totalLessons }} bài học</span>
                </div>
                
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                    @foreach($curriculumSections as $section)
                        <div class="border-b border-slate-200 last:border-b-0 dark:border-slate-800">
                            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4 font-bold text-slate-900 dark:border-slate-800 dark:bg-slate-800/60 dark:text-white">
                                <span>{{ $section->title }}</span>
                                <span class="rounded border border-slate-200 bg-white px-2.5 py-1 text-sm font-medium text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">{{ $section->lessons->count() }} bài</span>
                            </div>
                            <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                                @foreach($section->lessons as $lesson)
                                    @php
                                        $canOpenLesson = $canAccessFullCourse || $lesson->is_preview;
                                        $lessonUrl = $canOpenLesson ? route('courses.lessons.show', [$course, $lesson]) : null;
                                    @endphp
                                    <li class="flex items-center justify-between px-5 py-3.5 text-sm transition duration-200 hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                        <div class="flex min-w-0 flex-1 items-center gap-3">
                                            <svg class="h-5 w-5 flex-shrink-0 text-[#0056D2] dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.752 11.168-3.197-2.132A1 1 0 0 0 10 9.87v4.263a1 1 0 0 0 1.555.832l3.197-2.132a1 1 0 0 0 0-1.664Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            @if($lessonUrl)
                                                <a href="{{ $lessonUrl }}" class="truncate font-medium text-slate-700 hover:text-[#0056D2] dark:text-slate-300 dark:hover:text-blue-300">{{ $lesson->title }}</a>
                                            @else
                                                <span class="truncate font-medium text-slate-700 dark:text-slate-300">{{ $lesson->title }}</span>
                                            @endif
                                            @if($lesson->is_preview)
                                                <span class="ui-badge-success">Học thử</span>
                                            @endif
                                        </div>
                                        <span class="ml-3 shrink-0 font-mono text-xs text-slate-500 dark:text-slate-400">{{ gmdate('i:s', $lesson->duration_seconds ?: 0) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Đánh giá --}}
            @if($reviews->isNotEmpty())
            <section>
                <h2 class="mb-6 text-2xl font-bold text-slate-900 dark:text-white">Đánh giá từ học viên</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($reviews as $review)
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                            <div class="mb-3 flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-[#0056D2] dark:bg-blue-950/50 dark:text-blue-300">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $review->user->name }}</div>
                                    <div class="mt-0.5 flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'text-amber-500' : 'text-slate-300 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
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

        <div class="relative lg:col-span-4">
            <div class="sticky top-24 space-y-6">
                
                {{-- Thẻ Mua hàng --}}
                <div class="relative z-10 rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-md dark:border-slate-700 dark:bg-slate-900 dark:text-white lg:-mt-32">
                    <div class="relative mb-6 flex aspect-video items-center justify-center overflow-hidden rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                        @else
                            <svg class="h-16 w-16 text-[#0056D2] dark:text-blue-300" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        @endif
                    </div>
                    
                    <div class="mb-6">
                        @if($price == 0)
                            <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">Miễn phí</span>
                        @else
                            <div class="flex items-end gap-2">
                                <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($price, 0, ',', '.') }}đ</span>
                                @if($originalPrice)
                                    <span class="mb-1 text-lg text-slate-500 line-through dark:text-slate-400">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @auth
                        @if(auth()->user()->isStudent())
                            <div class="mb-6 space-y-3">
                                @if($isEnrolled && $learningEntryUrl)
                                    <a href="{{ $learningEntryUrl }}" class="ui-button-primary flex w-full items-center justify-center gap-2">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                        Vào học ngay
                                    </a>
                                    <p class="text-center text-xs text-emerald-600 dark:text-emerald-400">Bạn đã sở hữu khóa học này</p>
                                @else
                                    <form method="POST" action="{{ route('student.cart.add', $course) }}">
                                        @csrf
                                        <button type="submit" class="ui-button-primary w-full">
                                            Thêm vào giỏ hàng
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('student.wishlist.toggle', $course->id) }}">
                                    @csrf
                                    <button type="submit" class="ui-button-secondary flex w-full items-center justify-center gap-2">
                                        <svg class="h-5 w-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 0 0 0 6.364L12 20.364l7.682-7.682a4.5 4.5 0 0 0-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 0 0-6.364 0Z"></path></svg>
                                        Yêu thích
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ auth()->user()->dashboardUrl() }}" class="ui-button-primary mb-6 w-full">
                                Vào Dashboard
                            </a>
                        @endif
                    @else
                        <div class="mb-6 space-y-3">
                            <a href="{{ route('register') }}" class="ui-button-primary w-full">
                                Đăng ký để học ngay
                            </a>
                            <p class="text-center text-sm text-slate-500 dark:text-slate-400">Đã có tài khoản? <a href="{{ route('login') }}" class="font-semibold text-[#0056D2] hover:text-[#0046B8] dark:text-blue-300 dark:hover:text-blue-200">Đăng nhập</a></p>
                        </div>
                    @endauth

                    <hr class="mb-5 border-slate-200 dark:border-slate-800">

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

                {{-- Thẻ Giảng viên --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <h3 class="mb-5 font-bold text-slate-900 dark:text-white">Về giảng viên</h3>
                    <div class="mb-4 flex items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xl font-bold text-[#0056D2] dark:bg-blue-950/50 dark:text-blue-300">
                            {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-lg font-bold text-slate-900 dark:text-white">{{ $course->instructor->name }}</div>
                            <div class="text-sm font-medium text-[#0056D2] dark:text-blue-300">Giảng viên FEA</div>
                        </div>
                    </div>
                    @if($course->instructor->bio)
                        <p class="text-sm leading-6 text-slate-600 dark:text-slate-400">{{ $course->instructor->bio }}</p>
                    @endif
                </div>
                
            </div>
        </div>
    </div>

    {{-- Khóa học liên quan --}}
    @if($relatedCourses->isNotEmpty())
    <div class="mt-20 border-t border-slate-200 pt-10 dark:border-slate-800">
        <h2 class="mb-8 text-2xl font-bold text-slate-900 dark:text-white">Khóa học liên quan</h2>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($relatedCourses as $related)
                <x-course-card :course="$related" />
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection