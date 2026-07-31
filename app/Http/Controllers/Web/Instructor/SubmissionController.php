<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $instructorId = $request->user()->id;

        // Get courses owned by the instructor
        $courses = Course::query()
            ->where('instructor_id', $instructorId)
            ->orderBy('title')
            ->get(['id', 'title']);

        $courseId = $request->integer('course_id') ?: null;
        $assignmentId = $request->integer('assignment_id') ?: null;
        $status = $request->query('status');
        $search = trim((string) $request->query('search'));

        // Query assignments for the course filter dropdown
        $assignments = collect();
        if ($courseId) {
            $assignments = Assignment::query()
                ->whereHas('lesson', fn ($query) => $query->where('course_id', $courseId))
                ->get(['id', 'title']);
        }

        $submissions = Submission::query()
            ->whereHas('assignment.lesson.course', fn ($query) => $query->where('instructor_id', $instructorId))
            ->with(['user:id,name,email,avatar', 'assignment.lesson.course:id,title,slug'])
            ->when($courseId, function ($query) use ($courseId) {
                $query->whereHas('assignment.lesson', fn ($q) => $q->where('course_id', $courseId));
            })
            ->when($assignmentId, fn ($query) => $query->where('assignment_id', $assignmentId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('instructor.submissions.index', compact(
            'submissions',
            'courses',
            'assignments',
            'courseId',
            'assignmentId',
            'status',
            'search'
        ));
    }

    public function show(Submission $submission, Request $request): View
    {
        $this->ensureOwned($submission, $request->user()->id);

        $submission->load(['user', 'assignment.lesson.course']);

        return view('instructor.submissions.show', compact('submission'));
    }

    public function grade(Request $request, Submission $submission): RedirectResponse
    {
        $this->ensureOwned($submission, $request->user()->id);

        $validated = $request->validate([
            'score' => [
                'required',
                'integer',
                'min:0',
                'max:' . ($submission->assignment->max_score ?? 100),
            ],
            'feedback' => 'nullable|string|max:5000',
            'status' => 'required|string|in:graded,returned',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => $validated['status'],
            'graded_at' => $validated['status'] === 'graded' ? now() : null,
        ]);

        return redirect()
            ->route('instructor.submissions.show', $submission)
            ->with('success', 'Đã chấm điểm và phản hồi bài tập thành công.');
    }

    private function ensureOwned(Submission $submission, int $instructorId): void
    {
        abort_unless(
            $submission->assignment->lesson->course->instructor_id === $instructorId,
            403,
            'Bạn không có quyền thực hiện hành động này.'
        );
    }
}
