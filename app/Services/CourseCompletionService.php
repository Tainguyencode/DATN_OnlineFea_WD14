<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\PushNotification;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Notifications\CertificateIssuedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Service Kiểm tra Hoàn thành Khóa học & Cấp Chứng chỉ Tự động (CourseCompletionService)
 * 
 * Chức năng chính:
 * 1. Kiểm tra điều kiện hoàn thành 3 tầng: Xem 100% Video, Làm 100% Bài Trắc nghiệm (Quiz), Đạt điểm Bài tập Tự luận (Assignment >= passing_score).
 * 2. Cập nhật trạng thái `Enrollment` sang `completed` và ghi nhận `completed_at`.
 * 3. Tự động khởi tạo Mã chứng chỉ điện tử duy nhất dạng `FEA-XXXXXXXX`.
 * 4. Gọi `CertificatePdfService` tạo file PDF chứng chỉ và gửi Email chúc mừng đính kèm thông báo Push Notification.
 */
class CourseCompletionService
{
    /**
     * Kiểm tra chi tiết tiến độ và điều kiện hoàn thành khóa học của Học viên.
     * 
     * @param Enrollment $enrollment Bản ghi ghi danh học viên
     * @param int $userId ID học viên
     * @return array [eligible, progress_percent, missing_requirements, completed_at]
     */
    public function check(Enrollment $enrollment, int $userId): array
    {
        $course = $enrollment->course()->with(['lessons.quiz', 'lessons.assignment'])->first();
        $missing = [];

        // Lấy danh sách tất cả bài học bắt buộc trong khóa học
        $allLessons = Lesson::query()
            ->where(function ($query) use ($course) {
                $query->where('course_id', $course->id)
                    ->orWhereHas('section', fn ($q) => $q->where('course_id', $course->id))
                    ->orWhereHas('chapter', fn ($q) => $q->where('course_id', $course->id));
            })
            ->where('is_required', true)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'published');
            })
            ->get();

        // 1. Kiểm tra hoàn thành 100% bài học Video
        $videoLessons = $allLessons->where('type', 'video');
        foreach ($videoLessons as $lesson) {
            $progress = LessonProgress::where('user_id', $userId)->where('lesson_id', $lesson->id)->first();
            if (! $progress || ! $progress->is_completed) {
                $missing[] = "Video \"{$lesson->title}\" chưa hoàn thành.";
            }
        }

        // 2. Kiểm tra hoàn thành 100% bài kiểm tra Trắc nghiệm (Quiz)
        $quizLessons = $allLessons->where('type', 'quiz');
        foreach ($quizLessons as $lesson) {
            $quiz = $lesson->quiz;
            if (! $quiz) {
                continue;
            }
            $submitted = QuizAttempt::query()
                ->where('user_id', $userId)
                ->where('quiz_id', $quiz->id)
                ->whereNotNull('completed_at')
                ->exists();
            if (! $submitted) {
                $missing[] = "Bài trắc nghiệm \"{$lesson->title}\" chưa đạt điểm yêu cầu.";
            }
        }

        // 3. Kiểm tra hoàn thành 100% bài tập tự luận (Assignments) và đạt điểm số yêu cầu
        $assignmentLessons = $allLessons->where('type', 'assignment');
        foreach ($assignmentLessons as $lesson) {
            $assignment = $lesson->assignment;
            if (! $assignment) {
                continue;
            }
            $passed = \App\Models\Submission::query()
                ->where('user_id', $userId)
                ->where('assignment_id', $assignment->id)
                ->where('status', 'graded')
                ->where('score', '>=', $assignment->passing_score ?? 70)
                ->exists();
            if (! $passed) {
                $missing[] = "Bài tập tự luận \"{$lesson->title}\" chưa đạt điểm đạt yêu cầu ({$assignment->passing_score} điểm).";
            }
        }

        $eligible = $missing === [] && $enrollment->hasLearningAccess();
        $completedAt = $enrollment->completed_at;

        // Nếu đủ điều kiện và chưa ghi nhận hoàn thành -> Cấp chứng chỉ & Bắn thông báo
        if ($eligible) {
            if (! $completedAt) {
                $completedAt = now();
                $enrollment->update([
                    'completed_at' => $completedAt,
                    'status' => Enrollment::STATUS_COMPLETED,
                ]);

                // Cộng +50 XP hoàn thành khóa học (1 lần duy nhất)
                app(\App\Services\PointService::class)->awardCourseCompletionPoints($userId, $course->id);

                $certificate = $this->issueCertificate($userId, $course);

                PushNotification::create([
                    'user_id' => $userId,
                    'title' => 'Hoàn thành khóa học',
                    'message' => "Chúc mừng! Bạn đã hoàn thành khóa học \"{$course->title}\".",
                    'type' => $certificate ? 'certificate_issued' : 'course_completed',
                    'url' => $certificate
                        ? route('student.certificates')
                        : route('student.dashboard'),
                    'is_read' => false,
                ]);
            } elseif ($enrollment->status !== Enrollment::STATUS_COMPLETED) {
                $enrollment->update([
                    'status' => Enrollment::STATUS_COMPLETED,
                ]);
            }
        }

        return [
            'eligible' => $eligible,
            'progress_percent' => (float) $enrollment->progress_percent,
            'missing_requirements' => $missing,
            'completed_at' => $completedAt?->toIso8601String(),
        ];
    }

    /**
     * Khởi tạo Chứng chỉ điện tử cho Học viên sau khi hoàn thành khóa học.
     * 
     * @param int $userId ID học viên
     * @param Course $course Model khóa học
     * @return Certificate|null Model chứng chỉ vừa tạo hoặc null nếu khóa học tắt chứng chỉ
     */
    private function issueCertificate(int $userId, Course $course): ?Certificate
    {
        if (! $course->certificate_enabled) {
            return null;
        }

        $certificate = Certificate::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $course->id],
            [
                'certificate_code' => 'FEA-'.strtoupper(Str::random(8)),
                'issued_at' => now(),
            ]
        );

        $wasRecentlyCreated = $certificate->wasRecentlyCreated;

        // Sinh file PDF chứng chỉ chuẩn mực
        app(CertificatePdfService::class)->ensureStored($certificate);
        $certificate->refresh();

        if ($wasRecentlyCreated) {
            $this->sendCertificateEmail($userId, $course, $certificate);
        }

        return $certificate;
    }

    /**
     * Gửi email thông báo cấp chứng chỉ kèm đường dẫn tải PDF cho Học viên.
     * 
     * @param int $userId ID học viên
     * @param Course $course Khóa học
     * @param Certificate $certificate Chứng chỉ
     */
    private function sendCertificateEmail(int $userId, Course $course, Certificate $certificate): void
    {
        try {
            $user = User::find($userId);
            if (! $user) {
                return;
            }

            $user->notify(new CertificateIssuedNotification($course, $certificate));
        } catch (Throwable $exception) {
            Log::warning('Certificate email failed; certificate remains issued.', [
                'user_id' => $userId,
                'course_id' => $course->id,
                'certificate_id' => $certificate->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
