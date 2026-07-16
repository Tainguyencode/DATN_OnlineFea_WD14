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
            <h2 class="mt-1 text-lg font-bold text-slate-950">Kết quả kiểm duyệt AI</h2>
            <p class="mt-1 text-sm text-slate-500">
                Kết quả quét nội dung video theo từng bài học. Vui lòng chỉnh sửa các bài có vi phạm trước khi gửi duyệt lại.
            </p>
        </div>
    </div>

    <!-- Copyright Disclaimer Alert -->
    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
        <div class="flex items-start gap-2.5">
            <span class="text-base" aria-hidden="true">⚠️</span>
            <div>
                <p class="font-bold">Lưu ý</p>
                <div class="mt-1.5 space-y-1.5 text-xs text-amber-700 leading-relaxed font-medium">
                    <p>• AI chỉ hỗ trợ phát hiện các dấu hiệu vi phạm như logo, watermark, nội dung nhạy cảm, bạo lực... AI không thể đảm bảo phát hiện mọi hành vi vi phạm bản quyền.</p>
                    <p>• Kết quả "Không phát hiện vi phạm" không đồng nghĩa với việc nội dung hoàn toàn hợp pháp.</p>
                    <p>• Giảng viên vẫn chịu hoàn toàn trách nhiệm về tính hợp pháp của video, tài liệu và các nội dung đã tải lên.</p>
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
