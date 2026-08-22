<x-instructor-layout :title="'Nội dung - '.$course->title" page-title="Quản lý nội dung khóa học" :breadcrumb="$course->title">

<script src="{{ asset('js/s3-multipart-uploader.js') }}"></script>

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

        @if(isset($pendingContentUpdates) && $pendingContentUpdates->isNotEmpty())
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                <h4 class="text-sm font-bold text-amber-900">Các yêu cầu cập nhật đang lưu nháp / chờ duyệt ({{ $pendingContentUpdates->count() }}):</h4>
                <ul class="mt-2 space-y-2 text-xs text-amber-900">
                    @foreach($pendingContentUpdates as $pUpdate)
                        <li class="flex items-center justify-between rounded-md bg-amber-100/60 p-2 border border-amber-200" x-data="{ showModal: false }">
                            <div>
                                <span class="font-bold uppercase text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded text-[11px]">{{ $pUpdate->type }}</span>
                                <span class="font-semibold text-slate-700">({{ $pUpdate->action }})</span>:
                                <strong class="text-slate-900 font-bold">@if(isset($pUpdate->payload['title'])) "{{ $pUpdate->payload['title'] }}" @else #{{ $pUpdate->entity_id }} @endif</strong>
                                - <span class="font-bold text-amber-800">{{ $pUpdate->submitted_at ? 'Đã gửi Admin duyệt' : 'Đang lưu nháp' }}</span>
                                @if($pUpdate->rejection_reason) <span class="text-rose-600 block text-[11px] font-semibold mt-0.5">Lý do từ chối trước đó: {{ $pUpdate->rejection_reason }}</span> @endif
                            </div>
                            <button type="button" @@click="showModal = true" class="rounded bg-amber-700 px-2.5 py-1 text-[11px] font-bold text-white hover:bg-amber-800 cursor-pointer">
                                Xem bản nháp
                            </button>

                            <!-- Modal Xem chi tiết bản nháp -->
                            <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 text-left" x-cloak>
                                <div class="w-full max-w-lg rounded-xl bg-white p-5 shadow-xl space-y-3">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <h3 class="text-sm font-bold text-slate-900">Chi tiết bản nháp {{ strtoupper($pUpdate->type) }} #{{ $pUpdate->id }}</h3>
                                        <span class="text-xs uppercase font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $pUpdate->action }}</span>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-semibold text-slate-500 uppercase">Nội dung chi tiết (Payload):</h4>
                                        <div class="mt-2 max-h-56 overflow-y-auto rounded-lg bg-slate-900 p-3 text-xs font-mono text-emerald-400 whitespace-pre-wrap">{{ json_encode($pUpdate->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="button" @@click="showModal = false" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

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
            <button type="submit"
                    class="inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
                + Thêm chương
            </button>
        </div>
    </form>

    <div class="space-y-5">
        @forelse($curriculumSections as $section)
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
                                            <input type="text" name="title" value="{{ $section->title }}" class="w-full rounded-lg border px-3 py-2 text-sm outline-none @error('title', 'updateSection_'.$section->id) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">
                                            @error('title', 'updateSection_'.$section->id) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                                        </label>
                                        <label class="block">
                                            <span class="mb-1 block text-xs font-bold text-slate-600">Mô tả</span>
                                            <textarea name="description" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm outline-none @error('description', 'updateSection_'.$section->id) border-rose-500 focus:border-rose-500 @else border-slate-300 focus:border-emerald-500 @enderror">{{ $section->description }}</textarea>
                                            @error('description', 'updateSection_'.$section->id) <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
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
                                        @php
                                            $lessonUpdate = $lesson->draft_update ?? null;
                                            if (!$lessonUpdate && isset($lesson->id)) {
                                                $lessonUpdate = \App\Models\ContentUpdate::where('course_id', $course->id)
                                                    ->where('entity_id', $lesson->id)
                                                    ->latest()
                                                    ->first();
                                            }
                                            $lPayload = $lessonUpdate?->payload ?? [];
                                            $effectiveReviewStatus = $lPayload['review_status'] ?? null;
                                            if (!$effectiveReviewStatus && isset($lesson->update_status)) {
                                                if ($lesson->update_status === 'rejected') $effectiveReviewStatus = 'fail';
                                                elseif ($lesson->update_status === 'approved') $effectiveReviewStatus = 'pass';
                                            }
                                        @endphp
                                        @if($effectiveReviewStatus === 'pass')
                                            <span class="rounded-full border border-emerald-300 bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800">Đạt</span>
                                        @elseif($effectiveReviewStatus === 'need_revision')
                                            <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Cần chỉnh sửa</span>
                                        @elseif($effectiveReviewStatus === 'fail')
                                            <span class="rounded-full border border-rose-300 bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">Từ chối</span>
                                        @elseif(isset($lesson->update_status))
                                            @if($lesson->update_status === 'draft')
                                                <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Draft</span>
                                            @elseif($lesson->update_status === 'pending')
                                                <span class="rounded-full border border-blue-300 bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">Đã gửi duyệt</span>
                                            @endif
                                        @else
                                            <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $statusClass }}">{{ $lessonStatuses[$lesson->status] ?? $lesson->status }}</span>
                                        @endif
                                        @if($lesson->is_preview)
                                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Xem thử</span>
                                        @endif
                                        @if($lesson->type === 'video')
                                            @php
                                                $hasVideoContent = filled($lesson->original_video_key) || filled($lesson->hls_manifest_key) || filled($lesson->video_path) || filled($lesson->video_url);
                                            @endphp
                                            <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $hasVideoContent ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-amber-200 bg-amber-50 text-amber-700' }}">
                                                {{ $hasVideoContent ? 'Đã có video' : 'Chưa có video' }}
                                            </span>
                                        @endif
                                    </div>
                                    <h4 class="mt-2 font-bold text-slate-950">{{ $lesson->title }}</h4>
                                    <div class="mt-1 flex flex-wrap gap-3 text-xs text-slate-500">
                                        <span>Thời lượng: {{ $formatDuration($lesson->duration ?? $lesson->duration_seconds) }}</span>
                                        <span>Bài {{ $lesson->sort_order }}</span>
                                        @if($lesson->type === 'video' && ($lesson->original_video_key || $lesson->hls_manifest_key || $lesson->video_path))
                                            <span class="font-semibold text-emerald-600">
                                                @if($lesson->processing_status === 'completed' || $lesson->hls_manifest_key || (!empty($lesson->video_path) && !\Illuminate\Support\Str::endsWith($lesson->video_path, '.mp4')))
                                                    Video S3 HLS (Đã bảo mật)
                                                @elseif($lesson->processing_status === 'failed')
                                                    Video S3 (Lỗi xử lý HLS)
                                                @else
                                                    Video S3 (Đang chờ xử lý HLS)
                                                @endif
                                            </span>
                                        @elseif($lesson->type === 'video' && $lesson->video_url)
                                            <a href="{{ $lesson->video_url }}" target="_blank" class="font-semibold text-indigo-600 hover:underline">Video URL</a>
                                        @endif
                                        @if(in_array($lesson->type, ['document', 'assignment'], true) && $lesson->document_file)
                                            <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="font-semibold text-sky-600 hover:underline">Tài liệu</a>
                                        @endif
                                    </div>
                                    @if($lesson->content && $lesson->type !== 'quiz')
                                        <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600">{{ $lesson->content }}</p>
                                    @endif

                                    {{-- Ghi chú kiểm duyệt của Admin dành riêng cho bài học này --}}
                                    @php
                                        $adminNote = $lPayload['admin_note'] ?? null;
                                        $requireReupload = !empty($lPayload['require_reupload']);
                                        $reviewStatus = $effectiveReviewStatus;
                                        $hasAdminFeedback = filled($adminNote) || $requireReupload || filled($reviewStatus);
                                    @endphp

                                    @if($hasAdminFeedback)
                                        @php
                                            $cardBoxStyle = match($reviewStatus) {
                                                'pass' => [
                                                    'wrapper' => 'border-emerald-200 bg-emerald-50/90',
                                                    'title' => 'text-emerald-950',
                                                    'badge' => 'bg-emerald-200/80 text-emerald-900',
                                                    'badge_text' => 'Đạt',
                                                    'icon' => '✅',
                                                    'note_box' => 'border-emerald-200 bg-white text-slate-800',
                                                    'note_title' => 'text-emerald-900',
                                                ],
                                                'need_revision' => [
                                                    'wrapper' => 'border-amber-200 bg-amber-50/90',
                                                    'title' => 'text-amber-950',
                                                    'badge' => 'bg-amber-200/80 text-amber-900',
                                                    'badge_text' => 'Cần chỉnh sửa',
                                                    'icon' => '⚠️',
                                                    'note_box' => 'border-amber-200 bg-white text-slate-800',
                                                    'note_title' => 'text-amber-900',
                                                ],
                                                default => [
                                                    'wrapper' => 'border-rose-200 bg-rose-50/90',
                                                    'title' => 'text-rose-950',
                                                    'badge' => 'bg-rose-200/80 text-rose-900',
                                                    'badge_text' => 'Từ chối',
                                                    'icon' => '❌',
                                                    'note_box' => 'border-rose-200 bg-white text-slate-800',
                                                    'note_title' => 'text-rose-900',
                                                ],
                                            };
                                        @endphp
                                        <div class="mt-4 max-w-2xl rounded-xl border {{ $cardBoxStyle['wrapper'] }} p-4 shadow-2xs">
                                            <div class="flex items-start gap-3">
                                                <span class="text-xl">{{ $cardBoxStyle['icon'] }}</span>
                                                <div class="w-full">
                                                    <div class="flex items-center justify-between">
                                                        <h5 class="text-sm font-bold {{ $cardBoxStyle['title'] }}">Phản hồi từ Admin</h5>
                                                        <span class="rounded-full {{ $cardBoxStyle['badge'] }} px-2.5 py-0.5 text-xs font-bold">{{ $cardBoxStyle['badge_text'] }}</span>
                                                    </div>
                                                    
                                                    @if(filled($adminNote))
                                                        <div class="mt-2.5 rounded-lg border {{ $cardBoxStyle['note_box'] }} p-3 text-xs leading-relaxed font-medium shadow-2xs">
                                                            <p class="font-bold {{ $cardBoxStyle['note_title'] }} mb-1">Ghi chú từ Admin:</p>
                                                            <div class="whitespace-pre-line text-slate-700">{!! nl2br(e($adminNote)) !!}</div>
                                                        </div>
                                                    @endif

                                                    @if($requireReupload)
                                                        <p class="mt-2.5 text-xs font-bold text-rose-700 flex items-center gap-1.5">
                                                            <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                            </svg>
                                                            <span>Yêu cầu: Vui lòng upload lại video gốc</span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @php
                                    $isDraftCreate = !empty($lesson->is_draft_create) && isset($lesson->draft_update);
                                    $updateActionUrl = $isDraftCreate
                                        ? route('instructor.courses.content-updates.update', [$course, $lesson->draft_update])
                                        : route('instructor.courses.lessons.update', [$course, $lesson->id]);
                                    $destroyActionUrl = $isDraftCreate
                                        ? route('instructor.courses.content-updates.destroy', [$course, $lesson->draft_update])
                                        : route('instructor.courses.lessons.destroy', [$course, $lesson->id]);
                                    $errorBagKey = 'updateLesson_' . ($isDraftCreate ? 'update_' . $lesson->draft_update->id : $lesson->id);
                                @endphp

                                <div class="flex shrink-0 flex-wrap gap-2">
                                    @if($lesson->type === 'quiz' && ! $isDraftCreate)
                                        <a href="{{ route('instructor.courses.lessons.quiz.show', [$course, $lesson]) }}"
                                           class="inline-flex min-h-10 items-center justify-center rounded-lg border border-violet-200 px-4 py-2 text-sm font-bold text-violet-700 transition-colors duration-200 hover:bg-violet-50 cursor-pointer">
                                            Quản lý câu hỏi
                                        </a>
                                    @endif
                                    <details {{ $errors->hasBag($errorBagKey) ? 'open' : '' }}>
                                        <summary class="inline-flex min-h-10 cursor-pointer list-none items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50">
                                            Sửa bài học
                                        </summary>
                                        <div class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-4 lg:w-[620px]">
                                            @include('instructor.courses.partials.lesson-form', [
                                                'course' => $course,
                                                'action' => $updateActionUrl,
                                                'method' => 'PUT',
                                                'lesson' => $lesson,
                                                'errorBag' => $errorBagKey,
                                                'lessonTypes' => $lessonTypes,
                                                'lessonStatuses' => $lessonStatuses,
                                                'submitLabel' => 'Lưu bài học',
                                            ])
                                        </div>
                                    </details>
                                    <form method="POST" action="{{ $destroyActionUrl }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa bài học này?')">
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

<script src="{{ asset('js/s3-multipart-uploader.js') }}"></script>
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
</script>

</x-instructor-layout>
