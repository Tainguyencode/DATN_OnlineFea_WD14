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

    $courseModel = $course ?? $lesson?->course ?? null;
    $createMultipartUrl = $courseModel ? route('instructor.courses.s3.multipart.create', $courseModel) : '';
    $signPartUrl = $courseModel ? route('instructor.courses.s3.multipart.sign-part', $courseModel) : '';
    $completeMultipartUrl = $courseModel ? route('instructor.courses.s3.multipart.complete', $courseModel) : '';
    $abortMultipartUrl = $courseModel ? route('instructor.courses.s3.multipart.abort', $courseModel) : '';
@endphp

<form method="POST"
      action="{{ $action }}"
      enctype="multipart/form-data"
      @submit="submitLessonForm($event)"
      class="space-y-4"
      x-data="createLessonFormState({
          selectedType: @js($selectedType),
          s3Key: @js($valueFor('s3_key', $lesson->original_video_key ?? '')),
          videoOriginalName: @js($valueFor('video_original_name', $lesson->video_original_name ?? '')),
          videoSize: @js($valueFor('video_size', $lesson->video_size ?? '')),
          videoMime: @js($valueFor('video_mime', $lesson->video_mime ?? '')),
          processingStatus: @js($lesson?->processing_status ?? ''),
          hlsManifestKey: @js($lesson?->hls_manifest_key ?? ''),
          videoPath: @js($lesson?->video_path ?? ''),
          courseId: @js($courseModel?->id),
          lessonId: @js($lesson?->id),
          maxVideoBytes: @js((int) config('video.upload.max_bytes')),
          createUrl: @js($createMultipartUrl),
          signPartUrl: @js($signPartUrl),
          completeUrl: @js($completeMultipartUrl),
          abortUrl: @js($abortMultipartUrl)
      })"
      data-lesson-form
      data-initial-type="{{ $selectedType ?: 'none' }}">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    {{-- S3 Hidden Metadata Inputs --}}
    <input type="hidden" name="s3_key" x-model="s3Key">
    <input type="hidden" name="video_original_name" x-model="videoOriginalName">
    <input type="hidden" name="video_size" x-model="videoSize">
    <input type="hidden" name="video_mime" x-model="videoMime">

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
                <span class="mb-1.5 block text-sm font-bold text-slate-700">Thời lượng (giây)</span>
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

    {{-- VIDEO PANEL --}}
    <fieldset x-show="selectedType === 'video'"
              x-cloak
              x-transition
              x-bind:disabled="selectedType !== 'video'"
              data-lesson-content-panel="video"
              class="rounded-lg border border-indigo-100 bg-indigo-50/60 p-4">
        <div class="mb-4">
            <h4 class="text-sm font-extrabold text-indigo-950 flex items-center justify-between">
                <span>Nội dung video bài giảng</span>
                <span class="text-[11px] font-bold text-indigo-600 bg-indigo-100/80 px-2 py-0.5 rounded-full">AWS S3 Multipart Upload</span>
            </h4>
            <p class="mt-1 text-xs font-medium text-indigo-700">Tải file video bài giảng lên Amazon S3 (mỗi bài học gắn 1 video, hệ thống tự động xử lý nền theo hàng chờ).</p>
        </div>

        {{-- S3 Multipart Uploader Box --}}
        <div class="rounded-xl border border-indigo-200 bg-white p-4 shadow-sm space-y-3">
            {{-- Vùng chọn File (1 file duy nhất) --}}
            <template x-if="!s3Key && !isUploading">
                <div>
                    <label class="flex flex-col items-center justify-center border-2 border-dashed border-indigo-300 rounded-xl p-6 hover:bg-indigo-50/50 transition cursor-pointer text-center">
                        <div class="h-12 w-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mb-3 shadow-inner">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-800">Nhấn để chọn video cho bài học này</span>
                        <span class="text-xs text-slate-500 mt-1">MP4, MOV, AVI, WEBM, MKV — tối đa {{ number_format(config('video.upload.max_bytes') / 1048576, 0) }}MB</span>
                        <input type="file"
                               x-ref="s3FileInput"
                               accept=".mp4,.mov,.avi,.webm,.m4v,.mkv,video/*"
                               @change="startS3Upload($event)"
                               class="hidden">
                    </label>
                </div>
            </template>

            {{-- Trạng thái đang tải lên (Upload Progress & Speed & ETA) --}}
            <template x-if="isUploading">
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs font-bold">
                        <span class="text-indigo-900 flex items-center gap-1.5 truncate max-w-sm">
                            <svg class="animate-spin h-3.5 w-3.5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="uploadStatusMessage || ('Đang tải: ' + videoOriginalName)"></span>
                        </span>
                        <span class="text-indigo-600 font-mono" x-text="uploadProgress + '%'"></span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-indigo-100">
                        <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-2.5 rounded-full transition-all duration-300 ease-out"
                             :style="`width: ${uploadProgress}%`"></div>
                    </div>

                    {{-- Live Metrics (Bytes, Speed, ETA) --}}
                    <div class="flex flex-wrap items-center justify-between text-[11px] font-semibold text-slate-500 pt-1">
                        <div class="flex items-center gap-2">
                            <span>Đã tải: <strong class="text-slate-800" x-text="uploadedBytesFormatted"></strong> / <span x-text="totalBytesFormatted"></span></span>
                            <span class="text-slate-300">|</span>
                            <span>Tốc độ: <strong class="text-indigo-600" x-text="uploadSpeedFormatted || 'Đang tính...'"></strong></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span x-show="uploadEtaFormatted">Còn lại: <strong class="text-slate-700" x-text="uploadEtaFormatted"></strong></span>
                            <button type="button"
                                    @click="cancelS3Upload()"
                                    class="text-rose-600 hover:text-rose-700 font-bold hover:underline cursor-pointer">
                                Hủy tải lên
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Trạng thái Video ĐÃ TẢI LÊN S3 thành công cho bài học này --}}
            <template x-if="s3Key && !isUploading">
                <div class="flex items-start justify-between bg-emerald-50/80 border border-emerald-200 rounded-lg p-3">
                    <div class="flex items-start gap-2.5">
                        <div class="h-8 w-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 mt-0.5">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-emerald-950">Video đã tải lên Amazon S3 an toàn và sẵn sàng lưu bài học</p>
                            <p class="text-xs text-emerald-800 font-medium mt-0.5" x-text="videoOriginalName || 'Video file'"></p>
                            <p class="text-[11px] text-emerald-700 font-mono mt-0.5 truncate max-w-sm" x-text="'Key: ' + s3Key"></p>
                        </div>
                    </div>
                    <button type="button"
                            @click="resetVideoSelection()"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline cursor-pointer">
                        Đổi video khác
                    </button>
                </div>
            </template>

            {{-- Trạng thái lỗi --}}
            <template x-if="uploadStatus === 'error' && !s3Key">
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-3 flex items-center justify-between text-xs text-rose-700">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="uploadStatusMessage"></span>
                    </div>
                    <button type="button"
                            @click="resetVideoSelection()"
                            class="font-bold text-rose-800 hover:underline">
                        Thử lại
                    </button>
                </div>
            </template>
        </div>

        {{-- Video hiện tại của bài học (nếu đang chỉnh sửa) --}}
        @if($lesson?->video_path || $lesson?->original_video_key)
            <div class="mt-3 rounded-lg border border-slate-200 bg-white p-3 shadow-2xs">
                <div class="font-bold text-xs flex items-center gap-2 text-slate-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span>Video hiện tại: {{ $lesson->video_original_name ?: basename($lesson->original_video_key ?: $lesson->video_path) }}</span>
                    @if($formatVideoSize($lesson->video_size))
                        <span class="text-slate-500 font-normal">({{ $formatVideoSize($lesson->video_size) }})</span>
                    @endif
                </div>
            </div>
        @endif

        @error('video_file', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
        @error('s3_key', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror

        <label class="mt-4 block">
            <span class="mb-1.5 block text-sm font-bold text-slate-700">Ghi chú / Nội dung kèm theo video</span>
            <textarea name="content" rows="3" class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition-colors duration-200 focus-visible:ring-2 @error('content', $bagName) border-rose-500 focus:border-rose-500 focus-visible:ring-rose-500/20 @else border-slate-300 focus:border-indigo-500 focus-visible:ring-indigo-500/20 @enderror">{{ $contentValue }}</textarea>
            @error('content', $bagName) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
        </label>
    </fieldset>

    {{-- DOCUMENT PANEL --}}
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

    {{-- QUIZ PANEL --}}
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
            <a href="{{ route('instructor.courses.lessons.quiz.show', [$courseModel ?? $lesson->course, $lesson]) }}"
               class="mt-4 inline-flex min-h-10 items-center justify-center rounded-lg border border-violet-200 bg-white px-4 py-2 text-sm font-bold text-violet-700 transition-colors duration-200 hover:bg-violet-50 cursor-pointer">
                Quản lý câu hỏi
            </a>
        @endif
    </fieldset>

    {{-- ASSIGNMENT PANEL --}}
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

        <!-- Ẩn các trường điểm số và lưu giá trị mặc định -->
        <input type="hidden" name="assignment_due_days" value="{{ $valueFor('assignment_due_days', $assignment->due_days ?? 1) }}">
        <input type="hidden" name="assignment_max_score" value="{{ $valueFor('assignment_max_score', $assignment->max_score ?? 100) }}">
        <input type="hidden" name="assignment_passing_score" value="{{ $valueFor('assignment_passing_score', $assignment->passing_score ?? 70) }}">

        <div class="mt-4 rounded-xl border border-indigo-200 bg-indigo-50/70 p-4 text-xs text-indigo-950 space-y-1.5">
            <div class="flex items-center gap-2 font-bold text-indigo-900">
                <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Quy tắc làm bài & Đánh giá bài thực hành</span>
            </div>
            <p class="text-slate-600 leading-relaxed">
                • <strong>Thời gian làm bài:</strong> 6 Giờ (tự động kích hoạt khi học viên bấm tải tài liệu về máy).<br>
                • <strong>Cơ chế đánh giá:</strong> Giảng viên chấm bài với 2 trạng thái <strong>PASS</strong> (Đạt) hoặc <strong>FAIL</strong> (Không đạt).
            </p>
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

    <button type="submit"
            class="inline-flex min-h-10 items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
        <span>{{ $submitLabel }}</span>
    </button>
</form>
