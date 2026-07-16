<?php

namespace App\Services;

use App\Enums\CourseReviewStatus;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CourseReviewService
{
    public function submitForReview(Course $course, User $instructor): CourseReview
    {
        abort_unless($course->isOwnedBy($instructor), 403);
        abort_unless($course->isEditable(), 422, 'Khóa học không ở trạng thái cho phép gửi duyệt.');

        return DB::transaction(function () use ($course) {
            $submissionNumber = (int) $course->submission_count + 1;

            $review = CourseReview::create([
                'course_id' => $course->id,
                'reviewer_id' => null,
                'submission_number' => $submissionNumber,
                'status' => CourseReviewStatus::Pending,
                'submitted_at' => now(),
            ]);

            $course->update([
                'status' => CourseStatus::PendingReview->value,
                'is_published' => false,
                'submitted_at' => now(),
                'submission_count' => $submissionNumber,
                'reject_reason' => null,
            ]);

            $this->notifyAdmins($course, 'course_submitted', 'Khóa học chờ duyệt', "Giảng viên đã gửi khóa học \"{$course->title}\" lần {$submissionNumber}.");

            ActivityLogService::log($course->instructor_id, 'submit_course_review', Course::class, $course->id);

            return $review;
        });
    }

    public function approve(Course $course, User $admin, array $checklist, bool $publishImmediately = true): CourseReview
    {
        abort_unless($admin->isAdmin(), 403);
        abort_unless($course->status === CourseStatus::PendingReview->value, 422);

        $this->assertChecklistComplete($checklist);

        return DB::transaction(function () use ($course, $admin, $checklist, $publishImmediately) {
            $review = $this->latestPendingReview($course);

            $review->update([
                'reviewer_id' => $admin->id,
                'status' => CourseReviewStatus::Approved,
                'checklist_json' => $checklist,
                'reviewed_at' => now(),
            ]);

            $courseUpdates = [
                'status' => $publishImmediately ? CourseStatus::Published->value : CourseStatus::Approved->value,
                'is_published' => $publishImmediately,
                'approved_at' => now(),
                'reject_reason' => null,
            ];

            if ($publishImmediately) {
                $courseUpdates['published_at'] = $course->published_at ?? now();
            }

            $course->update($courseUpdates);

            $this->notifyInstructor($course, 'course_approved', 'Khóa học đã được duyệt',
                $publishImmediately
                    ? "Khóa học \"{$course->title}\" đã được duyệt và xuất bản."
                    : "Khóa học \"{$course->title}\" đã được duyệt. Chờ xuất bản.");

            ActivityLogService::log($admin->id, 'approve_course', Course::class, $course->id);

            return $review->fresh();
        });
    }

    public function reject(Course $course, User $admin, string $comment, array $checklist = []): CourseReview
    {
        abort_unless($admin->isAdmin(), 403);
        abort_unless($course->status === CourseStatus::PendingReview->value, 422);

        $comment = trim($comment);
        abort_if(strlen($comment) < config('course.reject_reason_min_length', 10), 422, 'Lý do từ chối phải có ít nhất 10 ký tự.');

        return DB::transaction(function () use ($course, $admin, $comment, $checklist) {
            $review = $this->latestPendingReview($course);

            $review->update([
                'reviewer_id' => $admin->id,
                'status' => CourseReviewStatus::Rejected,
                'comment' => $comment,
                'checklist_json' => $checklist ?: null,
                'reviewed_at' => now(),
            ]);

            $course->update([
                'status' => CourseStatus::Rejected->value,
                'is_published' => false,
                'reject_reason' => $comment,
            ]);

            $this->notifyInstructor($course, 'course_rejected', 'Khóa học bị từ chối',
                "Khóa học \"{$course->title}\" bị từ chối. Lý do: {$comment}");

            ActivityLogService::log($admin->id, 'reject_course', Course::class, $course->id);

            return $review->fresh();
        });
    }

    public function suspend(Course $course, User $admin): void
    {
        abort_unless($admin->isAdmin(), 403);

        $course->update([
            'status' => CourseStatus::Suspended->value,
            'is_published' => false,
            'suspended_at' => now(),
        ]);

        ActivityLogService::log($admin->id, 'suspend_course', Course::class, $course->id);
    }

    public function publish(Course $course, User $admin): void
    {
        abort_unless($admin->isAdmin(), 403);
        abort_unless(in_array($course->status, [CourseStatus::Approved->value, CourseStatus::Suspended->value], true), 422);

        $course->update([
            'status' => CourseStatus::Published->value,
            'is_published' => true,
            'published_at' => $course->published_at ?? now(),
            'suspended_at' => null,
        ]);

        ActivityLogService::log($admin->id, 'publish_course', Course::class, $course->id);
    }

    private function latestPendingReview(Course $course): CourseReview
    {
        return CourseReview::query()
            ->where('course_id', $course->id)
            ->where('status', CourseReviewStatus::Pending)
            ->orderByDesc('submission_number')
            ->firstOrFail();
    }

    private function assertChecklistComplete(array $checklist): void
    {
        $required = array_keys(config('course.admin_review_checklist', []));
        $missing = collect($required)->filter(fn ($key) => empty($checklist[$key]));

        abort_if($missing->isNotEmpty(), 422, 'Vui lòng hoàn thành checklist kiểm duyệt.');
    }

    private function notifyInstructor(Course $course, string $type, string $title, string $message): void
    {
        if (! $course->instructor_id) {
            return;
        }

        PushNotification::create([
            'user_id' => $course->instructor_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => route('instructor.courses.edit', $course),
            'is_read' => false,
        ]);
    }

    private function notifyAdmins(Course $course, string $type, string $title, string $message): void
    {
        User::query()->where('role', 'admin')->each(function (User $admin) use ($course, $type, $title, $message) {
            PushNotification::create([
                'user_id' => $admin->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'url' => route('admin.course-reviews.show', $course),
                'is_read' => false,
            ]);
        });
    }
}
