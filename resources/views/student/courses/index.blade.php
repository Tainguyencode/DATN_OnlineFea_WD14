<x-student-layout title="Khóa học" page-title="Khóa học của tôi" breadcrumb="Các khóa học bạn đã đăng ký">

@if($enrollments->isEmpty())
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center shadow-sm">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
            </svg>
        </div>
        <h2 class="mt-5 text-xl font-bold text-slate-950">Bạn chưa đăng ký khóa học nào</h2>
        <p class="mt-2 text-sm text-slate-500">Khám phá catalog khóa học đã được duyệt và bắt đầu học ngay.</p>
        <a href="{{ route('courses.index') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white transition-colors duration-200 hover:bg-indigo-700 cursor-pointer">
            Tìm khóa học
        </a>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach($enrollments as $enrollment)
            @php
                $course = $enrollment->course;
                $progress = (float) ($enrollment->progress_percent ?? 0);
                $statusLabels = [
                    'active' => 'Đang học',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                ];
            @endphp
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                <a href="{{ route('courses.show', $course->slug) }}" class="block aspect-video overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-900 to-violet-700">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-4xl font-extrabold text-white/70">Fea</div>
                    @endif
                </a>

                <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <span class="truncate text-xs font-bold uppercase tracking-wide text-indigo-600">{{ $course->category?->name ?? 'Khóa học' }}</span>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                            {{ $statusLabels[$enrollment->status] ?? 'Đang học' }}
                        </span>
                    </div>

                    <h3 class="mt-3 line-clamp-2 text-lg font-extrabold leading-snug text-slate-950">
                        {{ $course->title }}
                    </h3>
                    <p class="mt-2 text-sm text-slate-500">Giảng viên: {{ $course->instructor?->name ?? 'Fea Instructor' }}</p>

                    <div class="mt-5">
                        <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-500">
                            <span>Tiến độ</span>
                            <span>{{ number_format($progress, 0) }}%</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all duration-300" style="width: {{ min(100, $progress) }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('courses.show', $course->slug) }}" class="mt-5 flex h-11 w-full items-center justify-center rounded-xl bg-slate-950 text-sm font-bold text-white transition-colors duration-200 hover:bg-indigo-600 cursor-pointer">
                        Tiếp tục học
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8">{{ $enrollments->links() }}</div>
@endif

</x-student-layout>
