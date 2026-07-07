@props([
    'course',
    'favorited' => null,
])

@php
    $discountPrice = $course->discount_price ?? $course->sale_price;
    $price = $discountPrice ?? $course->price;
    $originalPrice = $discountPrice ? $course->price : null;
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $gradients = [
        'from-indigo-500 to-purple-600',
        'from-emerald-500 to-teal-600',
        'from-orange-500 to-red-500',
        'from-blue-500 to-cyan-500',
        'from-pink-500 to-rose-500',
    ];
    $gradient = $gradients[$course->id % count($gradients)];
    $lessonCount = $course->lessons_count ?? 0;
    $isFavorited = (bool) ($favorited ?? ($course->is_favorited ?? false));
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200/60 bg-white transition-all duration-300 hover:border-indigo-300 hover:shadow-xl hover:shadow-indigo-500/5 dark:border-slate-800/80 dark:bg-[#161615] dark:hover:border-indigo-500/50 dark:hover:shadow-none">
    <div class="relative aspect-video overflow-hidden bg-gradient-to-br {{ $gradient }}">
        <a href="{{ route('courses.show', $course->slug) }}" class="block h-full" aria-label="Xem chi tiết {{ $course->title }}">
            @if($course->thumbnail)
                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="h-14 w-14 text-slate-400 dark:text-slate-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5zm0 0 6.16-3.422a12.083 12.083 0 0 1 .665 6.479A11.952 11.952 0 0 0 12 20.055a11.952 11.952 0 0 0-6.824 2.998 12.078 12.078 0 0 1 .665-6.479L12 14z"/></svg>
                </div>
            @endif
        </a>

        <x-favorite-button :course="$course" :favorited="$isFavorited" class="absolute right-2 top-2 z-10" />

        @if($course->is_featured)
            <span class="absolute left-2 top-2 rounded bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">Best Seller</span>
        @endif
        <span class="absolute bottom-2 right-2 rounded bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-700 shadow-sm dark:bg-slate-900 dark:text-slate-200">
            {{ $levelLabels[$course->level] ?? $course->level }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-4">
        @if($course->category)
            <span class="mb-1 text-xs font-semibold uppercase tracking-wide text-[#0056D2] dark:text-blue-300">{{ $course->category->name }}</span>
        @endif

        <h3 class="mb-1.5 line-clamp-2 min-h-10 text-base font-bold leading-snug text-slate-900 transition duration-200 group-hover:text-[#0056D2] dark:text-white dark:group-hover:text-blue-300">
            <a href="{{ route('courses.show', $course->slug) }}">{{ $course->title }}</a>
        </h3>

        <p class="mb-3 line-clamp-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
            {{ $course->short_description ?: Str::limit($course->description, 110) }}
        </p>

        <p class="mb-1.5 line-clamp-1 text-xs text-slate-500 dark:text-slate-400">
            {{ $course->instructor?->name ?? 'Giảng viên FEA' }}
        </p>

        <div class="mb-1.5 flex items-center gap-1">
            <span class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ number_format((float) $course->rating_avg, 1) }}</span>
            @for($i = 1; $i <= 5; $i++)
                <svg class="h-3.5 w-3.5 {{ $i <= round($course->rating_avg) ? 'fill-amber-500 text-amber-500' : 'text-slate-300 dark:text-slate-700' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
            <span class="ml-1 text-xs text-slate-500 dark:text-slate-400">({{ $course->rating_count }})</span>
        </div>

        <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">{{ $course->enrollment_count }} học viên • {{ $levelLabels[$course->level] ?? $course->level }}</p>

        <div class="mt-auto">
            <div>
                @if($price == 0)
                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">Miễn phí</span>
                @else
                    <span class="text-base font-bold text-slate-900 dark:text-white">{{ number_format($price, 0, ',', '.') }}đ</span>
                    @if($originalPrice)
                        <span class="ml-1 text-sm text-slate-500 line-through dark:text-slate-400">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                    @endif
                @endif
            </div>
            <span class="flex items-center gap-1 text-xs font-medium text-slate-400 dark:text-slate-400">
                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 1 1 0 5.292M15 21H3v-1a6 6 0 0 1 12 0v1zm0 0h6v-1a6 6 0 0 0-9-5.197M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z" /></svg>
                {{ $lessonCount }} bài
            </span>
        </div>

        <a href="{{ route('courses.show', $course->slug) }}" class="mt-4 inline-flex h-10 items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white transition hover:bg-indigo-600 dark:bg-white dark:text-slate-950 dark:hover:bg-indigo-200">
            Xem chi tiết
        </a>
    </div>
</article>
