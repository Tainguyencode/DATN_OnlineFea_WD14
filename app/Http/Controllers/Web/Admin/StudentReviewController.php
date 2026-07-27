<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ModerateReviewRequest;
use App\Models\Course;
use App\Models\Review;
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
        $filters = $this->filters($request);

        $reviews = Review::query()
            ->whereNull('parent_id')
            ->whereIn('status', [ReviewStatus::Visible->value, ReviewStatus::Hidden->value])
            ->with(['user:id,name,email,avatar', 'course:id,instructor_id,title,slug', 'course.instructor:id,name', 'replies.user:id,name', 'moderator:id,name'])
            ->when($filters['keyword'] ?? null, function ($query, string $keyword) {
                $query->where(function ($nested) use ($keyword) {
                    $nested->where('comment', 'like', "%{$keyword}%")
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$keyword}%"))
                        ->orWhereHas('course', fn ($course) => $course->where('title', 'like', "%{$keyword}%"));
                });
            })
            ->when($filters['course_id'] ?? null, fn ($query, $id) => $query->where('course_id', $id))
            ->when(($filters['status'] ?? null) === ReviewStatus::Visible->value, fn ($query) => $query->visible())
            ->when(($filters['status'] ?? null) === ReviewStatus::Hidden->value, fn ($query) => $query->hidden())
            ->latest()
            ->paginate(15)
            ->appends($filters);

        $courses = Course::query()->orderBy('title')->get(['id', 'title']);
        $statusOptions = collect(ReviewStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]);

        return view('admin.student-reviews.index', compact('reviews', 'courses', 'statusOptions', 'filters'));
    }

    public function show(Review $review): View
    {
        Gate::authorize('course_reviews.view');
        $review->load(['user', 'course.instructor', 'replies.user', 'moderator']);

        return view('admin.student-reviews.show', compact('review'));
    }

    public function hide(ModerateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('course_reviews.moderate');
        $this->reviews->hide($review, $request->user(), $request->validated('moderation_note'));

        return back()->with('success', 'Đã ẩn đánh giá.');
    }

    public function restore(ModerateReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('course_reviews.moderate');
        $this->reviews->restore($review, $request->user(), $request->validated('moderation_note'));

        return back()->with('success', 'Đánh giá đã được hiển thị lại.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('deleteAsModerator', $review);
        $this->reviews->delete($review);

        return redirect()->route('admin.student-reviews.index')->with('success', 'Đã xóa đánh giá.');
    }

    private function filters(Request $request): array
    {
        $filters = [];
        $keyword = trim((string) $request->query('keyword', ''));
        $courseId = $request->integer('course_id');
        $status = (string) $request->query('status', '');

        if ($keyword !== '') {
            $filters['keyword'] = $keyword;
        }

        if ($courseId > 0) {
            $filters['course_id'] = $courseId;
        }

        if (in_array($status, [ReviewStatus::Visible->value, ReviewStatus::Hidden->value], true)) {
            $filters['status'] = $status;
        }

        return $filters;
    }
}
