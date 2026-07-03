<x-instructor-layout :title="'Nội dung - '.$course->title" page-title="Quản lý nội dung khóa học" :breadcrumb="$course->title">

@php
    $typeStyles = [
        'video' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'document' => 'bg-sky-50 text-sky-700 border-sky-200',
        'quiz' => 'bg-violet-50 text-violet-700 border-violet-200',
        'assignment' => 'bg-amber-50 text-amber-700 border-amber-200',
    ];

    $statusStyles = [
        'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
        'published' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];

    $formatDuration = function ($seconds) {
        $seconds = (int) $seconds;
        if ($seconds <= 0) {
            return 'Chưa đặt';
        }

        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return $minutes > 0 ? $minutes.' phút'.($remaining ? ' '.$remaining.' giây' : '') : $remaining.' giây';
    };
@endphp

<div class="space-y-6">
    <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-600">Udemy-style curriculum builder</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">{{ $course->title }}</h2>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                    Xây dựng chương học, bài video, tài liệu, quiz và bài tập trước khi gửi khóa học cho admin duyệt.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('instructor.courses.edit', $course) }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 cursor-pointer">
                    Thông tin khóa học
                </a>
                <a href="{{ route('instructor.courses.index') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-500 cursor-pointer">
                    Danh sách khóa học
                </a>
            </div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Chương học</span>
                <strong class="mt-1 block text-2xl text-slate-950">{{ $course->courseSections->count() }}</strong>
            </div>
            <div class="rounded-lg bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Bài học</span>
                <strong class="mt-1 block text-2xl text-slate-950">{{ $course->courseSections->sum(fn ($section) => $section->lessons->count()) }}</strong>
            </div>
            <div class="rounded-lg bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Bài xem thử</span>
                <strong class="mt-1 block text-2xl text-slate-950">{{ $course->courseSections->flatMap->lessons->where('is_preview', true)->count() }}</strong>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
            <p class="font-bold">Vui lòng kiểm tra lại thông tin.</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('instructor.courses.sections.store', $course) }}"
          class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)_auto] lg:items-end">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Tên chương</span>
                <input type="text" name="title" required maxlength="255" placeholder="Ví dụ: Giới thiệu khóa học"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Mô tả chương</span>
                <input type="text" name="description" maxlength="1000" placeholder="Nội dung chính của chương này"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus:border-emerald-500 focus-visible:ring-2 focus-visible:ring-emerald-500/20">
            </label>
            <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
                + Thêm chương
            </button>
        </div>
    </form>

    <div class="space-y-5">
        @forelse($course->courseSections as $section)
            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md bg-slate-900 px-2 py-1 text-xs font-bold text-white">Chương {{ $loop->iteration }}</span>
                                <span class="text-xs font-semibold text-slate-500">sort_order: {{ $section->sort_order }}</span>
                            </div>
                            <h3 class="mt-2 text-lg font-bold text-slate-950">{{ $section->title }}</h3>
                            @if($section->description)
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $section->description }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            <details class="group">
                                <summary class="inline-flex min-h-10 cursor-pointer list-none items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-white">
                                    Sửa chương
                                </summary>
                                <div class="mt-3 w-full rounded-lg border border-slate-200 bg-white p-4 shadow-sm lg:w-[520px]">
                                    <form method="POST" action="{{ route('instructor.courses.sections.update', [$course, $section]) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Tên chương</span>
                                            <input type="text" name="title" value="{{ $section->title }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">
                                        </label>
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Mô tả</span>
                                            <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-emerald-500">{{ $section->description }}</textarea>
                                        </label>
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700 cursor-pointer">Lưu chương</button>
                                    </form>
                                </div>
                            </details>
                            <form method="POST" action="{{ route('instructor.courses.sections.destroy', [$course, $section]) }}" onsubmit="return confirm('Xóa chương này sẽ xóa toàn bộ bài học bên trong. Bạn chắc chắn?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-rose-200 px-4 py-2 text-sm font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-50 cursor-pointer">
                                    Xóa chương
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($section->lessons as $lesson)
                        @php
                            $typeClass = $typeStyles[$lesson->type] ?? $typeStyles['video'];
                            $statusClass = $statusStyles[$lesson->status] ?? $statusStyles['draft'];
                        @endphp
                        <div class="p-5">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $typeClass }}">{{ $lessonTypes[$lesson->type] ?? $lesson->type }}</span>
                                        <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $lessonStatuses[$lesson->status] ?? $lesson->status }}</span>
                                        @if($lesson->is_preview)
                                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Xem thử</span>
                                        @endif
                                        @if($lesson->type === 'video')
                                            <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $lesson->video_path ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                                                {{ $lesson->video_path ? 'Đã có video' : 'Chưa có video' }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="mt-2 font-bold text-slate-950">{{ $lesson->title }}</h4>
                                    <div class="mt-1 flex flex-wrap gap-3 text-xs text-slate-500">
                                        <span>Thời lượng: {{ $formatDuration($lesson->duration ?? $lesson->duration_seconds) }}</span>
                                        <span>sort_order: {{ $lesson->sort_order }}</span>
                                        @if($lesson->type === 'video' && $lesson->video_path)
                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($lesson->video_path) }}" target="_blank" class="font-semibold text-emerald-600 hover:underline">Video file</a>
                                        @endif
                                        @if($lesson->video_url)
                                            <a href="{{ $lesson->video_url }}" target="_blank" class="font-semibold text-indigo-600 hover:underline">Video URL</a>
                                        @endif
                                        @if($lesson->document_file)
                                            <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="font-semibold text-sky-600 hover:underline">Tài liệu</a>
                                        @endif
                                    </div>
                                    @if($lesson->content)
                                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $lesson->content }}</p>
                                    @endif
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-2">
                                    @if($lesson->type === 'quiz')
                                        <a href="{{ route('instructor.courses.lessons.quiz.show', [$course, $lesson]) }}"
                                           class="inline-flex min-h-10 items-center justify-center rounded-lg border border-violet-200 px-4 py-2 text-sm font-bold text-violet-700 transition-colors duration-200 hover:bg-violet-50 cursor-pointer">
                                            Quan ly cau hoi
                                        </a>
                                    @endif
                                    <details>
                                        <summary class="inline-flex min-h-10 cursor-pointer list-none items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                                            Sửa bài học
                                        </summary>
                                        <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-4 lg:w-[620px]">
                                            @include('instructor.courses.partials.lesson-form', [
                                                'action' => route('instructor.courses.lessons.update', [$course, $lesson]),
                                                'method' => 'PUT',
                                                'lesson' => $lesson,
                                                'lessonTypes' => $lessonTypes,
                                                'lessonStatuses' => $lessonStatuses,
                                                'submitLabel' => 'Lưu bài học',
                                            ])
                                        </div>
                                    </details>
                                    <form method="POST" action="{{ route('instructor.courses.lessons.destroy', [$course, $lesson]) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài học này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-rose-200 px-4 py-2 text-sm font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-50 cursor-pointer">
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500">Chương này chưa có bài học.</div>
                    @endforelse
                </div>

                <details class="border-t border-slate-100 bg-white">
                    <summary class="cursor-pointer list-none px-5 py-4 text-sm font-bold text-emerald-700 transition-colors duration-200 hover:bg-emerald-50">
                        + Thêm bài học
                    </summary>
                    <div class="border-t border-slate-100 bg-slate-50 p-5">
                        @include('instructor.courses.partials.lesson-form', [
                            'action' => route('instructor.courses.sections.lessons.store', [$course, $section]),
                            'method' => 'POST',
                            'lesson' => null,
                            'nextSortOrder' => $section->lessons->count(),
                            'lessonTypes' => $lessonTypes,
                            'lessonStatuses' => $lessonStatuses,
                            'submitLabel' => 'Thêm bài học',
                        ])
                    </div>
                </details>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 bg-white px-6 py-14 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h10"/>
                    </svg>
                </div>
                <h3 class="mt-5 text-lg font-bold text-slate-950">Chưa có chương học</h3>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Tạo chương đầu tiên để bắt đầu chia khóa học thành các phần rõ ràng.</p>
            </div>
        @endforelse
    </div>
</div>

</x-instructor-layout>
