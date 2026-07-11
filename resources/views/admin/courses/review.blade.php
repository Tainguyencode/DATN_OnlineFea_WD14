@use('App\Models\CourseReviewItem')
@use('App\Models\Course')
<x-admin-layout :title="'Duyệt - '.$course->title" page-title="Kiểm duyệt khóa học" :breadcrumb="$course->title">

@php
    $formatPrice = fn ($value) => (float) $value <= 0 ? 'Miễn phí' : number_format((float) $value, 0, ',', '.').'đ';
    $price = $course->discount_price ?? $course->sale_price ?? $course->price;
    $levelLabels = ['beginner' => 'Cơ bản', 'intermediate' => 'Trung cấp', 'advanced' => 'Nâng cao'];
    $typeLabels = ['video' => 'Video', 'text' => 'Bài đọc', 'document' => 'Tài liệu', 'quiz' => 'Quiz', 'assignment' => 'Bài tập'];

    $formatDuration = function (int $seconds): string {
        if ($seconds <= 0) {
            return '0 phút';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = "{$hours} giờ";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes} phút";
        }
        if ($hours === 0 && $minutes === 0 && $remainingSeconds > 0) {
            $parts[] = "{$remainingSeconds} giây";
        }

        return implode(' ', $parts);
    };

    $suggestedStatuses = [
        \App\Models\CourseReviewItem::ITEM_COURSE_INFORMATION => filled(trim((string) $course->title)) && filled($course->category_id),
        \App\Models\CourseReviewItem::ITEM_THUMBNAIL          => filled($course->thumbnail),
        \App\Models\CourseReviewItem::ITEM_DESCRIPTION        => filled(trim(strip_tags((string) $course->description))),
        \App\Models\CourseReviewItem::ITEM_OBJECTIVES         => filled(trim(strip_tags((string) $course->objectives))),
        \App\Models\CourseReviewItem::ITEM_LESSON_COUNT       => $totalLessons >= \App\Models\Course::MIN_LESSON_COUNT,
        \App\Models\CourseReviewItem::ITEM_VIDEO_DURATION     => $totalVideoDurationMinutes >= \App\Models\Course::MIN_VIDEO_DURATION_MINUTES,
    ];
@endphp

