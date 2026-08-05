<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\ApproveCourseRequest;
use App\Http\Requests\Course\RejectCourseRequest;
use App\Models\Course;
use App\Services\CourseReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseReviewController extends Controller
{
    public function __construct(
        private readonly CourseReviewService $reviewService,
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status', 'all_pending');

        $courses = Course::query()
            ->with(['instructor:id,name,email', 'category:id,name', 'courseSections.lessons', 'chapters.lessons'])
            ->withCount('courseReviews')
            ->when($status === 'all_pending', fn ($q) => $q->whereIn('status', [CourseStatus::PendingReview->value, CourseStatus::PendingUpdate->value]))
            ->when($status && $status !== 'all_pending', fn ($q) => $q->where('status', $status))
            ->orderByDesc('submitted_at')
            ->paginate(12)
            ->withQueryString();

        $statusOptions = collect([
            'all_pending' => 'Tất cả đang chờ duyệt (Mới & Cập nhật)',
        ])->merge(collect(CourseStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]));

        return view('admin.course-reviews.index', [
            'courses' => $courses,
            'status' => $status,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function show(Course $course): RedirectResponse
    {
        return redirect()->route('admin.courses.review', $course);
    }

    public function approve(ApproveCourseRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('approve', $course);

        $this->reviewService->approve(
            $course,
            $request->user(),
            $request->validated('checklist', []),
            $request->boolean('publish_immediately', true),
        );

        return back()->with('success', "Đã duyệt khóa học \"{$course->title}\".");
    }

    public function reject(RejectCourseRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('reject', $course);

        $this->reviewService->reject(
            $course,
            $request->user(),
            $request->validated('comment'),
            $request->validated('checklist', []),
        );

        return back()->with('success', 'Đã từ chối khóa học.');
    }
}
