<?php

namespace Tests\Unit;

use App\Models\Lesson;
use PHPUnit\Framework\TestCase;

class PointServiceTest extends TestCase
{
    /**
     * Test video duration under 30 minutes (< 1800s) awards 10 points.
     */
    public function test_lesson_under_30_minutes_awards_10_points(): void
    {
        $lesson5m = new Lesson(['duration_seconds' => 300]);
        $lesson20m = new Lesson(['duration_seconds' => 1200]);
        $lesson29m = new Lesson(['duration_seconds' => 1740]);
        $lesson29m59s = new Lesson(['duration_seconds' => 1799]);

        $this->assertSame(10, $this->calculatePoints($lesson5m));
        $this->assertSame(10, $this->calculatePoints($lesson20m));
        $this->assertSame(10, $this->calculatePoints($lesson29m));
        $this->assertSame(10, $this->calculatePoints($lesson29m59s));
    }

    /**
     * Test video duration from 30 minutes to under 60 minutes (1800s - 3599s) awards 15 points.
     */
    public function test_lesson_between_30_and_60_minutes_awards_15_points(): void
    {
        $lesson30m = new Lesson(['duration_seconds' => 1800]);
        $lesson45m = new Lesson(['duration_seconds' => 2700]);
        $lesson59m = new Lesson(['duration_seconds' => 3540]);
        $lesson59m59s = new Lesson(['duration_seconds' => 3599]);

        $this->assertSame(15, $this->calculatePoints($lesson30m));
        $this->assertSame(15, $this->calculatePoints($lesson45m));
        $this->assertSame(15, $this->calculatePoints($lesson59m));
        $this->assertSame(15, $this->calculatePoints($lesson59m59s));
    }

    /**
     * Test video duration 60 minutes or more (>= 3600s) awards 20 points.
     */
    public function test_lesson_60_minutes_or_more_awards_20_points(): void
    {
        $lesson60m = new Lesson(['duration_seconds' => 3600]);
        $lesson90m = new Lesson(['duration_seconds' => 5400]);
        $lesson120m = new Lesson(['duration_seconds' => 7200]);

        $this->assertSame(20, $this->calculatePoints($lesson60m));
        $this->assertSame(20, $this->calculatePoints($lesson90m));
        $this->assertSame(20, $this->calculatePoints($lesson120m));
    }

    /**
     * Test lesson without duration defaults to 10 points.
     */
    public function test_lesson_without_duration_defaults_to_10_points(): void
    {
        $noDurationLesson = new Lesson([]);
        $this->assertSame(10, $this->calculatePoints($noDurationLesson));
    }

    /**
     * Test free course detection:
     * - Khóa học gốc 0đ: Không tính điểm (isFree = true)
     * - Khóa học có phí > 0đ (kể cả dùng voucher giảm về 0đ): Vẫn tính điểm (isFree = false)
     */
    public function test_course_is_free(): void
    {
        $freeCourseZero = new \App\Models\Course(['price' => 0]);
        $freeCourseNull = new \App\Models\Course(['price' => null]);
        $paidCourse = new \App\Models\Course(['price' => 200000, 'discount_price' => null]);
        $paidCourseWithVoucherOrDiscount = new \App\Models\Course(['price' => 200000, 'discount_price' => 0]);

        $this->assertTrue($freeCourseZero->isFree());
        $this->assertTrue($freeCourseNull->isFree());
        $this->assertFalse($paidCourse->isFree());
        $this->assertFalse($paidCourseWithVoucherOrDiscount->isFree());
    }

    /**
     * Test role student check:
     * - Student: isStudent = true
     * - Instructor / Admin: isStudent = false
     */
    public function test_user_is_student(): void
    {
        $student = new \App\Models\User(['role' => 'student']);
        $instructor = new \App\Models\User(['role' => 'instructor']);
        $admin = new \App\Models\User(['role' => 'admin']);

        $this->assertTrue($student->isStudent());
        $this->assertFalse($instructor->isStudent());
        $this->assertFalse($admin->isStudent());
    }

    private function calculatePoints(Lesson $lesson): int
    {
        $durationSeconds = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);

        if ($durationSeconds >= 3600) {
            return 20;
        } elseif ($durationSeconds >= 1800) {
            return 15;
        } else {
            return 10;
        }
    }
}
