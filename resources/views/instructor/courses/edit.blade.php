<x-instructor-layout :title="$course->title" page-title="Chỉnh sửa khóa học" :breadcrumb="$course->title">

@php
    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
        'archived' => 'bg-zinc-100 text-zinc-700 border-zinc-200',
    ];

    $sectionCount = $course->courseSections->count();
    $lessonCount = $course->courseSections->sum(fn ($section) => $section->lessons->count());
@endphp

<div class="space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusStyles[$course->status] ?? $statusStyles['draft'] }}">
                        {{ $statusOptions[$course->status] ?? $course->status }}
                    </span>
                    <span class="text-xs font-semibold text-slate-500">Tạo ngày {{ $course->created_at?->format('d/m/Y') }}</span>
                </div>
                <h2 class="mt-3 text-xl font-bold text-slate-950">{{ $course->title }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $course->short_description ?: 'Bổ sung mô tả ngắn để học viên hiểu nhanh giá trị khóa học.' }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('instructor.courses.index') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 cursor-pointer">
                    Quay lại
                </a>
                <a href="{{ route('instructor.courses.curriculum', $course) }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-indigo-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 cursor-pointer">
                    Quản lý nội dung
                </a>
                @if($course->status === 'published')
                    <a href="{{ route('courses.show', $course->slug) }}" target="_blank"
                       class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 cursor-pointer">
                        Xem trước
                    </a>
                @endif
                @if(in_array($course->status, ['draft', 'rejected'], true))
                    <form method="POST" action="{{ route('instructor.courses.submit', $course) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex min-h-10 items-center justify-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-amber-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 cursor-pointer">
                            Gửi duyệt
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if($course->status === 'rejected' && $course->rejectionReasonText())
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <strong>Lý do từ chối:</strong> {{ $course->rejectionReasonText() }}
            </div>
        @endif
    </div>

    @include('instructor.courses._form', [
        'course' => $course,
        'categories' => $categories,
        'action' => route('instructor.courses.update', $course),
        'method' => 'PUT',
        'submitLabel' => 'Lưu nháp',
    ])

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Curriculum builder</p>
                <h2 class="mt-1 text-lg font-bold text-slate-950">Nội dung khóa học</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $sectionCount }} chương · {{ $lessonCount }} bài học</p>
            </div>
            <a href="{{ route('instructor.courses.curriculum', $course) }}"
               class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
                Mở trình quản lý nội dung
            </a>
        </div>
    </section>
</div>

</x-instructor-layout>
