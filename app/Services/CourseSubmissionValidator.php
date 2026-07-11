<?php

namespace App\Services;

use App\Data\CourseSubmissionCheckResult;
use App\Models\Course;

class CourseSubmissionValidator
{
    public const KEY_THUMBNAIL = 'thumbnail';

    public const KEY_TITLE = 'title';

    public const KEY_DESCRIPTION = 'description';

    public const KEY_OBJECTIVES = 'objectives';

    public const KEY_CATEGORY = 'category';

    public const KEY_LESSON_COUNT = 'lesson_count';

    public const KEY_VIDEO_DURATION = 'video_duration';

    public function validate(Course $course): CourseSubmissionCheckResult
    {
        $course->loadMissing('category.parent');

        $lessonCount = $course->lessonCount();
        $durationMinutes = $course->totalVideoDurationMinutes();
        $categoryReady = $course->category?->isSelectableForCourse() ?? false;

        $items = [
            $this->makeItem(
                self::KEY_THUMBNAIL,
                'Ảnh thumbnail',
                filled($course->thumbnail),
                'Thiếu thumbnail',
            ),
            $this->makeItem(
                self::KEY_TITLE,
                'Tên khóa học',
                filled(trim((string) $course->title)),
                'Thiếu tên khóa học',
            ),
            $this->makeItem(
                self::KEY_DESCRIPTION,
                'Mô tả chi tiết',
                filled(trim(strip_tags((string) $course->description))),
                'Thiếu mô tả chi tiết',
            ),
            $this->makeItem(
                self::KEY_OBJECTIVES,
                'Mục tiêu khóa học',
                filled(trim(strip_tags((string) $course->objectives))),
                'Thiếu mục tiêu khóa học',
            ),
            $this->makeItem(
                self::KEY_CATEGORY,
                'Danh mục',
                $categoryReady,
                'Chưa chọn danh mục con đang hoạt động',
            ),
            $this->makeItem(
                self::KEY_LESSON_COUNT,
                'Số bài học',
                $lessonCount >= Course::MIN_LESSON_COUNT,
                $lessonCount >= Course::MIN_LESSON_COUNT
                    ? null
                    : sprintf(
                        'Chưa đủ %d bài học (hiện có %d bài)',
                        Course::MIN_LESSON_COUNT,
                        $lessonCount,
                    ),
            ),
            $this->makeItem(
                self::KEY_VIDEO_DURATION,
                'Tổng thời lượng video',
                $durationMinutes >= Course::MIN_VIDEO_DURATION_MINUTES,
                $durationMinutes >= Course::MIN_VIDEO_DURATION_MINUTES
                    ? null
                    : sprintf(
                        'Tổng thời lượng mới %d phút (yêu cầu tối thiểu %d phút)',
                        $durationMinutes,
                        Course::MIN_VIDEO_DURATION_MINUTES,
                    ),
            ),
        ];

        return new CourseSubmissionCheckResult($items);
    }

    /**
     * @return array{key: string, label: string, passed: bool, message: string|null}
     */
    private function makeItem(string $key, string $label, bool $passed, ?string $failMessage): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'passed' => $passed,
            'message' => $passed ? null : $failMessage,
        ];
    }
}
