<x-student-layout title="Khóa học yêu thích" page-title="Khóa học yêu thích">
@php
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
@endphp

@if($items->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center shadow-sm">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 0 1 6.364 0L12 7.636l1.318-1.318a4.5 4.5 0 1 1 6.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 0 1 0-6.364Z"/>
            </svg>
        </div>
        <h2 class="mt-4 text-lg font-bold text-slate-950">Bạn chưa có khóa học yêu thích nào.</h2>
        <p class="mt-2 text-sm text-slate-500">Lưu lại những khóa học bạn quan tâm để quay lại xem nhanh hơn.</p>
        <a href="{{ route('courses.index') }}" class="mt-5 inline-flex h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white transition hover:bg-indigo-700">
            Khám phá khóa học
        </a>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($items as $item)
            @php
                $course = $item->course;
                $discountPrice = $course->discount_price ?? $course->sale_price;
                $price = $discountPrice ?? $course->price;
                $originalPrice = $discountPrice ? $course->price : null;
                $lessonCount = $course->lessons_count ?? 0;
            @endphp

            <article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <a href="{{ route('courses.show', $course->slug) }}" class="block">
                    <div class="relative aspect-video overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-900 to-violet-800">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-3xl font-extrabold text-white/80">Fea</div>
                        @endif
                        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-xs font-bold text-slate-900">
                            {{ $levelLabels[$course->level] ?? 'Mọi trình độ' }}
                        </span>
                    </div>
                </a>

                <div class="flex flex-1 flex-col p-5">
                    @if($course->category)
                        <a href="{{ route('courses.category', $course->category->slug) }}" class="text-xs font-bold uppercase tracking-wide text-indigo-600 hover:text-indigo-800">
                            {{ $course->category->full_name }}
                        </a>
                    @endif

                    <h3 class="mt-2 line-clamp-2 text-lg font-extrabold leading-snug text-slate-950">
                        <a href="{{ route('courses.show', $course->slug) }}" class="transition hover:text-indigo-600">{{ $course->title }}</a>
                    </h3>

                    <div class="mt-4 flex items-center gap-2 text-sm text-slate-500">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                            {{ strtoupper(substr($course->instructor?->name ?? 'F', 0, 1)) }}
                        </div>
                        <span class="truncate">{{ $course->instructor?->name ?? 'Giảng viên Fea' }}</span>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <span class="block text-xs font-semibold text-slate-500">Bài học</span>
                            <strong class="mt-1 block text-slate-950">{{ $lessonCount }}</strong>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <span class="block text-xs font-semibold text-slate-500">Giá</span>
                            <strong class="mt-1 block text-slate-950">{{ $formatPrice($price) }}</strong>
                            @if($originalPrice && (float) $originalPrice > (float) $price)
                                <span class="text-xs text-slate-400 line-through">{{ $formatPrice($originalPrice) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('courses.show', $course->slug) }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-slate-950 px-4 text-sm font-bold text-white transition hover:bg-indigo-600">
                            Xem chi tiết
                        </a>
                        <form method="POST" action="{{ route('courses.favorite.destroy', $course) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-600 transition hover:bg-rose-100">
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
