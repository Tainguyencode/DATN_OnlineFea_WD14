@php
    $bagName = $errorBag ?? 'default';
    $formErrors = $errors->getBag($bagName);
    $hasFormErrors = $formErrors->any();
    $selectedType = $hasFormErrors ? old('type', $lesson->type ?? '') : ($lesson->type ?? '');
    $selectedStatus = $hasFormErrors ? old('status', $lesson->status ?? 'draft') : ($lesson->status ?? 'draft');
    $valueFor = fn ($field, $default = null) => $hasFormErrors ? old($field, $default) : $default;
    $checkedFor = fn ($field, $default = false) => (bool) ($hasFormErrors ? old($field, $default) : $default);
    $assignment = $lesson?->assignment;
    $contentValue = $valueFor('content', $lesson->content ?? '');
    $documentAccept = '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar';
    $formatVideoSize = function ($bytes) {
        if (! $bytes) {
            return null;
        }

        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 2).' MB'
            : number_format($bytes / 1024, 1).' KB';
    };
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      class="space-y-4"
      x-data="{ selectedType: @js($selectedType) }"
      data-lesson-form
      data-initial-type="{{ $selectedType ?: 'none' }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="rounded-lg border border-slate-200 bg-white p-4">
        <div class="grid gap-4 lg:grid-cols-2">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Tên bài học</span>
                <input type="text" name="title" value="{{ $valueFor('title', $lesson->title ?? '') }}" maxlength="255"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('title', $bagName) border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-emerald-500 focus-visible:ring-emerald-500/20 @enderror">
                @error('title', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Loại bài học</span>
                <select name="type"
                        x-model="selectedType"
                        class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 cursor-pointer @error('type', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">
                    <option value="">Chọn loại bài học</option>
                    @foreach($lessonTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('type', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Thời lượng</span>
                <input type="number" name="duration" value="{{ $valueFor('duration', $lesson->duration ?? $lesson->duration_seconds ?? '') }}" min="0"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 @error('duration', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">
                @error('duration', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Thứ tự</span>
                <input type="number" name="sort_order" value="{{ $valueFor('sort_order', $lesson->sort_order ?? $nextSortOrder ?? '') }}" min="0"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 @error('sort_order', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">
                @error('sort_order', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Trạng thái</span>
                <select name="status" class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 cursor-pointer @error('status', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">
                    @foreach($lessonStatuses as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="inline-flex min-h-11 items-center gap-2 self-end rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700">
                <input type="checkbox" name="is_preview" value="1" @checked($checkedFor('is_preview', $lesson->is_preview ?? false)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                Bài xem thử
            </label>
        </div>
    </div>

    <fieldset x-show="selectedType === 'video'"
              x-cloak
              x-transition
              x-bind:disabled="selectedType !== 'video'"
              data-lesson-content-panel="video"
              class="rounded-lg border border-indigo-100 bg-indigo-50/60 p-4">
        <div class="mb-4">
            <h4 class="text-sm font-extrabold text-indigo-950">Nội dung video</h4>
            <p class="mt-1 text-xs font-medium text-indigo-700">Nhập Video URL hoặc tải file video lên.</p>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Video URL</span>
                <input type="url" name="video_url" value="{{ $valueFor('video_url', $lesson->video_url ?? '') }}" placeholder="https://..."
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('video_url', $bagName) border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-indigo-500 focus-visible:ring-indigo-500/20 @enderror">
                @error('video_url', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Video bài giảng</span>
                <input type="file" name="video_file" accept=".mp4,.mov,.avi,.webm,video/mp4,video/quicktime,video/x-msvideo,video/webm"
                       class="block w-full cursor-pointer rounded-lg border bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-indigo-700 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-indigo-800 @error('video_file', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 @enderror">
                <span class="mt-1 block text-xs font-medium text-slate-500">MP4, MOV, AVI hoặc WEBM. Tối đa 200MB.</span>
                @error('video_file', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>
        </div>

        @if($lesson?->video_path)
            <div class="mt-4 rounded-lg border border-indigo-100 bg-white px-3 py-2 text-sm text-indigo-900">
                <div class="font-bold">
                    Video hiện tại: {{ $lesson->video_original_name ?: basename($lesson->video_path) }}
                    @if($formatVideoSize($lesson->video_size))
                        <span class="font-semibold text-indigo-700">({{ $formatVideoSize($lesson->video_size) }})</span>
                    @endif
                </div>
                <p class="mt-1 text-xs font-medium text-indigo-700">Upload video mới để thay thế file hiện tại.</p>
            </div>
        @endif

        <label class="mt-4 block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Ghi chú video</span>
            <textarea name="content" rows="3" class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('content', $bagName) border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-indigo-500 focus-visible:ring-indigo-500/20 @enderror">{{ $contentValue }}</textarea>
            @error('content', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
        </label>
    </fieldset>

    <fieldset x-show="selectedType === 'document'"
              x-cloak
              x-transition
              x-bind:disabled="selectedType !== 'document'"
              data-lesson-content-panel="document"
              class="rounded-lg border border-sky-100 bg-sky-50/60 p-4">
        <div class="mb-4">
            <h4 class="text-sm font-extrabold text-sky-950">Nội dung tài liệu</h4>
            <p class="mt-1 text-xs font-medium text-sky-700">Nhập nội dung dạng text hoặc tải tệp tài liệu lên.</p>
        </div>

        <label class="block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Nội dung dạng text</span>
            <textarea name="content" rows="4" class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('content', $bagName) border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-sky-500 focus-visible:ring-sky-500/20 @enderror">{{ $contentValue }}</textarea>
            @error('content', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
        </label>

        <label class="mt-4 block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Tệp tài liệu</span>
            <input type="file" name="document_file" accept="{{ $documentAccept }}"
                   class="block w-full cursor-pointer rounded-lg border bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-900 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-slate-800 @error('document_file', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 @enderror">
            <span class="mt-1 block text-xs font-medium text-slate-500">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, ZIP hoặc RAR. Tối đa 10MB.</span>
            @if($lesson?->document_file)
                <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-sky-600 hover:underline">Tệp hiện tại</a>
            @endif
            @error('document_file', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
        </label>
    </fieldset>

    <fieldset x-show="selectedType === 'quiz'"
              x-cloak
              x-transition
              x-bind:disabled="selectedType !== 'quiz'"
              data-lesson-content-panel="quiz"
              class="rounded-lg border border-violet-100 bg-violet-50/60 p-4">
        <div>
            <h4 class="text-sm font-extrabold text-violet-950">Nội dung Quiz</h4>
            <p class="mt-1 text-xs font-medium text-violet-700">Sau khi lưu bài học, hệ thống sẽ mở màn hình quản lý câu hỏi và đáp án.</p>
        </div>

        @if($lesson?->exists)
            <a href="{{ route('instructor.courses.lessons.quiz.show', [$course, $lesson]) }}"
               class="mt-4 inline-flex min-h-10 items-center justify-center rounded-lg border border-violet-200 bg-white px-4 py-2 text-sm font-bold text-violet-700 transition-colors duration-200 hover:bg-violet-50 cursor-pointer">
                Quản lý câu hỏi
            </a>
        @endif
    </fieldset>

    <fieldset x-show="selectedType === 'assignment'"
              x-cloak
              x-transition
              x-bind:disabled="selectedType !== 'assignment'"
              data-lesson-content-panel="assignment"
              class="rounded-lg border border-amber-100 bg-amber-50/60 p-4">
        <div class="mb-4">
            <h4 class="text-sm font-extrabold text-amber-950">Nội dung bài tập</h4>
            <p class="mt-1 text-xs font-medium text-amber-700">Nhập yêu cầu bài tập, điểm và file đính kèm nếu cần.</p>
        </div>

        <label class="block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Yêu cầu bài tập</span>
            <textarea name="content" rows="4" class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('content', $bagName) border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-amber-500 focus-visible:ring-amber-500/20 @enderror">{{ $contentValue }}</textarea>
            @error('content', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
        </label>

        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Thời hạn nộp</span>
                <input type="number" name="assignment_due_days" value="{{ $valueFor('assignment_due_days', $assignment->due_days ?? '') }}" min="1" max="3650" placeholder="Số ngày"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 @error('assignment_due_days', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-amber-500 @enderror">
                @error('assignment_due_days', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Điểm tối đa</span>
                <input type="number" name="assignment_max_score" value="{{ $valueFor('assignment_max_score', $assignment->max_score ?? 100) }}" min="1" max="1000"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 @error('assignment_max_score', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-amber-500 @enderror">
                @error('assignment_max_score', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>

            <label class="block">
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Điểm đạt</span>
                <input type="number" name="assignment_passing_score" value="{{ $valueFor('assignment_passing_score', $assignment->passing_score ?? 70) }}" min="0" max="1000"
                       class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 @error('assignment_passing_score', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-amber-500 @enderror">
                @error('assignment_passing_score', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
            </label>
        </div>

        <label class="mt-4 block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">File đính kèm</span>
            <input type="file" name="document_file" accept="{{ $documentAccept }}"
                   class="block w-full cursor-pointer rounded-lg border bg-white text-sm text-slate-700 file:mr-4 file:border-0 file:bg-slate-900 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-white hover:file:bg-slate-800 @error('document_file', $bagName) border-rose-500 focus:border-rose-500 @else border-slate-300 @enderror">
            <span class="mt-1 block text-xs font-medium text-slate-500">PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, TXT, ZIP hoặc RAR. Tối đa 10MB.</span>
            @if($lesson?->document_file)
                <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="mt-1 inline-block text-xs font-semibold text-amber-700 hover:underline">File hiện tại</a>
            @endif
            @error('document_file', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
        </label>
    </fieldset>

    <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
        {{ $submitLabel }}
    </button>
</form>
