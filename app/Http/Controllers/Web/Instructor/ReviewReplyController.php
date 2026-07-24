<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\NotificationService;
use App\Enums\ReviewStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ReviewReplyController extends Controller
{
    public function store(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'comment' => ['required', 'string', 'min:2', 'max:1500', 'not_regex:/<[^>]*>/'],
        ]);

        $course = $review->course;
        abort_unless($course && (int) $course->instructor_id === (int) $request->user()->id, 403);
        abort_if($review->isReply(), 400, 'Cannot reply to a reply.');

        // Check if there is already a reply to this review
        $exists = Review::query()->where('parent_id', $review->id)->exists();
        abort_if($exists, 400, 'Đánh giá đã có phản hồi.');

        $reply = Review::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'parent_id' => $review->id,
            'rating' => null,
            'comment' => $request->input('comment'),
            'status' => ReviewStatus::Approved->value,
            'verified_purchase' => false,
        ]);

        // Send notification to student who reviewed
        $student = $review->user;
        if ($student) {
            app(NotificationService::class)->send(
                $student,
                'Phản hồi mới từ giảng viên',
                "Giảng viên đã phản hồi đánh giá của bạn trong khóa học \"{$course->title}\".",
                'review_reply',
                route('courses.show', $course->slug).'#reviews'
            );
        }

        return back()->with('success', 'Đã lưu phản hồi của giảng viên.');
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'comment' => ['required', 'string', 'min:2', 'max:1500', 'not_regex:/<[^>]*>/'],
        ]);

        abort_unless($review->isReply(), 404);

        $course = $review->course;
        abort_unless($course && (int) $course->instructor_id === (int) $request->user()->id, 403);
        abort_unless((int) $review->user_id === (int) $request->user()->id, 403);

        $review->update([
            'comment' => $request->input('comment'),
        ]);

        return back()->with('success', 'Đã cập nhật phản hồi của giảng viên.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        abort_unless($review->isReply(), 404);

        $course = $review->course;
        abort_unless($course && (int) $course->instructor_id === (int) auth()->id(), 403);
        abort_unless((int) $review->user_id === (int) auth()->id(), 403);

        $review->delete();

        return back()->with('success', 'Đã xóa phản hồi của giảng viên.');
    }
}
