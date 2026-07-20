@php
    $sections = $course->courseSections->isNotEmpty()
        ? $course->courseSections
        : $course->chapters;

    $lessonItems = collect();
    $courseLessonNumber = 0;

    foreach ($sections as $section) {
        foreach ($section->lessons->sortBy('sort_order') as $lesson) {
            $courseLessonNumber++;

            if ($lesson->type !== 'video') {
                continue;
            }

            $lessonItems->push([
                'lesson' => $lesson,
                'number' => $courseLessonNumber,
                'sectionTitle' => $section->title,
            ]);
        }
    }
@endphp

<section id="ai-moderation-results" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex items-start gap-3">
        <span class="text-2xl leading-none" aria-hidden="true"></span>
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">Kiểm duyệt AI</p>
            <h2 class="mt-1 text-lg font-bold text-slate-950">Kết quả phân tích AI</h2>
            <p class="mt-1 text-sm text-slate-500">
                AI chỉ <strong>phát hiện dấu hiệu</strong> trong từng bài học video. AI không tự kết luận vi phạm.
                Quyết định Approve / Cần chỉnh sửa / Từ chối luôn do Admin.
            </p>
        </div>
    </div>

    {{-- Disclaimer detection-only --}}
    <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
        <div class="flex items-start gap-2.5">
            <span class="text-base" aria-hidden="true">ℹ️</span>
            <div>
                <p class="font-bold">AI phát hiện dấu hiệu – Không kết luận vi phạm</p>
                <div class="mt-1.5 space-y-1.5 text-xs text-blue-700 leading-relaxed font-medium">
                    <p>• AI chỉ ghi nhận: Logo YouTube/TikTok, watermark, nội dung nhạy cảm... Đây là <em>dấu hiệu cần xem lại</em>, không phải kết luận vi phạm.</p>
                    <p>• Logo hoặc giao diện nền tảng khác xuất hiện trong video minh họa là bình thường và không tự động bị từ chối.</p>
                    <p>• Giảng viên vẫn chịu trách nhiệm về tính hợp pháp của nội dung đã tải lên.</p>
                </div>
            </div>
        </div>
    </div>

    @if ($lessonItems->isEmpty())
        <p class="mt-5 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
            Khóa học chưa có bài học video để kiểm duyệt.
        </p>
    @else
        <div class="mt-5 space-y-4">
            @foreach ($lessonItems as $item)
                <x-video-moderation-card
                    :lesson="$item['lesson']"
                    :lesson-number="$item['number']"
                    :section-title="$item['sectionTitle']"
                />
            @endforeach
        </div>
    @endif
</section>
