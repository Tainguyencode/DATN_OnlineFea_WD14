<x-instructor-layout :title="$course->title" page-title="Chỉnh sửa khóa học" :breadcrumb="$course->title">

@php
    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
        'archived' => 'bg-zinc-100 text-zinc-700 border-zinc-200',
    ];
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
                @if($course->status === 'published')
                    <a href="{{ route('courses.show', $course) }}" target="_blank"
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

        @if($course->status === 'rejected' && $course->rejection_reason)
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <strong>Lý do từ chối:</strong> {{ $course->rejection_reason }}
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

    <section id="course-content" class="scroll-mt-20 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Curriculum builder</p>
                <h2 class="mt-1 text-lg font-bold text-slate-950">Quản lý nội dung</h2>
                <p class="mt-1 text-sm text-slate-500">Tạo chương, bài học, quiz hoặc assignment trước khi gửi khóa học cho admin duyệt.</p>
            </div>
            <a href="{{ route('instructor.courses.students', $course) }}"
               class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 cursor-pointer">
                {{ $course->enrollments()->count() }} học viên
            </a>
        </div>

        <div class="mt-5 space-y-4">
            @forelse($course->chapters as $chapter)
                <article class="overflow-hidden rounded-lg border border-slate-200">
                    <div class="flex items-center justify-between gap-3 bg-slate-50 px-4 py-3">
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $chapter->title }}</h3>
                            <p class="text-xs text-slate-500">{{ $chapter->lessons->count() }} bài học</p>
                        </div>
                    </div>

                    <ul class="divide-y divide-slate-100">
                        @forelse($chapter->lessons as $lesson)
                            <li class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                                <div>
                                    <span class="font-semibold text-slate-800">{{ $lesson->title }}</span>
                                    @if($lesson->is_preview)
                                        <span class="ml-2 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">Học thử</span>
                                    @endif
                                </div>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold uppercase text-slate-500">{{ $lesson->type }}</span>
                            </li>
                        @empty
                            <li class="px-4 py-4 text-sm text-slate-500">Chương này chưa có bài học.</li>
                        @endforelse
                    </ul>

                    <form method="POST" action="{{ route('instructor.chapters.lessons.store', $chapter) }}" class="grid gap-2 border-t border-slate-100 p-4 lg:grid-cols-[minmax(0,1fr)_150px_120px_auto]">
                        @csrf
                        <input type="text" name="title" placeholder="Tên bài giảng" required
                               class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
                        <select name="type" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 cursor-pointer">
                            <option value="video">Video</option>
                            <option value="document">Tài liệu</option>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                        </select>
                        <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-600">
                            <input type="checkbox" name="is_preview" value="1" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Học thử
                        </label>
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 cursor-pointer">
                            Thêm bài
                        </button>
                    </form>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">
                    Chưa có chương nào. Tạo chương đầu tiên để bắt đầu xây dựng nội dung khóa học.
                </div>
            @endforelse
        </div>

        <form method="POST" action="{{ route('instructor.courses.chapters.store', $course) }}" class="mt-5 grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto]">
            @csrf
            <input type="text" name="title" placeholder="Tên chương mới" required
                   class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
            <button type="submit" class="rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800 cursor-pointer">
                Thêm chương
            </button>
        </form>
    </section>
</div>

</x-instructor-layout>
