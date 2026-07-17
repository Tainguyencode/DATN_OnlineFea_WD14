<?php

namespace App\Services;

use App\Enums\ReviewStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\ReviewHelpful;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function create(Course $course, User $user, array $data): Review
    {
        try {
            return DB::transaction(function () use ($course, $user, $data) {
                Enrollment::query()
                    ->where('user_id', $user->id)
                    ->where('course_id', $course->id)
                    ->withLearningAccess()
                    ->lockForUpdate()
                    ->firstOrFail();

                $review = Review::query()->create([
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'rating' => $data['rating'],
                    'comment' => $data['comment'],
                    'status' => config('reviews.default_status', ReviewStatus::Pending->value),
                    'verified_purchase' => true,
                ]);

                $this->syncCourseRating($course->id);

                $course->loadMissing('instructor');
                if ($course->instructor && (int) $course->instructor->id !== (int) $user->id) {
                    $this->notifications->send(
                        $course->instructor,
                        'Khóa học có đánh giá mới',
                        "{$user->name} vừa gửi đánh giá cho khóa học {$course->title}.",
                        'course_review_created',
                        route('instructor.reviews.index', ['course_id' => $course->id]),
                    );
                }

                return $review;
            });
        } catch (QueryException $exception) {
            if (Review::withTrashed()->where('course_id', $course->id)->where('user_id', $user->id)->exists()) {
                throw ValidationException::withMessages([
                    'rating' => 'Bạn đã đánh giá khóa học này. Vui lòng chỉnh sửa đánh giá hiện có.',
                ]);
            }

            throw $exception;
        }
    }

    public function update(Review $review, array $data): Review
    {
        return DB::transaction(function () use ($review, $data) {
            Review::query()->whereKey($review->getKey())->lockForUpdate()->firstOrFail();
            $status = config('reviews.reset_status_on_update', true)
                ? ReviewStatus::Pending->value
                : $review->status->value;

            $review->update([
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'status' => $status,
                'moderated_by' => null,
                'moderated_at' => null,
                'moderation_note' => null,
            ]);

            $this->syncCourseRating($review->course_id);

            return $review->refresh();
        });
    }

    public function delete(Review $review): void
    {
        DB::transaction(function () use ($review) {
            $courseId = $review->course_id;
            $review->helpfulRecords()->delete();
            $review->delete();
            $this->syncCourseRating($courseId);
        });
    }

    public function moderate(Review $review, ReviewStatus $status, User $moderator, ?string $note = null): Review
    {
        return DB::transaction(function () use ($review, $status, $moderator, $note) {
            $review->update([
                'status' => $status->value,
                'moderated_by' => $moderator->id,
                'moderated_at' => now(),
                'moderation_note' => $note,
            ]);

            $this->syncCourseRating($review->course_id);
            $review->loadMissing(['user', 'course']);

            $labels = [
                ReviewStatus::Approved->value => 'được duyệt',
                ReviewStatus::Rejected->value => 'bị từ chối',
                ReviewStatus::Hidden->value => 'được ẩn',
            ];

            if ($review->user) {
                $this->notifications->send(
                    $review->user,
                    'Cập nhật trạng thái đánh giá',
                    'Đánh giá của bạn cho khóa học '.$review->course?->title.' đã '.($labels[$status->value] ?? 'được cập nhật').'.',
                    'course_review_moderated',
                    route('student.reviews.index'),
                );
            }

            return $review->refresh();
        });
    }

    public function reply(Review $review, User $instructor, string $reply): Review
    {
        return DB::transaction(function () use ($review, $instructor, $reply) {
            $review->update([
                'instructor_reply' => $reply,
                'replied_by' => $instructor->id,
                'replied_at' => now(),
            ]);

            $review->loadMissing(['user', 'course']);
            if ($review->user && $review->course) {
                $this->notifications->send(
                    $review->user,
                    'Giảng viên đã phản hồi đánh giá',
                    'Đánh giá của bạn cho khóa học '.$review->course->title.' vừa nhận được phản hồi.',
                    'course_review_replied',
                    route('courses.show', $review->course->slug).'#reviews',
                );
            }

            return $review->refresh();
        });
    }

    public function removeReply(Review $review): Review
    {
        $review->update(['instructor_reply' => null, 'replied_by' => null, 'replied_at' => null]);

        return $review->refresh();
    }

    public function toggleHelpful(Review $review, User $user): bool
    {
        return DB::transaction(function () use ($review, $user) {
            $lockedReview = Review::query()->lockForUpdate()->findOrFail($review->id);
            $record = ReviewHelpful::query()
                ->where('review_id', $lockedReview->id)
                ->where('user_id', $user->id)
                ->first();

            if ($record) {
                $record->delete();
                $marked = false;
            } else {
                try {
                    ReviewHelpful::query()->create(['review_id' => $lockedReview->id, 'user_id' => $user->id]);
                } catch (QueryException) {
                    // The unique index resolves concurrent duplicate requests safely.
                }
                $marked = true;
            }

            $lockedReview->update([
                'helpful_count' => ReviewHelpful::query()->where('review_id', $lockedReview->id)->count(),
            ]);

            return $marked;
        });
    }

    public function syncCourseRating(int $courseId): void
    {
        $course = Course::query()->lockForUpdate()->find($courseId);
        if (! $course) {
            return;
        }

        $aggregate = Review::query()
            ->approved()
            ->where('course_id', $courseId)
            ->selectRaw('COUNT(*) as review_count, COALESCE(AVG(rating), 0) as review_avg')
            ->first();

        $course->forceFill([
            'rating_count' => (int) $aggregate->review_count,
            'rating_avg' => round((float) $aggregate->review_avg, 2),
        ])->save();
    }

    public function syncHelpfulCount(Review $review): int
    {
        $count = $review->helpfulRecords()->count();
        $review->forceFill(['helpful_count' => max(0, $count)])->save();

        return $count;
    }
}
