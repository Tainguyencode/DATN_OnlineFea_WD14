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
                    @if($course->status === 'pending_update')
                        <span class="rounded-full border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">Cập nhật chờ duyệt</span>
                    @elseif($course->status === 'published')
                        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Đã xuất bản</span>
                    @elseif($course->status === 'approved')
                        @if($course->instructor?->instructor_status === 'approved' && ! $course->instructor?->isLocked())
                            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Đã duyệt & Xuất bản</span>
                        @else
                            <span class="rounded-full border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">Đã duyệt nội dung (Chờ duyệt GV)</span>
                        @endif
                    @elseif($course->status === 'rejected')
                        <span class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700">Bị từ chối</span>
                    @else
                        <span class="rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">Đang chờ duyệt</span>
                    @endif
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

                <div class="mt-5 border-t border-slate-100 pt-4">
                    <h4 class="text-xs font-bold uppercase tracking-wide text-slate-500">Cam kết bản quyền</h4>
                    @if($course->copyright_agreed)
                        <div class="mt-1.5 flex items-center gap-1.5 text-sm text-emerald-700 font-bold">
                            <span class="text-emerald-600">✔</span> <span>Đã cam kết</span>
                        </div>
                        <div class="mt-2 grid gap-4 sm:grid-cols-2 text-xs">
                            <div>
                                <span class="block font-medium text-slate-500 uppercase tracking-wider">Thời gian:</span>
                                <strong class="text-slate-900">{{ $course->copyright_agreed_at?->format('d/m/Y H:i') }}</strong>
                            </div>
                            <div>
                                <span class="block font-medium text-slate-500 uppercase tracking-wider">Người xác nhận:</span>
                                <strong class="text-slate-900">{{ $course->copyrightAgreedBy?->name ?? $course->instructor?->name }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="mt-1.5 flex items-center gap-1.5 text-sm text-rose-700 font-bold">
                            <span class="text-rose-600">✖</span> <span>Chưa cam kết</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('admin.courses.pending') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition-colors duration-200 hover:bg-slate-50 cursor-pointer">
                    Quay lại danh sách
                </a>
            </div>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- THÔNG TIN GIẢNG VIÊN TẠO KHÓA HỌC                                        --}}
    {{-- ========================================================================= --}}
    @php
        $instructor = $course->instructor;
        $certsCount = $instructor?->instructorCertificates?->count() ?? 0;
    @endphp
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 {{ $instructor?->instructor_status !== 'approved' ? 'border-amber-300 bg-amber-50/20 dark:border-amber-700/50 dark:bg-amber-950/10' : '' }}">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <img src="{{ $instructor?->avatarUrl() ?? 'https://ui-avatars.com/api/?name='.urlencode($instructor?->name ?? 'Instructor') }}"
                     alt="{{ $instructor?->name }}"
                     class="h-16 w-16 rounded-2xl object-cover border-2 border-slate-200 shadow-sm shrink-0">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-lg font-black text-slate-950 dark:text-white">{{ $instructor?->name ?? 'Chưa xác định' }}</h3>
                        <span class="text-xs text-slate-400 font-medium">({{ '@' . ($instructor?->username ?? 'user') }})</span>

                        {{-- Trạng thái hồ sơ giảng viên --}}
                        @if($instructor?->isLocked())
                            <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-bold text-slate-800 border border-slate-400">
                                 Đang bị khóa
                            </span>
                        @elseif($instructor?->instructor_status === 'approved')
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-800 border border-emerald-300">
                                 Đã duyệt
                            </span>
                        @elseif($instructor?->instructor_status === 'rejected')
                            <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800 border border-rose-300">
                                 Bị từ chối
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800 border border-amber-300">
                                 Chưa duyệt hồ sơ
                            </span>
                        @endif

                        @if($instructor?->instructor_status !== 'approved')
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 border border-amber-500/30 px-2.5 py-0.5 text-xs font-black text-amber-700 dark:text-amber-300">
                                ⚠️ Giảng viên chưa được duyệt
                            </span>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500">
                        Email: <strong class="text-slate-800 dark:text-slate-200">{{ $instructor?->email }}</strong>
                        @if($instructor?->instructorProfile?->phone ?? $instructor?->phone)
                            · SĐT: <strong class="text-slate-800 dark:text-slate-200">{{ $instructor->instructorProfile?->phone ?? $instructor->phone }}</strong>
                        @endif
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-1 text-xs text-slate-600 dark:text-slate-400">
                        <div>
                            <span>Trạng thái tài khoản:</span>
                            <strong class="text-slate-900 dark:text-white">{{ $instructor?->account_status === 'locked' ? 'Bị khóa' : 'Hoạt động' }}</strong>
                        </div>
                        <div>
                            <span>Chứng chỉ / Tài liệu:</span>
                            <strong class="text-slate-900 dark:text-white">{{ $certsCount }} tài liệu</strong>
                        </div>
                        <div>
                            <span>Khóa học chờ duyệt:</span>
                            <strong class="text-amber-600 dark:text-amber-400">{{ $instructorPendingCoursesCount ?? 1 }} khóa học</strong>
                        </div>
                    </div>
                </div>
            </div>

            @if($instructor)
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('admin.instructors.applications.show', $instructor) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                        <span>Xem hồ sơ giảng viên</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            @endif
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
                                $hasDraftUpdate = isset($lesson->draft_update);
                                $payload = $hasDraftUpdate ? ($lesson->draft_update->payload ?? []) : [];

                                $effectiveOriginalKey = $hasDraftUpdate ? ($payload['original_video_key'] ?? null) : $lesson->original_video_key;
                                $effectiveHlsKey = $hasDraftUpdate ? ($payload['hls_manifest_key'] ?? null) : $lesson->hls_manifest_key;
                                $effectiveVideoPath = $hasDraftUpdate ? ($payload['video_path'] ?? null) : $lesson->video_path;
                                $effectiveVideoUrl = $hasDraftUpdate ? ($payload['video_url'] ?? null) : $lesson->video_url;

                                $hasVideo = filled($effectiveOriginalKey) || filled($effectiveHlsKey) || filled($effectiveVideoPath) || filled($effectiveVideoUrl);
                                $lessonDuration = (int) ($hasDraftUpdate ? ($payload['duration_seconds'] ?? ($payload['duration'] ?? 0)) : ($lesson->duration_seconds ?: $lesson->duration ?: 0));

                                $videoLessonKey = $hasDraftUpdate
                                    ? ('update_les_' . $lesson->draft_update->id)
                                    : $lesson->id;

                                $effectiveModeration = $hasDraftUpdate
                                    ? (!empty($payload['ai_moderation']) ? (is_array($payload['ai_moderation']) ? new \App\Models\VideoModeration($payload['ai_moderation']) : $payload['ai_moderation']) : null)
                                    : $lesson->videoModeration;
                            @endphp
                            <div class="p-4">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-bold text-slate-700">{{ $typeLabels[$lesson->type] ?? $lesson->type }}</span>
                                            @if(isset($lesson->update_status))
                                                @if($lesson->update_status === 'draft')
                                                    <span class="rounded-full border border-amber-300 bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">Draft</span>
                                                @elseif($lesson->update_status === 'pending')
                                                    <span class="rounded-full border border-blue-300 bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-800">Pending</span>
                                                @elseif($lesson->update_status === 'rejected')
                                                    <span class="rounded-full border border-rose-300 bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-800">Rejected</span>
                                                @endif
                                            @endif
                                            @if(!empty($lesson->is_draft_create))
                                                <span class="rounded-full border border-emerald-300 bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800">Mới (New)</span>
                                            @endif
                                            @if(!empty($lesson->is_pending_deletion))
                                                <span class="rounded-full border border-rose-300 bg-rose-100 px-2.5 py-1 text-xs font-bold text-rose-800">Yêu cầu xóa</span>
                                            @endif
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

                                        @if($lesson->type === 'video' && $hasVideo && !$effectiveVideoUrl)
                                            <div class="mt-4 aspect-video max-w-xl overflow-hidden rounded-lg border border-slate-200 bg-slate-950">
                                                <video
                                                    id="admin-video-{{ $videoLessonKey }}"
                                                    data-hls-src="{{ route('admin.ai-moderation.hls.playlist', ['lesson' => $videoLessonKey]) }}"
                                                    controls
                                                    preload="metadata"
                                                    class="h-full w-full"
                                                    data-admin-review-video
                                                ></video>
                                            </div>
                                        @endif
                                            
                                            {{-- Nút và khu vực hiển thị quét AI --}}
                                            <div class="mt-4 max-w-xl ai-moderation-container" data-lesson-id="{{ $videoLessonKey }}">
                                                <button type="button" class="btn-scan-ai inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-indigo-700 disabled:opacity-50">
                                                    <span>Quét nội dung</span>
                                                </button>
                                                
                                                <div class="ai-progress-area hidden mt-3 rounded-lg border border-indigo-100 bg-indigo-50 p-4">
                                                    <p class="ai-status-text text-sm font-semibold text-indigo-700">Đang khởi tạo...</p>
                                                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-indigo-200">
                                                        <div class="ai-progress-bar h-full bg-indigo-600 transition-all duration-300" style="width: 0%"></div>
                                                    </div>
                                                </div>

                                                <div class="ai-result-area mt-4 {{ $effectiveModeration ? '' : 'hidden' }}">
                                                    @if($effectiveModeration)
                                                        @php $mod = $effectiveModeration; @endphp
                                                        @php
                                                            $hasHard = $mod->violence || $mod->adult || $mod->weapon;
                                                            $hasSigns = $mod->hasDetectedSigns();
                                                            $riskBadgeMap = [
                                                                'none'   => ['bg-emerald-100 text-emerald-700', 'Không phát hiện dấu hiệu'],
                                                                'low'    => ['bg-amber-100 text-amber-700',   'Có dấu hiệu cần kiểm tra'],
                                                                'medium' => ['bg-orange-100 text-orange-700', 'Nên xem lại video'],
                                                                'high'   => ['bg-rose-100 text-rose-700',     'AI nghi ngờ cao – cần xác minh'],
                                                            ];
                                                            $riskKey = in_array($mod->copyright_risk, ['none','low','medium','high']) ? $mod->copyright_risk : 'none';
                                                            [$riskCss, $riskLabel] = $riskBadgeMap[$riskKey];
                                                        @endphp
                                                        <div class="rounded-lg border {{ $hasHard ? 'border-rose-200 bg-rose-50' : ($hasSigns ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50') }} p-4 shadow-sm">
                                                            {{-- Banner trạng thái --}}
                                                            <div class="flex items-center gap-2 mb-3">
                                                                @if($hasHard)
                                                                    <span class="text-base">🔴</span>
                                                                    <h6 class="font-bold text-rose-900 text-sm">AI phát hiện nội dung cần xem xét</h6>
                                                                @elseif($hasSigns)
                                                                    <span class="text-base">🔍</span>
                                                                    <h6 class="font-bold text-amber-900 text-sm">AI phát hiện dấu hiệu – cần admin kiểm tra</h6>
                                                                @else
                                                                    <span class="text-base">✅</span>
                                                                    <h6 class="font-bold text-emerald-900 text-sm">Không phát hiện dấu hiệu đáng chú ý</h6>
                                                                @endif
                                                            </div>

                                                            {{-- Badges chi tiết --}}
                                                            <div class="flex flex-wrap gap-2 mb-3">
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->violence ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">Bạo lực: {{ $mod->violence ? 'Phát hiện' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->adult ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">18+: {{ $mod->adult ? 'Phát hiện' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->weapon ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">Vũ khí: {{ $mod->weapon ? 'Phát hiện' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->watermark ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">Watermark: {{ $mod->watermark ? 'Có dấu hiệu' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->tiktok_logo ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">TikTok: {{ $mod->tiktok_logo ? 'Có dấu hiệu' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $mod->youtube_logo ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">YouTube: {{ $mod->youtube_logo ? 'Có dấu hiệu' : 'Không' }}</span>
                                                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $riskCss }}">Mức nghi ngờ: {{ $riskLabel }}</span>
                                                            </div>

                                                            @if($mod->summary)
                                                                <p class="text-sm text-slate-700 mb-3"><span class="font-semibold">AI nhận xét:</span> {{ $mod->summary }}</p>
                                                            @endif
                                                            
                                                            @php $violatedFrames = $mod->violatedFrameDetails(); @endphp
                                                            @if(!empty($violatedFrames))
                                                                <div class="text-xs text-slate-600 bg-white rounded-lg border border-slate-200 mt-3 overflow-hidden">
                                                                    <div class="bg-slate-50 px-3 py-2 border-b border-slate-200 flex items-center justify-between">
                                                                        <p class="font-semibold text-slate-800 text-xs">🎯 Dấu hiệu phát hiện theo thời điểm</p>
                                                                        <span class="text-xs text-slate-400">Bấm ▶ để nhảy đến đoạn AI phát hiện</span>
                                                                    </div>
                                                                    <ul class="divide-y divide-slate-100 max-h-48 overflow-y-auto">
                                                                    @foreach($violatedFrames as $vf)
                                                                        @php
                                                                            // Tính seconds từ timestamp string "MM:SS"
                                                                            $tsParts = explode(':', $vf['timestamp']);
                                                                            $tsSeconds = count($tsParts) === 2
                                                                                ? ((int)$tsParts[0] * 60 + (int)$tsParts[1])
                                                                                : (int)($tsParts[0] ?? 0);
                                                                        @endphp
                                                                        <li class="flex items-start gap-2 px-3 py-2 hover:bg-amber-50 transition-colors">
                                                                            {{-- Nút seek --}}
                                                                            @if($lesson->video_path)
                                                                                <button
                                                                                    type="button"
                                                                                    class="admin-seek-btn flex-shrink-0 inline-flex items-center gap-1 rounded-md bg-indigo-600 px-2 py-0.5 text-xs font-bold text-white hover:bg-indigo-700 transition-colors"
                                                                                    data-video-id="admin-video-{{ $videoLessonKey }}"
                                                                                    data-timestamp="{{ $tsSeconds }}"
                                                                                    title="Nhảy đến {{ $vf['timestamp'] }} và phát video"
                                                                                >
                                                                                    ▶ {{ $vf['timestamp'] }}
                                                                                </button>
                                                                            @else
                                                                                <span class="flex-shrink-0 inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">
                                                                                    {{ $vf['timestamp'] }}
                                                                                </span>
                                                                            @endif
                                                                            {{-- Labels & reason --}}
                                                                            <div class="min-w-0 flex-1">
                                                                                <span class="text-amber-700 font-semibold">{{ implode(', ', $vf['labels']) }}</span>
                                                                                @if(!empty($vf['reason']))
                                                                                    <span class="text-slate-500"> – {{ $vf['reason'] }}</span>
                                                                                @endif
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif

                                                            {{-- Note admin --}}
                                                            <p class="mt-3 text-xs text-slate-500 italic">ℹ️ AI chỉ hỗ trợ phát hiện dấu hiệu. Quyết định Approve / Cần chỉnh sửa / Từ chối luôn do Admin.</p>
                                                        </div>
                                                    @endif

                                                    {{-- Ghi chú của Admin theo từng Lesson --}}
                                                    @php
                                                        $lessonUpdate = $lesson->draft_update ?? null;
                                                        if (!$lessonUpdate && str_starts_with((string)$lesson->id, 'update_les_')) {
                                                            $uId = str_replace('update_les_', '', $lesson->id);
                                                            $lessonUpdate = \App\Models\ContentUpdate::find($uId);
                                                        }
                                                        if (!$lessonUpdate) {
                                                            $lessonUpdate = \App\Models\ContentUpdate::where('course_id', $course->id)
                                                                ->where('entity_id', $lesson->id)
                                                                ->latest()
                                                                ->first();
                                                        }
                                                        $lPayload = $lessonUpdate?->payload ?? [];
                                                        $existingAdminNote = $lPayload['admin_note'] ?? null;
                                                        $existingRequireReupload = !empty($lPayload['require_reupload']);
                                                        $existingLessonStatus = $lPayload['review_status'] ?? ($lessonUpdate?->status === 'rejected' ? 'fail' : 'pass');
                                                    @endphp

                                                    <div class="mt-4 max-w-xl rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-2xs">
                                                        <div class="flex items-center justify-between">
                                                            <p class="text-xs font-bold uppercase tracking-wide text-indigo-700 flex items-center gap-1.5">
                                                                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                                </svg>
                                                                <span>Ghi chú của Admin (Cho bài học này)</span>
                                                            </p>
                                                            <span class="text-xs text-slate-500">Mỗi bài học có ghi chú riêng</span>
                                                        </div>

                                                        <div class="mt-3 space-y-3">
                                                            <textarea
                                                                name="lesson_notes[{{ $videoLessonKey }}][admin_note]"
                                                                rows="3"
                                                                placeholder="Ví dụ: Video có watermark TikTok. Vui lòng upload bản gốc. Âm thanh hơi nhỏ..."
                                                                class="w-full resize-none rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                                                            >{{ old("lesson_notes.{$videoLessonKey}.admin_note", $existingAdminNote) }}</textarea>

                                                            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                                                                <div class="flex flex-wrap items-center gap-4">
                                                                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                                                                        <input
                                                                            type="checkbox"
                                                                            name="lesson_notes[{{ $videoLessonKey }}][require_reupload]"
                                                                            value="1"
                                                                            @checked(old("lesson_notes.{$videoLessonKey}.require_reupload", $existingRequireReupload))
                                                                            class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
                                                                        >
                                                                        <span class="text-rose-700">☐ Yêu cầu upload lại video</span>
                                                                    </label>

                                                                    <div class="flex items-center gap-3 border-l border-slate-200 pl-4">
                                                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-slate-700">
                                                                            <input type="radio" name="lesson_notes[{{ $videoLessonKey }}][status]" value="need_revision" @checked(old("lesson_notes.{$videoLessonKey}.status", $existingLessonStatus) === 'need_revision') class="h-3.5 w-3.5 text-amber-600 focus:ring-amber-500">
                                                                            <span class="text-amber-700">Cần chỉnh sửa</span>
                                                                        </label>
                                                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-slate-700">
                                                                            <input type="radio" name="lesson_notes[{{ $videoLessonKey }}][status]" value="fail" @checked(old("lesson_notes.{$videoLessonKey}.status", $existingLessonStatus) === 'fail') class="h-3.5 w-3.5 text-rose-600 focus:ring-rose-500">
                                                                            <span class="text-rose-700">Từ chối</span>
                                                                        </label>
                                                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-xs font-bold text-slate-700">
                                                                            <input type="radio" name="lesson_notes[{{ $videoLessonKey }}][status]" value="pass" @checked(old("lesson_notes.{$videoLessonKey}.status", $existingLessonStatus) === 'pass') class="h-3.5 w-3.5 text-emerald-600 focus:ring-emerald-500">
                                                                            <span class="text-emerald-700">Đạt</span>
                                                                        </label>
                                                                    </div>
                                                                </div>

                                                                @php
                                                                    $hasSavedNote = filled($existingAdminNote) || $existingRequireReupload;
                                                                @endphp
                                                                <div class="flex items-center gap-2">
                                                                    <span class="note-save-status text-xs font-bold text-emerald-600 {{ $hasSavedNote ? '' : 'hidden' }}">
                                                                        ✓ Đã lưu trên hệ thống
                                                                    </span>
                                                                    <button
                                                                        type="button"
                                                                        data-lesson-id="{{ $videoLessonKey }}"
                                                                        class="btn-save-lesson-note inline-flex items-center gap-1.5 rounded-lg border {{ $hasSavedNote ? 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'border-indigo-200 bg-white text-indigo-700 hover:bg-indigo-50' }} px-3 py-1.5 text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer"
                                                                    >
                                                                        <svg class="h-3.5 w-3.5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                                                        </svg>
                                                                        <span>{{ $hasSavedNote ? 'Cập nhật ghi chú' : 'Lưu ghi chú' }}</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        @if($lesson->video_url)
                                            <a href="{{ $lesson->video_url }}" target="_blank" class="rounded-lg border border-indigo-200 px-3 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-50">Xem video URL</a>
                                        @endif
                                        @if($lesson->video_path)
                                            <span class="rounded-lg border border-emerald-200 px-3 py-2 text-xs font-bold text-emerald-700">Video file ({{ \Illuminate\Support\Str::endsWith($lesson->video_path, '.mp4') ? 'Chưa bảo mật HLS' : 'Đã bảo mật' }})</span>
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
    <section id="review-decision-section" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Checklist đánh giá</p>
                <h3 class="mt-1 text-lg font-bold text-slate-950">Kiểm tra từng mục trước khi quyết định</h3>
            </div>
            <p class="text-xs text-slate-500">Các mục có gợi ý tự động được đánh dấu sẵn theo dữ liệu khóa học.</p>
        </div>

        <form method="POST"
              action="{{ route('admin.courses.submitReview', $course) }}#review-decision-section"
              id="course-review-form"
              class="mt-5 space-y-6">
            @csrf

            {{-- Hidden input nhận giá trị action từ JS khi click button --}}
            <input type="hidden" name="action" id="review-action-input" value="">

            @if ($errors->any())
                <div class="rounded-lg bg-rose-50 p-4 text-sm text-rose-800 border border-rose-200">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Không thể thực hiện yêu cầu! Vui lòng kiểm tra lại:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-xs font-semibold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                                        <p class="mt-1.5 text-xs font-bold text-rose-600 flex items-center gap-1">
                                            <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span>{{ $message }}</span>
                                        </p>
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
                          class="mt-2 w-full resize-none rounded-lg border px-3 py-2 text-sm outline-none transition-all duration-200 focus-visible:ring-2 @error('comment') border-rose-500 bg-rose-50/20 ring-2 ring-rose-500/20 focus:border-rose-500 focus-visible:ring-rose-500/30 @else border-slate-300 focus:border-indigo-500 focus-visible:ring-indigo-500/20 @enderror">{{ old('comment') }}</textarea>
                
                {{-- JS Client-side validation message --}}
                <p id="js-comment-error" class="mt-2 text-xs font-bold text-rose-600 flex items-center gap-1.5 hidden"></p>

                @error('comment')
                    <p class="mt-2 text-xs font-bold text-rose-600 flex items-center gap-1.5">
                        <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            @error('action')
                <p class="mt-2 text-xs font-bold text-rose-600 flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $message }}</span>
                </p>
            @enderror

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

    <div
        id="ai-review-result-modal"
        class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ai-review-result-title"
        aria-describedby="ai-review-result-message"
        aria-hidden="true"
    >
        <div class="w-full max-w-xl overscroll-contain rounded-2xl border border-violet-200 bg-white p-6 shadow-2xl dark:border-violet-900/60 dark:bg-slate-900">
            <h2 id="ai-review-result-title" class="text-xl font-bold text-slate-950 dark:text-white"></h2>
            <p id="ai-review-result-message" class="mt-3 max-h-[60vh] overflow-y-auto whitespace-pre-line break-words text-sm leading-6 text-slate-600 dark:text-slate-300"></p>
            <button
                type="button"
                id="ai-review-result-close"
                class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-bold text-white transition-colors duration-200 hover:bg-violet-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 sm:w-auto"
            >
                Đóng
            </button>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var commentInput = document.getElementById('review-comment');
    var jsCommentError = document.getElementById('js-comment-error');
    var aiResultModal = document.getElementById('ai-review-result-modal');
    var aiResultTitle = document.getElementById('ai-review-result-title');
    var aiResultMessage = document.getElementById('ai-review-result-message');
    var aiResultClose = document.getElementById('ai-review-result-close');

    function showAdminToast(message, type) {
        var safeMessage = typeof message === 'string' && message.trim() !== ''
            ? message
            : 'Không thể thực hiện thao tác. Vui lòng thử lại.';

        if (window.AppToast?.show) {
            window.AppToast.show({ type: type || 'info', message: safeMessage });
            return;
        }

        console.error(safeMessage);
    }

    function createAiUserFacingError(message) {
        var error = new Error(message);
        error.isUserFacing = true;
        return error;
    }

    function getAiUserFacingMessage(error, fallback) {
        return error?.isUserFacing && typeof error.message === 'string' && error.message.trim() !== ''
            ? error.message
            : fallback;
    }

    function showAiReviewResult(title, message) {
        return new Promise(function (resolve) {
            var previousFocus = document.activeElement;
            var previousOverflow = document.body.style.overflow;

            aiResultTitle.textContent = title;
            aiResultMessage.textContent = typeof message === 'string' && message.trim() !== ''
                ? message
                : 'Không có nội dung đánh giá.';
            aiResultModal.classList.remove('hidden');
            aiResultModal.classList.add('flex');
            aiResultModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            function closeModal() {
                aiResultModal.classList.add('hidden');
                aiResultModal.classList.remove('flex');
                aiResultModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = previousOverflow;
                aiResultClose.removeEventListener('click', closeModal);
                document.removeEventListener('keydown', handleModalKeydown);
                if (previousFocus instanceof HTMLElement) previousFocus.focus();
                resolve();
            }

            function handleModalKeydown(event) {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeModal();
                    return;
                }

                if (event.key === 'Tab') {
                    event.preventDefault();
                    aiResultClose.focus();
                }
            }

            aiResultClose.addEventListener('click', closeModal);
            document.addEventListener('keydown', handleModalKeydown);
            aiResultClose.focus();
        });
    }

    @if($errors->any())
        if (window.location.hash !== '#review-decision-section') {
            window.location.hash = 'review-decision-section';
        }
        setTimeout(function () {
            var targetSection = document.getElementById('review-decision-section');
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'auto', block: 'start' });
            }
            if (commentInput) {
                commentInput.focus();
            }
        }, 50);
    @endif

    if (commentInput) {
        commentInput.addEventListener('input', function () {
            if (commentInput.value.trim().length >= 10) {
                commentInput.classList.remove('border-rose-500', 'bg-rose-50/20', 'ring-2', 'ring-rose-500/20');
                commentInput.classList.add('border-slate-300');
                if (jsCommentError) {
                    jsCommentError.classList.add('hidden');
                }
            }
        });
    }
    // Khởi tạo các video HLS (nếu có)
    function initAdminHlsVideos() {
        if (typeof Hls === 'undefined') {
            setTimeout(initAdminHlsVideos, 150);
            return;
        }

        document.querySelectorAll('video[data-hls-src]').forEach(function (video) {
            if (video.dataset.hlsReady) return;
            video.dataset.hlsReady = 'true';

            var hlsSrc = video.getAttribute('data-hls-src');

            if (Hls.isSupported()) {
                var hls = new Hls({
                    enableWorker: true,
                    lowLatencyMode: false,
                });
                hls.loadSource(hlsSrc);
                hls.attachMedia(video);
                hls.on(Hls.Events.ERROR, function (event, data) {
                    if (data.fatal) {
                        switch (data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                console.warn('[Admin HLS] Fatal network error, recovering...', data);
                                hls.startLoad();
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                console.warn('[Admin HLS] Fatal media error, recovering...', data);
                                hls.recoverMediaError();
                                break;
                            default:
                                console.error('[Admin HLS] Unrecoverable error:', data);
                                hls.destroy();
                                break;
                        }
                    }
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = hlsSrc;
            }
        });
    }

    initAdminHlsVideos();

    // 1. Logic duyệt khóa học
    var form     = document.getElementById('course-review-form');
    var actionInput = document.getElementById('review-action-input');
    var buttons  = document.querySelectorAll('.review-action-btn');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            var action  = btn.getAttribute('data-action');
            var message = btn.getAttribute('data-confirm');

            if (action === 'rejected' || action === 'need_revision') {
                var val = commentInput ? commentInput.value.trim() : '';
                if (val.length < 10) {
                    e.preventDefault();

                    if (commentInput) {
                        commentInput.classList.remove('border-slate-300', 'focus:border-indigo-500');
                        commentInput.classList.add('border-rose-500', 'bg-rose-50/20', 'ring-2', 'ring-rose-500/20');
                        commentInput.focus();
                    }

                    if (jsCommentError) {
                        jsCommentError.innerHTML = `
                            <svg class="h-4 w-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Lý do / ghi chú bắt buộc khi yêu cầu chỉnh sửa hoặc từ chối (tối thiểu 10 ký tự).</span>
                        `;
                        jsCommentError.classList.remove('hidden');
                    }

                    return false;
                }
            }

            if (!confirm(message)) {
                return;
            }

            actionInput.value = action;
            form.submit();
        });
    });

    // ─────────────────────────────────────────────────────────────
    // 3. Logic xem nhanh đoạn AI phát hiện (CHỈ Admin Review)
    // ─────────────────────────────────────────────────────────────

    function adminSeekAndPlay(video, seconds) {
        seconds = Math.max(0, parseFloat(seconds) || 0);

        var seekableInfo = 'none';
        if (video.seekable && video.seekable.length > 0) {
            seekableInfo = video.seekable.start(0) + 's – ' + video.seekable.end(0) + 's';
        }
        console.log('[AdminSeek] target=' + seconds + 's | readyState=' + video.readyState
            + ' | duration=' + video.duration + ' | seekable=' + seekableInfo);

        function doSeek() {
            video.pause();
            video.currentTime = seconds;
            console.log('[AdminSeek] currentTime sau set:', video.currentTime);

            var done = false;

            var fallback = setTimeout(function () {
                if (done) return;
                done = true;
                console.warn('[AdminSeek] fallback play @ ' + video.currentTime);
                video.play().catch(function () {});
            }, 600);

            video.addEventListener('seeked', function onSeeked() {
                if (done) return;
                done = true;
                clearTimeout(fallback);
                video.removeEventListener('seeked', onSeeked);
                console.log('[AdminSeek] seeked OK @ ' + video.currentTime);
                video.play().catch(function () {});
            }, { once: true });
        }

        if (video.readyState >= 1) {
            doSeek();
        } else {
            // Metadata chưa load (preload=metadata đang tải) → chờ
            video.addEventListener('loadedmetadata', function () {
                doSeek();
            }, { once: true });
        }
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.admin-seek-btn');
        if (!btn) return;

        var videoId = btn.dataset.videoId;
        var seconds = parseFloat(btn.dataset.timestamp || 0);

        if (!videoId) return;

        var video = document.getElementById(videoId);
        if (!video) {
            console.error('[AdminSeek] Không tìm thấy video#' + videoId);
            return;
        }

        // Seek và play TRƯỚC, cuộn SAU (tránh scrollIntoView interrupt seek)
        adminSeekAndPlay(video, seconds);

        // Cuộn đến video để người dùng thấy
        video.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Highlight nút đang active
        document.querySelectorAll('.admin-seek-btn').forEach(function (b) {
            b.classList.remove('ring-2', 'ring-yellow-400', 'bg-indigo-800');
        });
        btn.classList.add('ring-2', 'ring-yellow-400', 'bg-indigo-800');
    });

    // ─────────────────────────────────────────────────────────────
    // 2. Logic Quét AI
    // ─────────────────────────────────────────────────────────────
    async function parseJsonResponse(response) {
        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            if (response.status === 419) {
                throw createAiUserFacingError('Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.');
            }
            throw createAiUserFacingError('Máy chủ trả về phản hồi không hợp lệ. Vui lòng thử lại.');
        }

        try {
            return await response.json();
        } catch (error) {
            throw createAiUserFacingError('Máy chủ trả về phản hồi không hợp lệ. Vui lòng thử lại.');
        }
    }

    function aiFetchHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
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
            frameText.innerText = 'Đang cắt frame (mỗi 300s)...';
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
            throw createAiUserFacingError(extData.error || 'Không thể trích xuất khung hình. Vui lòng thử lại.');
        }

        var frames = extData.frames;
        var total = extData.total;

        if (total === 0) {
            throw createAiUserFacingError('Không trích xuất được khung hình nào từ video này.');
        }

        var aiResults = [];
        var lastApiError = '';

        for (var i = 0; i < total; i++) {
            var framePath = frames[i];
            var match = framePath.match(/frame_(\d+)\.jpg$/);
            var timestamp = match ? parseInt(match[1], 10) : i * 300;

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
            throw createAiUserFacingError(
                lastApiError || 'Không thể phân tích nội dung video lúc này. Vui lòng thử lại.'
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
            throw createAiUserFacingError(saveData.error || saveData.message || 'Không thể lưu kết quả kiểm duyệt. Vui lòng thử lại.');
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
                var saveData = await scanLesson(lessonId, function (state) {
                    if (state.phase === 'extract') {
                        statusText.innerText = 'Đang cắt frame (mỗi 300s)...';
                        progressBar.style.width = '10%';
                    } else if (state.phase === 'analyze') {
                        statusText.innerText = 'Đang phân tích ' + state.frameIndex + '/' + state.frameTotal + ' frame...';
                        progressBar.style.width = (10 + (90 * state.frameIndex / state.frameTotal)) + '%';
                    } else if (state.phase === 'save') {
                        statusText.innerText = 'Đang lưu kết quả...';
                        progressBar.style.width = '98%';
                    }
                });

                statusText.innerText = 'Quét AI hoàn tất!';
                progressBar.style.width = '100%';

                var modSummary = (saveData && saveData.moderation && saveData.moderation.summary)
                    ? saveData.moderation.summary
                    : 'Không phát hiện dấu hiệu đáng chú ý.';

                showAdminToast('Đã hoàn tất kiểm duyệt AI cho bài học.', 'success');
                await showAiReviewResult('Kết quả kiểm duyệt AI', modSummary);
                window.location.reload();
            } catch (err) {
                showAdminToast(
                    getAiUserFacingMessage(err, 'Không thể phân tích bài học lúc này. Vui lòng thử lại.'),
                    'error'
                );
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
                showAdminToast('Khóa học không có video để kiểm duyệt.', 'warning');
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
                    stats.errors.push({
                        title: lesson.title,
                        message: getAiUserFacingMessage(err, 'Không thể phân tích video này.'),
                    });
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

            showAdminToast(
                stats.failed > 0 ? 'Đã hoàn tất kiểm duyệt AI, một số video gặp lỗi.' : 'Đã hoàn tất kiểm duyệt AI cho khóa học.',
                stats.failed > 0 ? 'warning' : 'success'
            );
            await showAiReviewResult('Tổng hợp kiểm duyệt AI', summary);
            window.location.reload();
        });
    }

    document.querySelectorAll('.btn-save-lesson-note').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            var lessonId = this.dataset.lessonId;
            var container = this.closest('.rounded-xl');
            var textarea = container ? container.querySelector('textarea') : null;
            var chkReupload = container ? container.querySelector('input[type="checkbox"]') : null;
            var radioStatus = container ? container.querySelector('input[type="radio"]:checked') : null;
            var statusSpan = container ? container.querySelector('.note-save-status') : null;

            var labelSpan = this.querySelector('span');
            var originalText = labelSpan ? labelSpan.innerText : 'Lưu ghi chú';
            if (labelSpan) labelSpan.innerText = 'Đang lưu...';
            this.disabled = true;

            try {
                var res = await fetch('/admin/courses/{{ $course->id }}/lessons/' + lessonId + '/note', {
                    method: 'POST',
                    headers: aiFetchHeaders(),
                    body: JSON.stringify({
                        admin_note: textarea ? textarea.value : '',
                        require_reupload: chkReupload ? chkReupload.checked : false,
                        status: radioStatus ? radioStatus.value : 'pass',
                    }),
                });

                var data = await parseJsonResponse(res);
                if (!res.ok) {
                    throw createAiUserFacingError(data.message || 'Không thể lưu ghi chú. Vui lòng thử lại.');
                }

                if (labelSpan) labelSpan.innerText = 'Cập nhật ghi chú';
                this.classList.remove('border-indigo-200', 'bg-white', 'text-indigo-700', 'hover:bg-indigo-50');
                this.classList.add('border-emerald-300', 'bg-emerald-50', 'text-emerald-700', 'hover:bg-emerald-100');

                if (statusSpan) {
                    statusSpan.innerText = '✓ Đã lưu trên hệ thống';
                    statusSpan.classList.remove('hidden');
                }

                this.disabled = false;
            } catch (err) {
                showAdminToast(
                    getAiUserFacingMessage(err, 'Không thể lưu ghi chú bài học. Vui lòng thử lại.'),
                    'error'
                );
                if (labelSpan) labelSpan.innerText = originalText;
                this.disabled = false;
            }
        });
    });

    // ─── Khởi tạo HLS Player cho các video kiểm duyệt của Admin (Tối ưu tải siêu tốc) ───
    function initAdminReviewPlayers() {
        var videoElements = document.querySelectorAll('video[data-admin-review-video]');
        if (videoElements.length === 0) return;

        function attachHlsToVideo(v) {
            if (v.dataset.hlsInit) return;
            var hlsUrl = v.getAttribute('data-hls-src');
            if (!hlsUrl) return;

            if (typeof Hls !== 'undefined' && Hls.isSupported()) {
                v.dataset.hlsInit = 'true';
                var hls = new Hls({
                    enableWorker: true,
                    lowLatencyMode: false,
                    startFragPrefetch: true,
                    maxBufferLength: 10,
                    maxMaxBufferLength: 30,
                    maxBufferSize: 30 * 1000 * 1000,
                    maxBufferHole: 0.5,
                    highBufferWatchdogPeriod: 2,
                    startLevel: -1,
                });
                hls.loadSource(hlsUrl);
                hls.attachMedia(v);

                hls.on(Hls.Events.ERROR, function (_, data) {
                    if (data.fatal) {
                        if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                            hls.startLoad();
                        } else {
                            hls.destroy();
                        }
                    }
                });
            } else if (v.canPlayType('application/vnd.apple.mpegurl')) {
                v.dataset.hlsInit = 'true';
                v.src = hlsUrl;
            }
        }

        function initAll() {
            videoElements.forEach(function (v) {
                attachHlsToVideo(v);
            });
        }

        if (typeof Hls !== 'undefined') {
            initAll();
        } else {
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/hls.js@1.5.17/dist/hls.min.js';
            s.onload = function () { initAll(); };
            document.head.appendChild(s);
        }
    }

    initAdminReviewPlayers();
});
</script>
</div>

</x-admin-layout>
