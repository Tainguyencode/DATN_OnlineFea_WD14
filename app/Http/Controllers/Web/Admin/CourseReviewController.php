<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\ApproveCourseRequest;
use App\Http\Requests\Course\RejectCourseRequest;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
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
        $status = $request->query('status', CourseStatus::PendingReview->value);

        $courses = Course::query()
            ->with(['instructor:id,name,email', 'category:id,name', 'courseSections.lessons', 'chapters.lessons'])
            ->withCount('courseReviews')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('submitted_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.course-reviews.index', [
            'courses' => $courses,
            'status' => $status,
            'statusOptions' => collect(CourseStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]),
        ]);
    }

    public function show(Course $course): View
    {
        $course->load([
            'instructor:id,name,email,avatar,bio',
            'category:id,name',
            'courseSections.lessons.quiz.questions',
            'courseSections.lessons.assignment',
            'chapters.lessons.quiz.questions',
            'chapters.lessons.assignment',
            'courseReviews.reviewer:id,name',
        ]);

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;

        $totalLessons = $curriculumSections->sum(fn ($s) => $s->lessons->count());
        $totalDuration = $curriculumSections->flatMap->lessons->sum(fn ($l) => (int) ($l->duration_seconds ?? 0));

        return view('admin.course-reviews.show', [
            'course' => $course,
            'curriculumSections' => $curriculumSections,
            'totalLessons' => $totalLessons,
            'totalDuration' => $totalDuration,
            'reviewHistory' => $course->courseReviews,
            'checklistItems' => config('course.admin_review_checklist'),
        ]);
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
