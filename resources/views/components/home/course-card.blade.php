@props([
    'course',
    'favorited' => false,
    'enrolled' => false,
    'variant' => 'featured',
])

@php
    $discountPrice = $course->discount_price ?? $course->sale_price;
    $currentPrice = $discountPrice ?? $course->price;
    $originalPrice = $discountPrice !== null && (float) $discountPrice < (float) $course->price
        ? $course->price
        : null;
    $isFree = (float) $currentPrice <= 0;
    $levelLabels = [
        'beginner' => 'Cơ bản',
        'intermediate' => 'Trung cấp',
        'advanced' => 'Nâng cao',
    ];
    $thumbnailUrl = null;
    if ($course->thumbnail) {
        $thumbnailUrl = str_starts_with($course->thumbnail, 'http://') || str_starts_with($course->thumbnail, 'https://')
            ? $course->thumbnail
            : asset('storage/'.$course->thumbnail);
    }
@endphp

<article data-home-course-card data-home-course-variant="{{ $variant }}" data-course-id="{{ $course->id }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xs transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-700">
    {{-- Thumbnail --}}
    <div class="relative h-[142px] sm:h-[146px] w-full overflow-hidden bg-gradient-to-br from-blue-950 via-blue-800 to-cyan-600">
        <a href="{{ route('courses.show', $course->slug) }}" class="block h-full w-full" aria-label="Xem chi tiết {{ $course->title }}">
            <div class="absolute inset-0 flex items-center justify-center text-white/30" aria-hidden="true">
                <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            @if($thumbnailUrl)
                <img src="{{ $thumbnailUrl }}"
                     alt="Ảnh khóa học {{ $course->title }}"
                     width="380"
                     height="214"
                     loading="lazy"
                     decoding="async"
                     class="relative h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
            @endif
        </a>

        <span class="absolute left-2 top-2 rounded-md px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-white shadow-xs {{ $variant === 'free' ? 'bg-emerald-600' : 'bg-amber-500' }}">
            {{ $variant === 'free' ? 'Miễn phí' : 'Nổi bật' }}
        </span>
        <x-favorite-button :course="$course" :favorited="$favorited" class="absolute right-2 top-2" />
    </div>

    {{-- Content --}}
    <div class="flex flex-1 flex-col p-3 sm:p-3.5">
        {{-- Category & Level --}}
        <div class="mb-1 flex items-center justify-between gap-1.5">
            @if($course->category)
                <a href="{{ route('courses.category', $course->category->slug) }}" class="truncate text-[11px] font-bold uppercase tracking-wider text-[#0D5BD7] hover:text-blue-700 dark:text-blue-400" title="{{ $course->category->full_name }}">
                    {{ $course->category->full_name }}
                </a>
            @else
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Khóa học</span>
            @endif
            <span class="shrink-0 rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                {{ $levelLabels[$course->level] ?? 'Mọi cấp độ' }}
            </span>
        </div>

        {{-- Title (Cố định chiều cao 2 dòng gọn gàng) --}}
        <h3 class="line-clamp-2 h-[38px] text-[13.5px] sm:text-[14px] font-bold leading-tight text-slate-900 transition group-hover:text-[#0D5BD7] dark:text-white dark:group-hover:text-blue-400">
            <a href="{{ route('courses.show', $course->slug) }}" title="{{ $course->title }}">{{ $course->title }}</a>
        </h3>

        {{-- Instructor --}}
        <p class="mt-1 truncate text-[11.5px] text-slate-500 dark:text-slate-400">
            {{ $course->instructor?->name ?? 'Giảng viên FEA' }}
        </p>

        {{-- Rating --}}
        <div class="mt-1 flex items-center gap-1 text-[11px]" aria-label="Điểm đánh giá {{ number_format((float) $course->rating_avg, 1) }} trên 5">
            <span class="font-extrabold text-amber-600 dark:text-amber-400">{{ number_format((float) $course->rating_avg, 1) }}</span>
            <div class="flex items-center">
                @for($star = 1; $star <= 5; $star++)
                    <svg class="h-2.5 w-2.5 sm:h-3 sm:w-3 {{ $star <= round((float) $course->rating_avg) ? 'text-amber-400' : 'text-slate-200 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
            </div>
            <span class="text-[10.5px] text-slate-400 dark:text-slate-500">({{ (int) $course->rating_count }})</span>
        </div>

        {{-- Lesson & Student Count (Gộp chung 1 hàng) --}}
        <div class="mt-1.5 flex items-center gap-1.5 border-t border-slate-100 pt-1.5 text-[11px] font-medium text-slate-500 dark:border-slate-800/80 dark:text-slate-400">
            <span>{{ (int) $course->lessons_count }} bài học</span>
            <span class="text-slate-300 dark:text-slate-700">•</span>
            <span>{{ number_format((int) $course->enrollment_count) }} học viên</span>
        </div>

        {{-- Price & Bottom Action Button --}}
        <div class="mt-auto pt-2">
            <div class="flex items-baseline gap-1.5">
                @if($enrolled)
                    <span class="text-[13.5px] font-extrabold text-[#0D5BD7] dark:text-blue-400">Đã sở hữu</span>
                @elseif($isFree)
                    <span class="text-[13.5px] font-extrabold text-emerald-600 dark:text-emerald-400">Miễn phí</span>
                @else
                    <span class="text-[13.5px] font-extrabold text-slate-950 dark:text-white">{{ number_format((float) $currentPrice, 0, ',', '.') }}đ</span>
                    @if($originalPrice)
                        <span class="text-[10.5px] text-slate-400 line-through dark:text-slate-500">{{ number_format((float) $originalPrice, 0, ',', '.') }}đ</span>
                    @endif
                @endif
            </div>

            <a href="{{ route('courses.show', $course->slug) }}" class="mt-2 inline-flex h-8.5 w-full items-center justify-center rounded-xl bg-slate-950 px-3 py-1.5 text-xs font-bold text-white shadow-2xs transition hover:bg-[#0D5BD7] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0D5BD7] dark:bg-white dark:text-slate-950 dark:hover:bg-blue-100">
                Xem chi tiết
            </a>
        </div>
    </div>
</article>
