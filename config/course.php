<?php

return [
    'minimum_sections' => (int) env('COURSE_MIN_SECTIONS', 1),
    'minimum_lessons' => (int) env('COURSE_MIN_LESSONS', 3),
    'minimum_video_lessons' => (int) env('COURSE_MIN_VIDEO_LESSONS', 1),

    'minimum_video_completion_percent' => (int) env('COURSE_MIN_VIDEO_PERCENT', 80),
    'minimum_course_completion_percent' => (int) env('COURSE_MIN_COMPLETION_PERCENT', 80),
    'minimum_quiz_score' => (int) env('COURSE_MIN_QUIZ_SCORE', 70),

    'default_required_video_percent' => 80,
    'default_required_lesson_percent' => 80,
    'default_minimum_quiz_score' => 70,
    'default_require_all_quizzes' => true,
    'default_require_all_assignments' => true,
    'default_certificate_enabled' => true,

    'progress_update_interval_seconds' => 15,

    'reject_reason_min_length' => 10,

    'admin_review_checklist' => [
        'course_info_complete' => 'Thông tin khóa học đầy đủ',
        'content_matches_title' => 'Nội dung phù hợp với tiêu đề',
        'video_quality_ok' => 'Video có hình ảnh và âm thanh đạt yêu cầu',
        'no_violent_content' => 'Không có nội dung bạo lực hoặc phản cảm',
        'no_copyright_issues' => 'Không phát hiện dấu hiệu vi phạm bản quyền',
        'no_empty_lessons' => 'Không có bài học trống',
        'quiz_assignment_valid' => 'Quiz và bài tập hợp lệ',
        'no_dangerous_links' => 'Không chứa liên kết hoặc nội dung nguy hiểm',
    ],
];