<div class="space-y-6">
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-6 p-5 lg:grid-cols-[320px_minmax(0,1fr)_220px]">
            <div class="aspect-video overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-900 to-rose-700 text-sm font-bold text-white">Fea LMS</div>
                @endif
            </div>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Đang chờ duyệt</span>
                    <span class="text-xs font-semibold text-slate-500">{{ $course->category?->name ?? 'Chưa chọn danh mục' }}</span>
                    @if($course->submitted_at)
                        <span class="text-xs text-slate-400">Gửi lúc {{ $course->submitted_at->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
                <h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $course->title }}</h2>
                <p class="mt-2 text-sm text-slate-500">Giảng viên: {{ $course->instructor?->name }} · {{ $course->instructor?->email }}</p>
                <p class="mt-4 text-sm leading-6 text-slate-600">{{ $course->short_description ?: 'Chưa có mô tả ngắn.' }}</p>

                <dl class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Giá</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $formatPrice($price) }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Trình độ</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $levelLabels[$course->level] ?? 'Chưa chọn' }}</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Tổng bài học</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $totalLessons }} bài</dd>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Tổng thời lượng</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-950">{{ $formatDuration($totalVideoDurationSeconds) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.courses.pending') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 cursor-pointer">
                    Quay lại danh sách
                </a>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-bold text-slate-950">Mô tả chi tiết</h3>
            <div class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                {{ $course->description ?: 'Chưa có mô tả chi tiết.' }}
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-bold text-slate-950">Mục tiêu khóa học</h3>
            <div class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600">
                {{ $course->objectives ?: 'Chưa có mục tiêu.' }}
            </div>
        </section>
    </div>

    @if($course->preview_video)
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-lg font-bold text-slate-950">Video giới thiệu</h3>
                <a href="{{ $course->preview_video }}" target="_blank" class="text-sm font-bold text-indigo-600 hover:underline">Mở trong tab mới</a>
            </div>
            <div class="mt-4 aspect-video overflow-hidden rounded-lg border border-slate-200 bg-slate-950">
                @if(str_contains($course->preview_video, 'youtube.com') || str_contains($course->preview_video, 'youtu.be'))
                    @php
                        preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $course->preview_video, $matches);
                        $youtubeId = $matches[1] ?? null;
                    @endphp
                    @if($youtubeId)
                        <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" class="h-full w-full" allowfullscreen></iframe>
                    @else
                        <a href="{{ $course->preview_video }}" target="_blank" class="flex h-full items-center justify-center text-sm font-bold text-white">Xem video giới thiệu</a>
                    @endif
                @else
                    <video src="{{ $course->preview_video }}" controls class="h-full w-full"></video>
                @endif
            </div>
        </section>
    @endif

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-rose-600">Nội dung kiểm duyệt</p>
                <h3 class="mt-1 text-lg font-bold text-slate-950">Chương và bài học</h3>
            </div>
            <div class="flex flex-col items-stretch gap-2 sm:items-end">
                <span class="text-sm font-semibold text-slate-500">{{ $curriculumSections->count() }} chương · {{ $totalLessons }} bài · {{ $formatDuration($totalVideoDurationSeconds) }}</span>
                @if($videoLessons->isNotEmpty())
                    <button type="button"
                            id="btn-scan-course-ai"
                            data-video-lessons='@json($videoLessons)'
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-violet-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-violet-700 disabled:cursor-not-allowed disabled:opacity-50">
                        <span>🔍 Quét AI toàn bộ khóa học ({{ $videoLessons->count() }} video)</span>
                    </button>
                @endif
            </div>
        </div>

        @if($videoLessons->isNotEmpty())
            <div id="course-ai-progress-panel" class="mt-4 hidden rounded-lg border border-violet-200 bg-violet-50 p-4">
                <p id="course-ai-status-text" class="text-sm font-semibold text-violet-800">Đang khởi tạo...</p>
                <p id="course-ai-frame-text" class="mt-1 text-xs font-medium text-violet-700"></p>
                <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-violet-200">
                    <div id="course-ai-progress-bar" class="h-full bg-violet-600 transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="course-ai-percent-text" class="mt-2 text-xs font-bold text-violet-700">0%</p>
            </div>
        @endif

        <div class="mt-5 space-y-4">
            @forelse($curriculumSections as $section)
                <article class="overflow-hidden rounded-lg border border-slate-200">
                    <div class="bg-slate-50 px-4 py-3">
                        <h4 class="font-bold text-slate-950">{{ $section->title }}</h4>
                        @if($section->description)
                            <p class="mt-1 text-sm text-slate-500">{{ $section->description }}</p>
                        @endif
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($section->lessons as $lesson)
                            @php
                                $hasVideo = filled($lesson->video_path) || filled($lesson->video_url);
                                $lessonDuration = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);
                            @endphp
                            <div class="p-4">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-700">{{ $typeLabels[$lesson->type] ?? $lesson->type }}</span>
                                            <span class="rounded-full border px-2.5 py-1 text-xs font-bold {{ $hasVideo ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-500' }}">
                                                {{ $hasVideo ? 'Có video' : 'Chưa có video' }}
                                            </span>
                                            @if($lesson->is_preview)
                                                <span class="rounded-full border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">Xem thử</span>
                                            @endif
                                            @if($lessonDuration > 0)
                                                <span class="text-xs font-semibold text-slate-500">{{ $formatDuration($lessonDuration) }}</span>
                                            @endif
                                        </div>
                                        <h5 class="mt-2 font-bold text-slate-950">{{ $lesson->title }}</h5>
                                        @if($lesson->content)
                                            <p class="mt-2 line-clamp-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $lesson->content }}</p>
                                        @endif

                                        @if($lesson->type === 'video' && $lesson->video_path)
                                            <div class="mt-4 aspect-video max-w-xl overflow-hidden rounded-lg border border-slate-200 bg-slate-950">
                                                <video src="{{ asset('storage/'.$lesson->video_path) }}" controls class="h-full w-full"></video>
                                            </div>
                                            
                                            {{-- Nút và khu vực hiển thị quét AI --}}
                                            <div class="mt-4 max-w-xl ai-moderation-container" data-lesson-id="{{ $lesson->id }}">
                                                <button type="button" class="btn-scan-ai inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-indigo-700 disabled:opacity-50">
                                                    <span>Quét nội dung</span>
                                                </button>
                                                
                                                <div class="ai-progress-area hidden mt-3 rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                                                    <p class="ai-status-text text-sm font-semibold text-indigo-700">Đang khởi tạo...</p>
                                                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-indigo-200">
                                                        <div class="ai-progress-bar h-full bg-indigo-600 transition-all duration-300" style="width: 0%"></div>
                                                    </div>
                                                </div>

                                                <div class="ai-result-area mt-4 {{ $lesson->videoModeration ? '' : 'hidden' }}">
                                                    @if($lesson->videoModeration)
                                                        @php $mod = $lesson->videoModeration; @endphp
                                                        <div class="rounded-lg border {{ $mod->hasViolations() ? 'border-rose-200 bg-rose-50' : 'border-emerald-200 bg-emerald-50' }} p-4 shadow-sm">
                                                            <h6 class="font-bold text-slate-900 mb-2">Kết quả quét xong</h6>
                                                            <div class="flex flex-wrap gap-2 mb-3">
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->violence ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">Bạo lực: {{ $mod->violence ? 'Có' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->adult ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">18+: {{ $mod->adult ? 'Có' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->weapon ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">Vũ khí: {{ $mod->weapon ? 'Có' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->watermark ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">Watermark: {{ $mod->watermark ? 'Có' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->tiktok_logo ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">TikTok: {{ $mod->tiktok_logo ? 'Có' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->youtube_logo ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">YouTube: {{ $mod->youtube_logo ? 'Có' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->copyright_risk === 'high' ? 'bg-rose-100 text-rose-700' : ($mod->copyright_risk === 'medium' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">Bản quyền: {{ strtoupper($mod->copyright_risk) }}</span>
                                                            </div>
                                                            @if($mod->summary)
                                                                <p class="text-sm text-slate-700 mb-3"><span class="font-semibold">Tóm tắt:</span> {{ $mod->summary }}</p>
                                                            @endif
                                                            
                                                            @php $violatedFrames = $mod->violatedFrameDetails(); @endphp
                                                            @if(!empty($violatedFrames))
                                                                <div class="text-xs text-slate-600 bg-white rounded border border-slate-200 p-2 max-h-32 overflow-y-auto mt-3">
                                                                    <p class="font-semibold mb-1 text-slate-900">Chi tiết lỗi các khung hình:</p>
                                                                    <ul class="list-disc pl-4 space-y-1">
                                                                    @foreach($violatedFrames as $vf)
                                                                        <li>
                                                                            <span class="font-bold text-slate-800">{{ $vf['timestamp'] }}</span>: 
                                                                            <span class="text-rose-700 font-semibold">{{ implode(', ', $vf['labels']) }}</span>
                                                                            @if(!empty($vf['reason'])) - <span class="text-slate-600">{{ $vf['reason'] }}</span> @endif
                                                                        </li>
                                                                    @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        @if($lesson->video_url)
                                            <a href="{{ $lesson->video_url }}" target="_blank" class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-50">Xem video URL</a>
                                        @endif
                                        @if($lesson->video_path)
                                            <a href="{{ asset('storage/'.$lesson->video_path) }}" target="_blank" class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50">Tải video file</a>
                                        @endif
                                        @if($lesson->document_file)
                                            <a href="{{ asset('storage/'.$lesson->document_file) }}" target="_blank" class="rounded-lg border border-sky-200 px-3 py-2 text-xs font-bold text-sky-700 hover:bg-sky-50">Xem tài liệu</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-sm text-slate-500">Chương này chưa có bài học.</div>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Khóa học chưa có chương học.</div>
            @endforelse
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h3 class="text-lg font-bold text-slate-950">Tài liệu đính kèm</h3>
        @if($attachments->isEmpty())
            <p class="mt-3 text-sm text-slate-500">Không có tài liệu đính kèm trong các bài học.</p>
        @else
            <ul class="mt-4 divide-y divide-slate-100 rounded-lg border border-slate-200">
                @foreach($attachments as $attachment)
                    <li class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $attachment['name'] }}</p>
                            <p class="text-xs text-slate-500">Bài học: {{ $attachment['lesson_title'] }}</p>
                        </div>
                        <a href="{{ $attachment['url'] }}" target="_blank" class="inline-flex min-h-9 items-center rounded-lg border border-sky-200 px-3 text-xs font-bold text-sky-700 hover:bg-sky-50">Tải xuống</a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Checklist + nút hành động --}}
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Checklist đánh giá</p>
                <h3 class="mt-1 text-lg font-bold text-slate-950">Kiểm tra từng mục trước khi quyết định</h3>
            </div>
            <p class="text-xs text-slate-500">Các mục có gợi ý tự động được đánh dấu sẵn theo dữ liệu khóa học.</p>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <p class="font-bold">Vui lòng kiểm tra lại thông tin:</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('admin.courses.submitReview', $course) }}"
              id="course-review-form"
              class="mt-5 space-y-6">
            @csrf

            {{-- Hidden input nhận giá trị action từ JS khi click button --}}
            <input type="hidden" name="action" id="review-action-input" value="">

            <div class="overflow-hidden rounded-lg border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Mục kiểm tra</th>
                            <th class="px-4 py-3 w-36">Kết quả</th>
                            <th class="px-4 py-3">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($checklistKeys as $itemKey)
                            @php
                                $label = $checklistLabels[$itemKey] ?? $itemKey;
                                $suggestedPass = $suggestedStatuses[$itemKey] ?? null;
                                $defaultStatus = old("checklist.{$itemKey}.status",
                                    $suggestedPass === true ? 'pass' : ($suggestedPass === false ? 'fail' : 'pass')
                                );
                            @endphp
                            <tr>
                                <td class="px-4 py-4 align-top">
                                    <p class="font-bold text-slate-950">{{ $label }}</p>
                                    @if($itemKey === CourseReviewItem::ITEM_LESSON_COUNT)
                                        <p class="mt-1 text-xs text-slate-500">
                                            Hiện có {{ $totalLessons }} bài
                                            (tối thiểu {{ Course::MIN_LESSON_COUNT }})
                                        </p>
                                    @elseif($itemKey === CourseReviewItem::ITEM_VIDEO_DURATION)
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $formatDuration($totalVideoDurationSeconds) }}
                                            (tối thiểu {{ Course::MIN_VIDEO_DURATION_MINUTES }} phút)
                                        </p>
                                    @endif
                                    @error("checklist.{$itemKey}.status")
                                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-col gap-2.5">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio"
                                                   name="checklist[{{ $itemKey }}][status]"
                                                   value="pass"
                                                   id="check-{{ $itemKey }}-pass"
                                                   @checked($defaultStatus === 'pass')
                                                   class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            <span class="text-xs font-bold text-emerald-700">✓ PASS</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="radio"
                                                   name="checklist[{{ $itemKey }}][status]"
                                                   value="fail"
                                                   id="check-{{ $itemKey }}-fail"
                                                   @checked($defaultStatus === 'fail')
                                                   class="h-4 w-4 border-slate-300 text-rose-600 focus:ring-rose-500">
                                            <span class="text-xs font-bold text-rose-700">✗ FAIL</span>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-4 align-top">
                                    <textarea name="checklist[{{ $itemKey }}][note]"
                                              rows="2"
                                              maxlength="500"
                                              placeholder="Ghi chú cho mục này..."
                                              class="w-full resize-none rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus-visible:ring-2 focus-visible:ring-indigo-500/20">{{ old("checklist.{$itemKey}.note") }}</textarea>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <label for="review-comment" class="block text-sm font-bold text-slate-900">
                    Lý do / Ghi chú chung
                    <span class="ml-1 font-normal text-slate-500">(bắt buộc khi Yêu cầu chỉnh sửa hoặc Từ chối)</span>
                </label>
                <textarea id="review-comment"
                          name="comment"
                          rows="4"
                          maxlength="2000"
                          placeholder="Nhập lý do hoặc hướng dẫn chỉnh sửa cho giảng viên..."
                          class="mt-2 w-full resize-none rounded-lg border @error('comment') border-rose-400 @else border-slate-300 @enderror px-3 py-2 text-sm outline-none focus:border-indigo-500 focus-visible:ring-2 focus-visible:ring-indigo-500/20">{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:flex-wrap">
                {{-- Approve --}}
                <button type="button"
                        data-action="approved"
                        data-confirm="Duyệt khóa học này? Giảng viên sẽ được thông báo."
                        class="review-action-btn inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Duyệt (Approve)
                </button>

                {{-- Need Revision --}}
                <button type="button"
                        data-action="need_revision"
                        data-confirm="Yêu cầu chỉnh sửa? Giảng viên sẽ thấy lý do và có thể gửi lại."
                        class="review-action-btn inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-amber-500 px-6 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-amber-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 012.828 2.828L11.828 15.828a2 2 0 01-1.415.586H8v-2.414a2 2 0 01.586-1.414z"/>
                    </svg>
                    Yêu cầu chỉnh sửa
                </button>

                {{-- Reject --}}
                <button type="button"
                        data-action="rejected"
                        data-confirm="Từ chối vĩnh viễn khóa học này? Hành động này không thể hoàn tác."
                        class="review-action-btn inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-rose-600 px-6 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-rose-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Từ chối (Reject)
                </button>
            </div>
        </form>
    </section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Logic duyệt khóa học
    var form     = document.getElementById('course-review-form');
    var actionInput = document.getElementById('review-action-input');
    var buttons  = document.querySelectorAll('.review-action-btn');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var action  = btn.getAttribute('data-action');
            var message = btn.getAttribute('data-confirm');

            if (!confirm(message)) {
                return;
            }

            actionInput.value = action;
            form.submit();
        });
    });

    // 2. Logic Quét AI
    async function parseJsonResponse(response) {
        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            if (response.status === 419) {
                throw new Error('Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.');
            }
            throw new Error(`Máy chủ trả về phản hồi không hợp lệ (HTTP ${response.status}).`);
        }
        return response.json();
    }

    function aiFetchHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
        };
    }

    function setAiScanBusy(busy) {
        document.querySelectorAll('.btn-scan-ai, #btn-scan-course-ai').forEach(function (btn) {
            btn.disabled = busy;
        });
    }

    function calcCourseProgressPercent(videoIndex, totalVideos, phase, frameIndex, frameTotal) {
        if (totalVideos === 0) {
            return 0;
        }

        var withinVideo = 0;
        if (phase === 'extract') {
            withinVideo = 0.05;
        } else if (phase === 'analyze' && frameTotal > 0) {
            withinVideo = 0.05 + (0.85 * (frameIndex / frameTotal));
        } else if (phase === 'save') {
            withinVideo = 0.95;
        } else if (phase === 'done') {
            withinVideo = 1;
        }

        return Math.min(100, Math.round(((videoIndex + withinVideo) / totalVideos) * 100));
    }

    function updateCourseProgress(videoIndex, totalVideos, lessonTitle, phase, frameIndex, frameTotal) {
        var panel = document.getElementById('course-ai-progress-panel');
        var statusText = document.getElementById('course-ai-status-text');
        var frameText = document.getElementById('course-ai-frame-text');
        var progressBar = document.getElementById('course-ai-progress-bar');
        var percentText = document.getElementById('course-ai-percent-text');

        if (!panel || !statusText || !frameText || !progressBar || !percentText) {
            return;
        }

        panel.classList.remove('hidden');

        var percent = calcCourseProgressPercent(videoIndex, totalVideos, phase, frameIndex, frameTotal);
        statusText.innerText = 'Đang quét video ' + (videoIndex + 1) + ' / ' + totalVideos + ': ' + lessonTitle;

        if (phase === 'extract') {
            frameText.innerText = 'Đang cắt frame (mỗi 30s)...';
        } else if (phase === 'analyze') {
            frameText.innerText = 'Frame ' + frameIndex + ' / ' + frameTotal;
        } else if (phase === 'save') {
            frameText.innerText = 'Đang lưu kết quả...';
        } else if (phase === 'done') {
            frameText.innerText = 'Hoàn thành video này.';
        } else {
            frameText.innerText = '';
        }

        progressBar.style.width = percent + '%';
        percentText.innerText = percent + '%';
    }

    async function scanLesson(lessonId, onProgress) {
        onProgress({ phase: 'extract', frameIndex: 0, frameTotal: 0 });

        var extRes = await fetch('/admin/ai-moderation/' + lessonId + '/extract', {
            method: 'POST',
            headers: aiFetchHeaders(),
        });

        var extData = await parseJsonResponse(extRes);
        if (!extRes.ok) {
            throw new Error(extData.error || 'Lỗi cắt frame');
        }

        var frames = extData.frames;
        var total = extData.total;

        if (total === 0) {
            throw new Error('Không cắt được frame nào');
        }

        var aiResults = [];
        var lastApiError = '';

        for (var i = 0; i < total; i++) {
            var framePath = frames[i];
            var match = framePath.match(/frame_(\d+)\.jpg$/);
            var timestamp = match ? parseInt(match[1], 10) : i * 30;

            onProgress({ phase: 'analyze', frameIndex: i + 1, frameTotal: total });

            var anRes = await fetch('/admin/ai-moderation/analyze-frame', {
                method: 'POST',
                headers: aiFetchHeaders(),
                body: JSON.stringify({ frame_path: framePath, timestamp: timestamp }),
            });

            var anData = await parseJsonResponse(anRes);
            if (anRes.ok && !anData.error) {
                aiResults.push(anData);
            } else if (anData.error) {
                lastApiError = anData.error;
            }
        }

        if (aiResults.length === 0) {
            throw new Error(
                lastApiError
                    ? 'Không phân tích được frame nào: ' + lastApiError
                    : 'Không phân tích được frame nào. Kiểm tra lại API key OpenRouter hoặc quota.'
            );
        }

        onProgress({ phase: 'save', frameIndex: total, frameTotal: total });

        var saveRes = await fetch('/admin/ai-moderation/' + lessonId + '/save', {
            method: 'POST',
            headers: aiFetchHeaders(),
            body: JSON.stringify({ results: aiResults }),
        });

        var saveData = await parseJsonResponse(saveRes);
        if (!saveRes.ok) {
            throw new Error(saveData.error || saveData.message || 'Lỗi lưu DB');
        }

        onProgress({ phase: 'done', frameIndex: total, frameTotal: total });

        return saveData;
    }

    document.querySelectorAll('.btn-scan-ai').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            var container = this.closest('.ai-moderation-container');
            var lessonId = container.dataset.lessonId;
            var progressArea = container.querySelector('.ai-progress-area');
            var statusText = container.querySelector('.ai-status-text');
            var progressBar = container.querySelector('.ai-progress-bar');
            var scanBtn = this;
            var labelSpan = scanBtn.querySelector('span');
            var defaultLabel = labelSpan.innerText;

            setAiScanBusy(true);
            labelSpan.innerText = 'Đang kiểm duyệt...';
            progressArea.classList.remove('hidden');

            try {
                await scanLesson(lessonId, function (state) {
                    if (state.phase === 'extract') {
                        statusText.innerText = 'Đang cắt frame (mỗi 30s)...';
                        progressBar.style.width = '10%';
                    } else if (state.phase === 'analyze') {
                        statusText.innerText = 'Đang phân tích ' + state.frameIndex + '/' + state.frameTotal + ' frame...';
                        progressBar.style.width = (10 + (90 * state.frameIndex / state.frameTotal)) + '%';
                    } else if (state.phase === 'save') {
                        statusText.innerText = 'Đang lưu kết quả...';
                        progressBar.style.width = '98%';
                    }
                });

                window.location.reload();
            } catch (err) {
                alert('Lỗi quét AI: ' + err.message);
                labelSpan.innerText = defaultLabel;
                progressArea.classList.add('hidden');
                setAiScanBusy(false);
            }
        });
    });

    var courseScanBtn = document.getElementById('btn-scan-course-ai');
    if (courseScanBtn) {
        courseScanBtn.addEventListener('click', async function () {
            var videoLessons = JSON.parse(courseScanBtn.dataset.videoLessons || '[]');
            var totalVideos = videoLessons.length;

            if (totalVideos === 0) {
                alert('Khóa học không có video để quét.');
                return;
            }

            if (!confirm('Quét AI toàn bộ ' + totalVideos + ' video của khóa học?\nQuá trình có thể mất nhiều thời gian và chạy tuần tự từng video.')) {
                return;
            }

            var labelSpan = courseScanBtn.querySelector('span');
            var defaultLabel = labelSpan.innerText;
            var stats = {
                total: totalVideos,
                success: 0,
                failed: 0,
                errors: [],
            };

            setAiScanBusy(true);
            labelSpan.innerText = 'Đang quét toàn bộ khóa học...';

            for (var i = 0; i < totalVideos; i++) {
                var lesson = videoLessons[i];

                try {
                    await scanLesson(lesson.id, function (state) {
                        updateCourseProgress(i, totalVideos, lesson.title, state.phase, state.frameIndex, state.frameTotal);
                    });
                    stats.success++;
                } catch (err) {
                    stats.failed++;
                    stats.errors.push({ title: lesson.title, message: err.message });
                    console.error('[AI Moderation] Lỗi quét video "' + lesson.title + '" (ID: ' + lesson.id + '):', err);
                }
            }

            updateCourseProgress(totalVideos - 1, totalVideos, '', 'done', 1, 1);
            document.getElementById('course-ai-status-text').innerText = 'Đã quét xong ' + totalVideos + '/' + totalVideos + ' video';
            document.getElementById('course-ai-frame-text').innerText =
                'Thành công: ' + stats.success + ' · Lỗi: ' + stats.failed;
            document.getElementById('course-ai-progress-bar').style.width = '100%';
            document.getElementById('course-ai-percent-text').innerText = '100%';

            var summary = 'Đã quét xong ' + totalVideos + '/' + totalVideos + ' video\n\n'
                + 'Tổng số video: ' + stats.total + '\n'
                + 'Quét thành công: ' + stats.success + '\n'
                + 'Lỗi: ' + stats.failed;

            if (stats.errors.length > 0) {
                summary += '\n\nVideo lỗi:\n' + stats.errors.map(function (item) {
                    return '- ' + item.title + ': ' + item.message;
                }).join('\n');
            }

            alert(summary);
            window.location.reload();
        });
    }
});
</script>
</div>

</x-admin-layout>
