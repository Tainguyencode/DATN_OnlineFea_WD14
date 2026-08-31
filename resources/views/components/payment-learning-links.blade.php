@props(['order'])

@php
    $order->loadMissing('items.course');
    $purchasedCourses = $order->items->pluck('course')->filter()->unique('id')->values();
@endphp

<div class="flex-1 space-y-2">
    @forelse($purchasedCourses as $course)
        <a href="{{ $course->learningEntryUrl() ?? route('courses.show', $course->slug) }}"
           data-purchased-course="{{ $course->id }}"
           class="block rounded-xl bg-[#0056D2] px-4 py-3 text-center text-xs font-bold text-white transition hover:bg-[#0046B8]">
            Vào học ngay{{ $purchasedCourses->count() > 1 ? ' — '.$course->title : '' }} →
        </a>
    @empty
        <a href="{{ route('student.courses') }}" class="block rounded-xl bg-[#0056D2] px-4 py-3 text-center text-xs font-bold text-white">
            Xem khóa học của tôi →
        </a>
    @endforelse
</div>
