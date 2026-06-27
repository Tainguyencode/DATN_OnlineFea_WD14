@extends('layouts.app')

@section('title', $course->title . ' - EduPlatform')

@section('content')
@php
    $price = $course->sale_price ?? $course->price;
    $originalPrice = $course->sale_price ? $course->price : null;
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
@endphp

<div class="bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                @if($course->category)
                    <span class="text-indigo-300 text-sm font-medium">{{ $course->category->name }}</span>
                @endif
                <h1 class="text-3xl sm:text-4xl font-bold mt-2 mb-4">{{ $course->title }}</h1>
                <p class="text-slate-300 leading-relaxed mb-6">{{ Str::limit($course->description, 300) }}</p>

                <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                        </div>
                        <span>{{ $course->instructor->name }}</span>
                    </div>
                    <span>·</span>
                    <span>{{ $levelLabels[$course->level] ?? $course->level }}</span>
                    <span>·</span>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($course->rating_avg) ? 'text-amber-400' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                        <span class="ml-1">{{ number_format($course->rating_avg, 1) }} ({{ $course->rating_count }})</span>
                    </div>
                    <span>·</span>
                    <span>{{ $course->enrollment_count }} học viên</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 text-slate-900 shadow-xl h-fit">
                <div class="aspect-video bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl mb-4 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white/40" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
                <div class="mb-4">
                    @if($price == 0)
                        <span class="text-3xl font-bold text-emerald-600">Miễn phí</span>
                    @else
                        <span class="text-3xl font-bold text-indigo-600">{{ number_format($price, 0, ',', '.') }}đ</span>
                        @if($originalPrice)
                            <span class="text-lg text-slate-400 line-through ml-2">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                        @endif
                    @endif
                </div>
                <ul class="space-y-2 text-sm text-slate-600 mb-6">
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $totalLessons }} bài giảng
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $previewLessons }} bài học thử miễn phí
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Chứng chỉ hoàn thành
                    </li>
                </ul>
                @auth
                    @if(auth()->user()->isStudent())
                        <form method="POST" action="{{ route('student.cart.add', $course) }}" class="mb-3">
                            @csrf
                            <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition">
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                        <form method="POST" action="{{ route('student.wishlist.toggle', $course->id) }}">
                            @csrf
                            <button type="submit" class="w-full border border-slate-300 text-slate-700 font-medium py-2.5 rounded-xl hover:bg-slate-50 transition text-sm">
                                ♡ Yêu thích
                            </button>
                        </form>
                    @else
                        <a href="{{ auth()->user()->dashboardUrl() }}" class="block w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition text-center">
                            Vào Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="block w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition text-center">
                        Đăng ký để học ngay
                    </a>
                    <p class="text-xs text-slate-500 text-center mt-3">Đã có tài khoản? <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Đăng nhập</a></p>
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-10">
            {{-- Mô tả --}}
            <section>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Giới thiệu khóa học</h2>
                <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">{{ $course->description }}</div>
                @if($course->objectives)
                    <h3 class="text-lg font-semibold text-slate-900 mt-6 mb-3">Mục tiêu khóa học</h3>
                    <p class="text-slate-600 whitespace-pre-line">{{ $course->objectives }}</p>
                @endif
            </section>

            {{-- Nội dung --}}
            <section>
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Nội dung khóa học</h2>
                <div class="space-y-3">
                    @foreach($course->chapters as $chapter)
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <div class="bg-slate-50 px-5 py-3 font-semibold text-slate-900 flex justify-between">
                                <span>{{ $chapter->title }}</span>
                                <span class="text-sm text-slate-500 font-normal">{{ $chapter->lessons->count() }} bài</span>
                            </div>
                            <ul class="divide-y divide-slate-100">
                                @foreach($chapter->lessons as $lesson)
                                    <li class="px-5 py-3 flex items-center justify-between text-sm">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="text-slate-700">{{ $lesson->title }}</span>
                                            @if($lesson->is_preview)
                                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Học thử</span>
                                            @endif
                                        </div>
                                        <span class="text-slate-400">{{ gmdate('i:s', $lesson->duration_seconds) }}</span>
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
                <h2 class="text-2xl font-bold text-slate-900 mb-4">Đánh giá từ học viên</h2>
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="bg-white border border-slate-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">{{ $review->user->name }}</div>
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-slate-600 text-sm">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
            @endif
        </div>

        {{-- Giảng viên --}}
        <div>
            <div class="bg-white border border-slate-200 rounded-2xl p-6 sticky top-24">
                <h3 class="font-bold text-slate-900 mb-4">Giảng viên</h3>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xl font-bold">
                        {{ strtoupper(substr($course->instructor->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-slate-900">{{ $course->instructor->name }}</div>
                        <div class="text-sm text-slate-500">Giảng viên</div>
                    </div>
                </div>
                @if($course->instructor->bio)
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $course->instructor->bio }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Khóa học liên quan --}}
    @if($relatedCourses->isNotEmpty())
    <section class="mt-16">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Khóa học liên quan</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedCourses as $related)
                <x-course-card :course="$related" />
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
