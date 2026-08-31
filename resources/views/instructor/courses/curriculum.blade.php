<x-instructor-layout :title="'Nội dung - '.$course->title" page-title="Quản lý nội dung khóa học" :breadcrumb="$course->title">

<script src="{{ asset('js/s3-multipart-uploader.js') }}?v={{ time() }}"></script>

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
                @if($course->publishedVersion?->version_number)
                    <p class="mt-1 text-xs font-bold text-emerald-700">Đang xuất bản: V{{ $course->publishedVersion->version_number }}</p>
                @endif
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">
                    Xây dựng chương học, bài video, tài liệu, quiz và bài tập trước khi gửi khóa học cho admin duyệt.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('instructor.courses.versions.index', $course) }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-indigo-200 px-4 py-2 text-sm font-bold text-indigo-700 transition-colors duration-200 hover:bg-indigo-50">
                    Lịch sử phiên bản
                </a>
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
                <strong id="overview-total-lessons" class="mt-1 block text-2xl text-slate-950">{{ $course->courseSections->sum(fn ($section) => $section->lessons->count()) }}</strong>
            </div>
            <div class="rounded-lg bg-slate-50 p-4">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-500">Bài xem thử</span>
                <strong class="mt-1 block text-2xl text-slate-950">{{ $course->courseSections->flatMap->lessons->where('is_preview', true)->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- HÀNG CHỜ UPLOAD VIDEO LÊN S3 --}}
    <div id="global-video-upload-queue-panel" class="hidden"></div>

    @include('instructor.courses.partials.curriculum-review-state')

    @if(! $submissionCheck->passes())
        <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-4 text-amber-900 shadow-xs">
            <p class="text-sm font-bold">Video đã sẵn sàng, nhưng khóa học vẫn chưa đủ điều kiện gửi duyệt.</p>
            <p class="mt-1 text-xs leading-5">Hoàn tất các mục trong checklist bên dưới để gửi khóa học cho admin duyệt.</p>
        </div>

        @include('instructor.courses.partials.submission-readiness', [
            'submissionCheck' => $submissionCheck,
        ])
    @endif

    <form method="POST" action="{{ route('instructor.courses.sections.store', $course) }}"
          class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.3fr)_auto] lg:items-end">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Tên chương</span>
                <input type="text" name="title" maxlength="255" placeholder="Ví dụ: Giới thiệu khóa học"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('title', 'storeSection') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-emerald-500 focus-visible:ring-emerald-500/20 @enderror">
                @error('title', 'storeSection') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Mô tả chương</span>
                <input type="text" name="description" maxlength="1000" placeholder="Nội dung chính của chương này"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('description', 'storeSection') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-emerald-500 focus-visible:ring-emerald-500/20 @enderror">
                @error('description', 'storeSection') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>
            <div class="flex flex-col gap-2 sm:flex-row">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer sm:w-auto">
                    + Thêm chương
                </button>
                <button
                    type="button"
                    data-lesson-import-open
                    aria-controls="lesson-import-dialog"
                    aria-haspopup="dialog"
                    class="inline-flex min-h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 sm:w-auto"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h10M18 15v6m-3-3h6" />
                    </svg>
                    Nhập từ Excel
                </button>
            </div>
        </div>
    </form>

    @include('instructor.courses.partials.lesson-import-modal', ['course' => $course])

    <div class="space-y-5">
        @forelse($curriculumSections as $section)
            @php
                $hasInvalidSectionDescription = filled($section->description)
                    && \App\Models\CourseSection::descriptionContainsMarkup($section->description);
                $safeSectionDescription = $hasInvalidSectionDescription ? null : $section->description;
                $sectionUpdate = $section->draft_update ?? null;
                $sectionReadOnly = $sectionUpdate?->isPending() ?? false;
                $sectionVersionContext = $sectionUpdate
                    ? app(\App\Services\ContentUpdateDiffService::class)->versionContext($sectionUpdate)
                    : ['current' => $section->publishedVersion?->version_number, 'proposed' => null];
            @endphp
            <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-slate-50 px-5 py-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-md bg-slate-900 px-2 py-1 text-xs font-bold text-white">Chương {{ $loop->iteration }}</span>
                                <span class="text-xs font-semibold text-slate-500">sort_order: {{ $section->sort_order }}</span>
                                @if(isset($section->update_status))
                                    @if($section->update_status === 'draft')
                                        <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Chương nháp</span>
                                    @elseif($section->update_status === 'pending')
                                        <span class="rounded-full border border-blue-300 bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">Chương đã gửi duyệt</span>
                                    @elseif($section->update_status === 'rejected')
                                        <span class="rounded-full border border-rose-300 bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">Chương bị từ chối</span>
                                    @endif
                                @endif
                                @if(($sectionVersionContext['current'] ?? null) !== null)
                                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Đang xuất bản: V{{ $sectionVersionContext['current'] }}</span>
                                @endif
                                @if(($sectionVersionContext['proposed'] ?? null) !== null)
                                    <span class="rounded-full border {{ $sectionUpdate?->isRejected() ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-blue-200 bg-blue-50 text-blue-800' }} px-2.5 py-0.5 text-xs font-bold">
                                        {{ $sectionUpdate?->isRejected() ? 'V'.$sectionVersionContext['proposed'].' — Bị từ chối' : 'Đề xuất: V'.$sectionVersionContext['proposed'].' — '.($sectionUpdate?->isPending() ? 'Chờ duyệt' : 'Nháp') }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="mt-2 text-lg font-bold text-slate-950">{{ $section->title }}</h3>
                            @if(filled($safeSectionDescription))
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $safeSectionDescription }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            @if($sectionReadOnly)
                                <span class="inline-flex min-h-10 items-center rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-bold text-blue-800">
                                    Thay đổi đang chờ Admin duyệt và không thể chỉnh sửa.
                                </span>
                            @else
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
                                            <input type="text" name="title" value="{{ $section->title }}" class="w-full rounded-lg border px-3 py-2 text-sm outline-none @error('title', 'updateSection_'.$section->id) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">
                                            @error('title', 'updateSection_'.$section->id) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                        </label>
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Mô tả</span>
                                            <textarea name="description" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm outline-none @error('description', 'updateSection_'.$section->id) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">{{ $safeSectionDescription }}</textarea>
                                            @if($hasInvalidSectionDescription)
                                                <p class="mt-1 text-xs font-semibold text-amber-700">Mô tả cũ chứa dữ liệu không hợp lệ. Hãy nhập lại mô tả bằng văn bản thuần.</p>
                                            @endif
                                            @error('description', 'updateSection_'.$section->id) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                        </label>
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700 cursor-pointer">Lưu chương</button>
                                    </form>
                                </div>
                            </details>
                            <form method="POST" action="{{ route('instructor.courses.sections.destroy', [$course, $section]) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa chương học này? (Lưu ý: Chỉ xóa được khi chương không còn bài học)')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-rose-200 px-4 py-2 text-sm font-bold text-rose-700 transition-colors duration-200 hover:bg-rose-50 cursor-pointer">
                                    Xóa chương
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100" id="section-lessons-{{ $section->id }}" data-section-lessons-container="{{ $section->id }}">
                    @forelse($section->lessons as $lesson)
                        @include('instructor.courses.partials.lesson-item', [
                            'course' => $course,
                            'section' => $section,
                            'lesson' => $lesson,
                            'typeStyles' => $typeStyles,
                            'statusStyles' => $statusStyles,
                            'lessonTypes' => $lessonTypes,
                            'lessonStatuses' => $lessonStatuses,
                            'formatDuration' => $formatDuration,
                        ])
                    @empty
                        <div class="px-5 py-6 text-sm text-slate-500" data-empty-lessons-notice>Chương này chưa có bài học.</div>
                    @endforelse
                </div>

                <details class="border-t border-slate-100 bg-white" {{ $errors->hasBag('storeLesson_'.$section->id) ? 'open' : '' }}>
                    <summary class="cursor-pointer list-none px-5 py-4 text-sm font-bold text-emerald-700 transition-colors duration-200 hover:bg-emerald-50">
                        + Thêm bài học
                    </summary>
                    <div class="border-t border-slate-100 bg-slate-50 p-5">
                        @include('instructor.courses.partials.lesson-form', [
                            'course' => $course,
                            'action' => route('instructor.courses.sections.lessons.store', [$course, $section]),
                            'method' => 'POST',
                            'lesson' => null,
                            'errorBag' => 'storeLesson_'.$section->id,
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

<script src="{{ asset('js/s3-multipart-uploader.js') }}?v={{ time() }}"></script>
<script>
    document.addEventListener('change', function (e) {
        var target = e.target;
        if (!target || target.name !== 'video_file' || !target.files || !target.files[0]) {
            return;
        }

        var file = target.files[0];
        var form = target.closest('form');
        if (!form) return;

        var durationInput = form.querySelector('input[name="duration"]');
        if (!durationInput) return;

        var video = document.createElement('video');
        video.preload = 'metadata';
        var objectUrl = URL.createObjectURL(file);

        video.onloadedmetadata = function () {
            URL.revokeObjectURL(objectUrl);
            if (video.duration && !isNaN(video.duration) && isFinite(video.duration)) {
                var seconds = Math.round(video.duration);
                durationInput.value = seconds;

                // Thêm hiệu ứng highlight thông báo cho giảng viên biết thời lượng đã tự đồng bộ
                durationInput.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500');
                setTimeout(function () {
                    durationInput.classList.remove('ring-2', 'ring-emerald-500');
                }, 2500);
            }
        };

        video.onerror = function () {
            URL.revokeObjectURL(objectUrl);
        };

        video.src = objectUrl;
    });

    if (window.initCurriculumHlsPolling) {
        window.initCurriculumHlsPolling(@js(route('instructor.courses.hls-status', $course)));
    }
</script>

</x-instructor-layout>
