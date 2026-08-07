<x-student-layout title="Khóa học yêu thích" page-title="Khóa học yêu thích">
@php
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
@endphp

{{-- Context-aware Breadcrumb & Back Navigation (STU-FE-13) --}}
<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('student.dashboard') }}" class="hover:text-indigo-600 font-medium">Học viên</a>
        <span>/</span>
        <a href="{{ route('student.profile') }}" class="hover:text-indigo-600 font-medium">Hồ sơ cá nhân</a>
        <span>/</span>
        <span class="font-bold text-slate-800 dark:text-slate-200">Khóa học yêu thích</span>
    </nav>

    <a href="{{ route('student.profile') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
        ← Quay lại Hồ sơ cá nhân
    </a>
</div>

@if($items->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-50 text-rose-500 dark:bg-rose-950/30 dark:text-rose-400">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/>
            </svg>
        </div>
        <h2 class="mt-4 text-lg font-bold text-slate-950 dark:text-white">Bạn chưa có khóa học yêu thích nào</h2>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Lưu lại những khóa học bạn quan tâm để dễ dàng theo dõi và đăng ký học sau.</p>
        <a href="{{ route('courses.index') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl bg-indigo-600 px-6 text-sm font-bold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
            Khám phá khóa học ngay
        </a>
    </div>
@else
    <div class="mb-4 flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
            Đang lưu <strong class="text-slate-900 dark:text-white">{{ $items->total() }}</strong> khóa học yêu thích
        </span>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($items as $item)
            @php
                $course = $item->course;
                $discountPrice = $course->discount_price ?? $course->sale_price;
                $price = $discountPrice ?? $course->price;
                $originalPrice = $discountPrice ? $course->price : null;
                $lessonCount = $course->lessons_count ?? 0;
                $rating = (float) ($course->rating_avg ?? 0);
            @endphp

            <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-200 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
                <a href="{{ route('courses.show', $course->slug) }}" class="block relative aspect-video overflow-hidden bg-slate-900">
                    <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy">
                    <span class="absolute left-3 top-3 rounded-full bg-slate-950/80 backdrop-blur px-2.5 py-1 text-[11px] font-bold text-white">
                        {{ $levelLabels[$course->level] ?? 'Mọi trình độ' }}
                    </span>
                    @if($discountPrice && (float)$discountPrice < (float)$course->price)
                        <span class="absolute right-3 top-3 rounded-full bg-rose-500 px-2.5 py-1 text-[10px] font-extrabold text-white uppercase tracking-wider">
                            Giảm giá
                        </span>
                    @endif
                </a>

                <div class="flex flex-1 flex-col p-5">
                    @if($course->category)
                        <a href="{{ route('courses.category', $course->category->slug) }}" class="text-xs font-bold uppercase tracking-wide text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                            {{ $course->category->name }}
                        </a>
                    @endif

                    <h3 class="mt-2 line-clamp-2 text-base font-extrabold leading-snug text-slate-950 dark:text-white">
                        <a href="{{ route('courses.show', $course->slug) }}" class="transition hover:text-indigo-600 dark:hover:text-indigo-400">{{ $course->title }}</a>
                    </h3>

                    {{-- Instructor & Rating Info --}}
                    <div class="mt-3 flex items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                {{ strtoupper(substr($course->instructor?->name ?? 'F', 0, 1)) }}
                            </div>
                            <span class="truncate font-medium text-slate-700 dark:text-slate-300">{{ $course->instructor?->name ?? 'Giảng viên FEA' }}</span>
                        </div>

                        @if($rating > 0)
                            <div class="flex items-center gap-1 font-bold text-amber-500 shrink-0">
                                <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span>{{ number_format($rating, 1) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Metrics: Lessons & Price --}}
                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-xl bg-slate-50 p-2.5 dark:bg-slate-800/60">
                            <span class="block text-[11px] font-semibold text-slate-400">Thời lượng</span>
                            <strong class="mt-0.5 block font-bold text-slate-800 dark:text-slate-200">{{ $lessonCount }} bài học</strong>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-2.5 dark:bg-slate-800/60">
                            <span class="block text-[11px] font-semibold text-slate-400">Học phí</span>
                            <strong class="mt-0.5 block font-bold text-slate-900 dark:text-white">{{ $formatPrice($price) }}</strong>
                            @if($originalPrice && (float) $originalPrice > (float) $price)
                                <span class="text-[10px] text-slate-400 line-through">{{ $formatPrice($originalPrice) }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Action Buttons (STU-FE-12) --}}
                    <div class="mt-5 grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-indigo-600 px-3 text-xs font-bold text-white transition hover:bg-indigo-700 shadow-sm">
                            Xem chi tiết
                        </a>
                        <form method="POST" action="{{ route('courses.favorite.destroy', $course) }}" onsubmit="return confirm('Bạn có chắc muốn bỏ khóa học này khỏi danh sách yêu thích?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 text-xs font-bold text-rose-600 transition hover:bg-rose-100 dark:border-rose-900/50 dark:bg-rose-950/40 dark:text-rose-300">
                                Bỏ yêu thích
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $items->links() }}
    </div>
@endif
</x-student-layout>
