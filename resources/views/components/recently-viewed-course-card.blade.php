@props([
    'history',
    'enrollment' => null,
    'showDelete' => false,
])

@php
    $course = $history->course;
    $lastViewedAt = $history->last_viewed_at;
    $isOwned = (bool) $enrollment;
    $progress = (float) ($enrollment?->progress_percent ?? 0);
    $price = (float) ($course?->discount_price ?? $course?->sale_price ?? $course?->price ?? 0);
    $originalPrice = ($course?->discount_price || $course?->sale_price) ? (float) $course?->price : null;
    $detailUrl = $course ? route('courses.show', $course->slug) : '#';
    $learningUrl = $isOwned && $course ? ($course->learningEntryUrl() ?? $detailUrl) : null;
@endphp

@if($course)
    <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-900">
        <a href="{{ $detailUrl }}" class="block aspect-video overflow-hidden bg-slate-900">
            @if($course->thumbnail)
                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            @else
                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-800 to-blue-700 text-4xl font-extrabold text-white/75">FEA</div>
            @endif
        </a>

        <div class="flex flex-1 flex-col p-5">
            <div class="flex items-start justify-between gap-3">
                <span class="min-w-0 truncate text-xs font-bold uppercase text-[#0056D2] dark:text-blue-300">
                    {{ $course->category?->name ?? 'Khóa học' }}
                </span>
                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                    {{ $lastViewedAt?->diffForHumans() ?? 'Vừa xem' }}
                </span>
            </div>

            <h3 class="mt-3 line-clamp-2 min-h-14 text-lg font-extrabold leading-snug text-slate-950 transition group-hover:text-[#0056D2] dark:text-white dark:group-hover:text-blue-300">
                <a href="{{ $detailUrl }}">{{ $course->title }}</a>
            </h3>

            <p class="mt-2 line-clamp-1 text-sm text-slate-500 dark:text-slate-400">
                Giảng viên: {{ $course->instructor?->name ?? 'FEA Instructor' }}
            </p>

            <div class="mt-4">
                @if($isOwned)
                    <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <span>Tiến độ</span>
                        <span>{{ number_format($progress, 0) }}%</span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                        <div class="h-full rounded-full bg-[#0056D2]" style="width: {{ min(100, $progress) }}%"></div>
                    </div>
                @else
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm font-bold text-slate-950 dark:text-white">
                            @if($price <= 0)
                                Miễn phí
                            @else
                                {{ number_format($price, 0, ',', '.') }}đ
                            @endif
                        </span>
                        @if($originalPrice && $originalPrice > $price)
                            <span class="text-xs font-semibold text-slate-400 line-through">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mt-auto flex flex-col gap-3 pt-5 sm:flex-row">
                <a href="{{ $learningUrl ?? $detailUrl }}" class="inline-flex h-10 flex-1 items-center justify-center rounded-xl bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-[#0056D2] dark:bg-white dark:text-slate-950 dark:hover:bg-blue-100">
                    {{ $isOwned ? 'Tiếp tục học' : 'Xem chi tiết' }}
                </a>

                @if($showDelete)
                    <form method="POST" action="{{ route('student.recently-viewed.destroy', $history->id) }}" onsubmit="return confirm('Xóa khóa học này khỏi lịch sử xem gần đây?')" class="sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                            Xóa
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </article>
@endif
