<?php

namespace App\Services;

use App\Enums\CourseReviewStatus;
use App\Enums\CourseStatus;
use App\Models\ContentUpdate;
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
        abort_unless($course->canBeSubmittedForReview(), 422, 'Khóa học không ở trạng thái cho phép gửi duyệt.');

        $hasAgreement = $course->copyright_agreed || request()->boolean('copyright_agreed');
        abort_unless($hasAgreement, 422, 'Bạn phải đồng ý với cam kết bản quyền trước khi gửi duyệt.');

        return DB::transaction(function () use ($course, $instructor) {
            // Serialize submission against concurrent approval/rejection and
            // only transition drafts. Existing pending/terminal records are
            // never silently rewritten.
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            abort_unless(in_array($course->status, [
                CourseStatus::Draft->value,
                CourseStatus::Rejected->value,
                CourseStatus::Published->value,
                CourseStatus::PendingUpdate->value,
                CourseStatus::RejectedUpdate->value,
            ], true), 422, 'Khóa học không ở trạng thái cho phép gửi duyệt.');
            $isAlreadyPublished = (bool) $course->is_published || in_array($course->status, [CourseStatus::Published->value, CourseStatus::PendingUpdate->value, CourseStatus::RejectedUpdate->value], true);
            $submissionNumber = (int) $course->submission_count + 1;

            $review = CourseReview::create([
                'course_id' => $course->id,
                'reviewer_id' => null,
                'submission_number' => $submissionNumber,
                'status' => CourseReviewStatus::Pending,
                'submitted_at' => now(),
            ]);

            $newStatus = $isAlreadyPublished ? CourseStatus::PendingUpdate->value : CourseStatus::PendingReview->value;

            $course->update([
                'status' => $newStatus,
                'is_published' => $isAlreadyPublished ? true : false,
                'submitted_at' => now(),
                'submission_count' => $submissionNumber,
                'reject_reason' => null,
                'copyright_agreed' => true,
                'copyright_agreed_at' => now(),
                'copyright_agreed_by' => $instructor->id,
            ]);

            // Cập nhật mốc thời gian submitted_at và chuyển trạng thái pending cho các bản ghi content_updates của khóa học này
            $draftUpdates = ContentUpdate::query()
                ->where('course_id', $course->id)
                ->where('status', ContentUpdate::STATUS_DRAFT)
                ->lockForUpdate()
                ->get();

            foreach ($draftUpdates as $draftUpdate) {
                if ($isAlreadyPublished) {
                    // Finalize the mutable authoring candidate before crossing
                    // the immutable draft -> pending boundary.
                    app(ContentVersionService::class)->prepareDraftCandidate($draftUpdate, $instructor);
                }
                $draftUpdate->update([
                    'status' => ContentUpdate::STATUS_PENDING,
                    'submitted_at' => now(),
                ]);
            }

            $noticeMsg = $isAlreadyPublished
                ? "Giảng viên đã gửi bản CẬP NHẬT khóa học \"{$course->title}\" lần {$submissionNumber}."
                : "Giảng viên đã gửi khóa học \"{$course->title}\" lần {$submissionNumber}.";

            $this->notifyAdmins($course, 'course_submitted', 'Khóa học chờ duyệt', $noticeMsg);

            ActivityLogService::log(
                $instructor->id,
                'copyright_agreed',
                Course::class,
                $course->id,
                [
                    'agreed_at' => now()->toDateTimeString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
                request(),
                "Giảng viên đồng ý cam kết bản quyền cho khóa học \"{$course->title}\""
            );

            ActivityLogService::log($course->instructor_id, 'submit_course_review', Course::class, $course->id);

            return $review;
        });
    }

    public function approve(Course $course, User $admin, array $checklist, bool $publishImmediately = true): CourseReview
    {
        abort_unless($admin->isAdmin(), 403);
        abort_unless(in_array($course->status, [CourseStatus::PendingReview->value, CourseStatus::PendingUpdate->value], true), 422);

        $this->assertChecklistComplete($checklist);

        return DB::transaction(function () use ($course, $admin, $checklist, $publishImmediately) {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            abort_unless(in_array($course->status, [CourseStatus::PendingReview->value, CourseStatus::PendingUpdate->value], true), 422);
            $wasAlreadyPublished = (bool) $course->is_published || in_array($course->status, [
                Course::STATUS_PUBLISHED,
                Course::STATUS_PENDING_UPDATE,
                Course::STATUS_REJECTED_UPDATE,
            ], true);
            $review = $this->latestPendingReview($course);

            if ($review) {
                $review->update([
                    'reviewer_id' => $admin->id,
                    'status' => CourseReviewStatus::Approved,
                    'checklist_json' => $checklist,
                    'reviewed_at' => now(),
                ]);
            }

            // Tự động phê duyệt toàn bộ các bản ghi content_updates đang pending của khóa học này (phê duyệt chapter trước lesson)
            $pendingUpdates = ContentUpdate::query()
                ->where('course_id', $course->id)
                ->where('status', ContentUpdate::STATUS_PENDING)
                ->orderByRaw("CASE type WHEN 'chapter' THEN 1 WHEN 'lesson' THEN 2 WHEN 'quiz' THEN 3 WHEN 'course' THEN 4 ELSE 5 END")
                ->lockForUpdate()
                ->get();

            $contentUpdateService = app(ContentUpdateService::class);
            foreach ($pendingUpdates as $pendingUpdate) {
                $contentUpdateService->applyApprovedUpdate($pendingUpdate, $admin);
            }

            if (! $wasAlreadyPublished) {
                app(QuizVersioningService::class)->publishInitialCourseDrafts($course);
                app(ContentVersionService::class)->publishInitialCourseTree($course, $admin);
            }

            $instructor = $course->relationLoaded('instructor') ? $course->instructor : $course->instructor()->first();
            $isInstructorApproved = $instructor && $instructor->instructor_status === 'approved' && ! $instructor->isLocked();

            $shouldPublish = $publishImmediately && $isInstructorApproved;

            $courseUpdates = [
                'status' => $shouldPublish ? CourseStatus::Published->value : CourseStatus::Approved->value,
                'is_published' => $shouldPublish,
                'approved_at' => now(),
                'reject_reason' => null,
            ];

            if ($shouldPublish) {
                $courseUpdates['published_at'] = $course->published_at ?? now();
            }

            $course->update($courseUpdates);

            if ($shouldPublish) {
                $this->notifyInstructor(
                    $course,
                    'course_approved',
                    'Khóa học đã được duyệt & xuất bản',
                    "Khóa học \"{$course->title}\" đã được duyệt và xuất bản công khai cho học viên."
                );
            } else {
                $this->notifyInstructor(
                    $course,
                    'course_approved_content',
                    'Khóa học đã được duyệt nội dung',
                    "Khóa học \"{$course->title}\" đã được duyệt nội dung thành công. Khóa học sẽ tự động hiển thị công khai ngay sau khi hồ sơ giảng viên của bạn được phê duyệt."
                );
            }

            ActivityLogService::log($admin->id, 'approve_course', Course::class, $course->id);

            return $review ? $review->fresh() : new CourseReview;
        });
    }

    /**
     * Tự động kích hoạt xuất bản (publish) toàn bộ các khóa học đã được duyệt nội dung
     * của giảng viên khi hồ sơ giảng viên được Admin phê duyệt.
     *
     * @return int Số lượng khóa học đã được kích hoạt xuất bản
     */
    public function syncInstructorApprovedCourses(User $instructor): int
    {
        if ($instructor->instructor_status !== 'approved' || $instructor->isLocked()) {
            return 0;
        }

        // Tìm các khóa học của giảng viên đã được duyệt nội dung trước đó nhưng chưa xuất bản
        $approvedCourses = Course::query()
            ->where('instructor_id', $instructor->id)
            ->where(function ($q) {
                $q->where('status', CourseStatus::Approved->value)
                    ->orWhere(function ($subQ) {
                        $subQ->whereNotNull('approved_at')
                            ->where('is_published', false)
                            ->whereNotIn('status', [
                                CourseStatus::Draft->value,
                                CourseStatus::PendingReview->value,
                                CourseStatus::Rejected->value,
                                CourseStatus::Suspended->value,
                                CourseStatus::Archived->value,
                            ]);
                    });
            })
            ->get();

        $count = 0;
        foreach ($approvedCourses as $course) {
            app(QuizVersioningService::class)->publishInitialCourseDrafts($course);
            app(ContentVersionService::class)->publishInitialCourseTree($course, $instructor);
            $course->update([
                'status' => CourseStatus::Published->value,
                'is_published' => true,
                'published_at' => $course->published_at ?? now(),
            ]);

            $this->notifyInstructor(
                $course,
                'course_published',
                'Khóa học đã sẵn sàng hiển thị cho học viên',
                "Hồ sơ giảng viên của bạn đã được duyệt. Khóa học \"{$course->title}\" đã được tự động kích hoạt xuất bản và sẵn sàng cho học viên tham gia."
            );

            $count++;
        }

        return $count;
    }

    public function reject(Course $course, User $admin, string $comment, array $checklist = []): CourseReview
    {
        abort_unless($admin->isAdmin(), 403);
        abort_unless(in_array($course->status, [CourseStatus::PendingReview->value, CourseStatus::PendingUpdate->value], true), 422);

        $comment = trim($comment);
        abort_if(strlen($comment) < config('course.reject_reason_min_length', 10), 422, 'Lý do từ chối phải có ít nhất 10 ký tự.');

        return DB::transaction(function () use ($course, $admin, $comment, $checklist) {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            abort_unless(in_array($course->status, [CourseStatus::PendingReview->value, CourseStatus::PendingUpdate->value], true), 422);
            $wasPublished = (bool) $course->is_published || $course->status === CourseStatus::PendingUpdate->value;
            $review = $this->latestPendingReview($course);

            if ($review) {
                $review->update([
                    'reviewer_id' => $admin->id,
                    'status' => CourseReviewStatus::Rejected,
                    'comment' => $comment,
                    'checklist_json' => $checklist ?: null,
                    'reviewed_at' => now(),
                ]);
            }

            // Cập nhật trạng thái các content_updates đang pending thành rejected
            $pendingUpdates = ContentUpdate::query()
                ->where('course_id', $course->id)
                ->where('status', ContentUpdate::STATUS_PENDING)
                ->lockForUpdate()
                ->get();

            $contentUpdateService = app(ContentUpdateService::class);
            foreach ($pendingUpdates as $pendingUpdate) {
                $contentUpdateService->rejectUpdate($pendingUpdate, $admin, $comment);
            }

            $newStatus = $wasPublished ? CourseStatus::RejectedUpdate->value : CourseStatus::Rejected->value;

            $course->update([
                'status' => $newStatus,
                'is_published' => $wasPublished ? true : false,
                'reject_reason' => $comment,
            ]);

            $this->notifyInstructor($course, 'course_rejected', 'Bản cập nhật khóa học bị từ chối',
                "Khóa học \"{$course->title}\" bị từ chối. Lý do: {$comment}");

            ActivityLogService::log($admin->id, 'reject_course', Course::class, $course->id);

            return $review ? $review->fresh() : new CourseReview;
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

        DB::transaction(function () use ($course, $admin): void {
            app(QuizVersioningService::class)->publishInitialCourseDrafts($course);
            app(ContentVersionService::class)->publishInitialCourseTree($course, $admin);
            $course->update([
                'status' => CourseStatus::Published->value,
                'is_published' => true,
                'published_at' => $course->published_at ?? now(),
                'suspended_at' => null,
            ]);
        });

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
            'created_by' => auth()->id() ?? $course->instructor_id,
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
                'created_by' => auth()->id() ?? $course->instructor_id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'url' => route('admin.course-reviews.show', $course),
                'is_read' => false,
            ]);
        });
    }
}
