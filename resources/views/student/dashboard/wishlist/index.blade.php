<x-student-layout title="Yêu thích" page-title="Khóa học yêu thích" breadcrumb="Danh sách khóa học bạn đã lưu để xem lại sau.">
    @if($items->isEmpty())
        <x-student.dashboard.empty-state title="Chưa có khóa học yêu thích" description="Lưu những khóa bạn quan tâm để dễ dàng quay lại." :action-url="route('courses.index')" action-label="Khám phá khóa học" />
    @else
        <p class="mb-5 text-sm font-semibold text-slate-500">{{ $items->total() }} khóa học đã lưu</p>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 2xl:grid-cols-3">
            @foreach($items as $item)
                @continue(! $item->course)
                @php
                    $owned = in_array($item->course->id, $enrolledCourseIds, true);
                    $inCart = in_array($item->course->id, $cartCourseIds, true);
                    $primaryUrl = $owned
                        ? ($item->course->learningEntryUrl() ?? route('courses.show', $item->course->slug))
                        : ($inCart ? route('student.cart') : route('courses.show', $item->course->slug));
                    $primaryLabel = $owned ? 'Vào học' : ($inCart ? 'Đã có trong giỏ' : 'Chi tiết');
                @endphp
                <x-student.dashboard.course-card :course="$item->course">
                    <x-slot:actions>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ $primaryUrl }}" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-[#0056D2] px-3 text-sm font-bold text-white hover:bg-[#0046B8]">{{ $primaryLabel }}</a>
                            <form method="POST" action="{{ route('courses.favorite.destroy', $item->course) }}" x-data="{ submitting: false }" x-on:submit="submitting = true" onsubmit="return confirm('Bỏ khóa học này khỏi danh sách yêu thích?')">
                                @csrf @method('DELETE')
                                <button type="submit" :disabled="submitting" class="min-h-10 w-full rounded-xl border border-rose-200 px-3 text-sm font-bold text-rose-600 hover:bg-rose-50 disabled:opacity-60">Bỏ lưu</button>
                            </form>
                        </div>
                    </x-slot:actions>
                </x-student.dashboard.course-card>
            @endforeach
        </div>
        <x-student.dashboard.pagination :paginator="$items" />
    @endif
</x-student-layout>
