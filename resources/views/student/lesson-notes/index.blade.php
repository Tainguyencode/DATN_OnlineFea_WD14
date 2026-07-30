<x-student-layout title="Ghi chú học tập" page-title="Ghi chú học tập" breadcrumb="Tất cả ghi chú riêng tư của bạn">

<div class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
    <form method="GET" action="{{ route('student.lesson-notes.index') }}" class="grid gap-4 lg:grid-cols-[1fr_220px_180px_auto] lg:items-end">
        <label class="block">
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Tìm nội dung</span>
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                class="ui-input mt-2"
                placeholder="Nhập từ khóa trong ghi chú"
            >
        </label>

        <label class="block">
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Khóa học</span>
            <select name="course_id" class="ui-select mt-2">
                <option value="">Tất cả khóa học</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected((int) $courseId === (int) $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
        </label>

        <label class="block">
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Sắp xếp</span>
            <select name="sort" class="ui-select mt-2">
                <option value="latest" @selected($sort === 'latest')>Mới nhất</option>
                <option value="timestamp" @selected($sort === 'timestamp')>Theo mốc thời gian</option>
            </select>
        </label>

        <button type="submit" class="ui-button-primary h-[50px]">Lọc</button>
    </form>
</div>

@if($notes->count() === 0)
    <div class="ui-empty">
        Bạn chưa có ghi chú nào phù hợp với bộ lọc hiện tại.
    </div>
@else
    <div data-study-notes class="space-y-4">
        @foreach($notes as $note)
            @php
                $course = $note->learningCourse();
                $lessonUrl = $course ? route('courses.lessons.show', [$course, $note->lesson]) : null;
                if ($lessonUrl && $note->timestamp_seconds !== null) {
                    $lessonUrl .= (str_contains($lessonUrl, '?') ? '&' : '?').'t='.$note->timestamp_seconds;
                }
            @endphp

            <article
                data-study-note-card
                data-update-url="{{ route('lesson-notes.update', $note) }}"
                data-delete-url="{{ route('lesson-notes.destroy', $note) }}"
                data-is-video="{{ $note->lesson?->type === 'video' ? '1' : '0' }}"
                data-duration="{{ (int) ($note->lesson?->duration_seconds ?: $note->lesson?->duration ?: 0) }}"
                class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900"
            >
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            <span>{{ $course?->title ?? 'Khóa học không xác định' }}</span>
                            @if($note->sectionTitle())
                                <span>•</span>
                                <span>{{ $note->sectionTitle() }}</span>
                            @endif
                            <span>•</span>
                            <span>{{ $note->lesson?->title }}</span>
                        </div>

                        <div class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-800 dark:text-slate-100" data-study-note-content>{{ $note->content }}</div>

                        <form data-study-note-edit-form class="mt-4 hidden space-y-3">
                            <textarea name="content" maxlength="2000" required rows="4" class="ui-input h-auto min-h-28 py-3" data-study-note-edit-content>{{ $note->content }}</textarea>
                            @if($note->lesson?->type === 'video')
                                <label class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-300">
                                    Mốc giây
                                    <input
                                        type="number"
                                        min="0"
                                        @if((int) ($note->lesson?->duration_seconds ?: $note->lesson?->duration ?: 0) > 0) max="{{ (int) ($note->lesson?->duration_seconds ?: $note->lesson?->duration ?: 0) }}" @endif
                                        name="timestamp_seconds"
                                        value="{{ $note->timestamp_seconds }}"
                                        class="ui-input h-10 w-28"
                                        data-study-note-edit-timestamp
                                    >
                                    <span data-study-note-edit-time-label>{{ $note->timestampLabel() }}</span>
                                </label>
                            @endif
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p data-study-note-status class="text-sm text-slate-500 dark:text-slate-400"></p>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-study-note-cancel class="ui-button-secondary h-10 px-4">Hủy</button>
                                    <button type="submit" data-study-note-save class="ui-button-primary h-10 px-4">Lưu</button>
                                </div>
                            </div>
                        </form>

                        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                            @if($note->timestamp_seconds !== null)
                                <span data-study-note-time>{{ $note->timestampLabel() }}</span>
                            @endif
                            <span>Cập nhật {{ $note->updated_at?->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2">
                        @if($lessonUrl)
                            <a href="{{ $lessonUrl }}" class="ui-button-secondary h-10 px-4">Mở bài học</a>
                        @endif
                        <button type="button" data-study-note-edit class="ui-button-secondary h-10 px-4">Sửa</button>
                        <button type="button" data-study-note-delete class="inline-flex h-10 items-center justify-center rounded-lg border border-rose-200 bg-white px-4 text-sm font-medium text-rose-600 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-70 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300">Xóa</button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $notes->links() }}
    </div>
@endif

</x-student-layout>
