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

        $statusValue = $validated['status'];
        if ($statusValue === 'returned') {
            $statusValue = 'resubmit_required';
        }

        // 1. Save to history
        $history = $submission->grading_history ?? [];
        $history[] = [
            'score' => (int) $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => $statusValue,
            'graded_by' => $request->user()->name,
            'graded_at' => now()->toIso8601String(),
        ];

        // 2. Update submission
        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => $statusValue,
            'graded_at' => now(),
            'graded_by' => $request->user()->id,
            'grading_history' => $history,
        ]);

        // 3. Send Notification to Student
        try {
            $student = $submission->user;
            $assignmentTitle = $submission->assignment->title;
            $course = $submission->assignment->lesson->course;
            
            $url = route('courses.lessons.show', [
                'course' => $course->id,
                'lesson' => $submission->assignment->lesson_id
            ]);

            $title = $statusValue === 'graded' 
                ? 'Bài tập của bạn đã được chấm điểm' 
                : 'Yêu cầu làm lại bài tập';

            $message = $statusValue === 'graded'
                ? "Bài tập \"{$assignmentTitle}\" trong khóa học \"{$course->title}\" đã đạt {$validated['score']}/{$submission->assignment->max_score} điểm."
                : "Giảng viên yêu cầu bạn làm lại bài tập \"{$assignmentTitle}\" trong khóa học \"{$course->title}\".";

            app(\App\Services\NotificationService::class)->send(
                $student,
                $title,
                $message,
                'assignment_graded',
                $url
            );
        } catch (\Exception $e) {
            logger()->error('Failed to send assignment grading notification: ' . $e->getMessage());
        }

        // 4. Check course completion
        try {
            $enrollment = \App\Models\Enrollment::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->first();
            if ($enrollment) {
                app(\App\Services\CourseCompletionService::class)->check($enrollment, $student->id);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to check course completion after grading: ' . $e->getMessage());
        }

        return redirect()
            ->route('instructor.submissions.show', $submission)
            ->with('success', 'Đã chấm điểm, ghi nhận lịch sử, kiểm tra điều kiện hoàn thành khóa học và gửi thông báo kết quả cho học viên.');
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
