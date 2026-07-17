<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReplyReviewRequest;
use App\Models\Course;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function index(Request $request): View
    {
        $courseId = $request->integer('course_id') ?: null;
        $rating = $request->integer('rating') ?: null;
        $status = $request->query('status');
        $replyState = $request->query('reply');

        $reviews = Review::query()
            ->whereHas('course', fn ($query) => $query->where('instructor_id', $request->user()->id))
            ->with(['user:id,name,avatar', 'course:id,instructor_id,title,slug', 'replier:id,name'])
            ->when($courseId, fn ($query) => $query->where('course_id', $courseId))
            ->when($rating && $rating >= 1 && $rating <= 5, fn ($query) => $query->where('rating', $rating))
            ->when(in_array($status, ['pending', 'approved', 'rejected', 'hidden'], true), fn ($query) => $query->where('status', $status))
            ->when($replyState === 'replied', fn ($query) => $query->whereNotNull('instructor_reply'))
            ->when($replyState === 'unreplied', fn ($query) => $query->whereNull('instructor_reply'))
            ->latest()
            ->paginate(config('reviews.per_page', 8))
            ->withQueryString();

        $courses = Course::query()
            ->where('instructor_id', $request->user()->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('instructor.reviews.index', compact('reviews', 'courses', 'courseId', 'rating', 'status', 'replyState'));
    }

    public function reply(ReplyReviewRequest $request, Course $course, Review $review): RedirectResponse
    {
        abort_unless((int) $review->course_id === (int) $course->id, 404);
        $this->reviews->reply($review, $request->user(), $request->validated('instructor_reply'));

        return back()->with('success', 'Đã lưu phản hồi của giảng viên.');
    }

    public function destroyReply(Course $course, Review $review): RedirectResponse
    {
        abort_unless((int) $review->course_id === (int) $course->id, 404);
        $this->authorize('reply', $review);
        $this->reviews->removeReply($review);

        return back()->with('success', 'Đã xóa phản hồi của giảng viên.');
    }
}
