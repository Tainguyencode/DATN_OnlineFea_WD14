<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ModerateReviewRequest;
use App\Models\Course;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StudentReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function index(Request $request): View
    {
        Gate::authorize('course_reviews.view');
        $filters = $request->only(['keyword', 'course_id', 'instructor_id', 'rating', 'status', 'date_from', 'date_to', 'reply']);

        $reviews = Review::query()
            ->with(['user:id,name,email,avatar', 'course:id,instructor_id,title,slug', 'course.instructor:id,name', 'replier:id,name', 'moderator:id,name'])
            ->when($filters['keyword'] ?? null, function ($query, $keyword) {
                $query->where(function ($nested) use ($keyword) {
                    $nested->where('comment', 'like', "%{$keyword}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('course', fn ($course) => $course->where('title', 'like', "%{$keyword}%"));
                });
            })
            ->when($filters['course_id'] ?? null, fn ($query, $id) => $query->where('course_id', $id))
            ->when($filters['instructor_id'] ?? null, fn ($query, $id) => $query->whereHas('course', fn ($course) => $course->where('instructor_id', $id)))
            ->when($filters['rating'] ?? null, fn ($query, $rating) => $query->where('rating', $rating))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->when(($filters['reply'] ?? null) === 'replied', fn ($query) => $query->whereNotNull('instructor_reply'))
            ->when(($filters['reply'] ?? null) === 'unreplied', fn ($query) => $query->whereNull('instructor_reply'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $courses = Course::query()->orderBy('title')->get(['id', 'title']);
        $instructors = User::query()->where('role', 'instructor')->orderBy('name')->get(['id', 'name']);
        $statusOptions = collect(ReviewStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]);

        return view('admin.student-reviews.index', compact('reviews', 'courses', 'instructors', 'statusOptions', 'filters'));
    }

    public function show(Review $review): View
    {
        Gate::authorize('course_reviews.view');
        $review->load(['user', 'course.instructor', 'replier', 'moderator']);

        return view('admin.student-reviews.show', compact('review'));
    }

    public function approve(ModerateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('course_reviews.approve');
        $this->reviews->moderate($review, ReviewStatus::Approved, $request->user(), $request->validated('moderation_note'));

        return back()->with('success', 'Đã duyệt đánh giá.');
    }

    public function reject(ModerateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('course_reviews.reject');
        $this->reviews->moderate($review, ReviewStatus::Rejected, $request->user(), $request->validated('moderation_note'));

        return back()->with('success', 'Đã từ chối đánh giá.');
    }

    public function hide(ModerateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('course_reviews.hide');
        $this->reviews->moderate($review, ReviewStatus::Hidden, $request->user(), $request->validated('moderation_note'));

        return back()->with('success', 'Đã ẩn đánh giá.');
    }

    public function restore(ModerateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('course_reviews.approve');
        $this->reviews->moderate($review, ReviewStatus::Approved, $request->user(), $request->validated('moderation_note'));

        return back()->with('success', 'Đánh giá đã được hiển thị lại.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('deleteAsModerator', $review);
        $this->reviews->delete($review);

        return redirect()->route('admin.student-reviews.index')->with('success', 'Đã xóa đánh giá.');
    }
}
