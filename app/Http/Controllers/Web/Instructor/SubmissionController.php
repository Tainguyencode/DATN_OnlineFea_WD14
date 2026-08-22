<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use App\Models\Assignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller Quản lý & Chấm điểm bài tập dành cho Giảng viên (Instructor)
 * 
 * Chức năng chính:
 * 1. Hiển thị danh sách bài nộp của học viên theo bộ lọc (Khóa học, Bài tập, Trạng thái, Tìm kiếm tên/email).
 * 2. Xem chi tiết từng bài nộp (file đính kèm, câu trả lời, lịch sử chấm điểm).
 * 3. Chấm điểm bài tập, lưu lịch sử chấm, gửi thông báo Push/Email cho học viên và tự động kiểm tra hoàn thành khóa học.
 */
class SubmissionController extends Controller
{
    /**
     * Danh sách tất cả bài nộp bài tập của học viên trong các khóa học thuộc quản lý của Giảng viên.
     * 
     * @param Request $request Chứa các tham số lọc: course_id, assignment_id, status, search
     * @return View Giao diện danh sách bài nộp (instructor.submissions.index)
     */
    public function index(Request $request): View
    {
        $instructorId = $request->user()->id;

        // 1. Lấy danh sách các khóa học do Giảng viên này sở hữu để đổ vào bộ lọc Dropdown
        $courses = Course::query()
            ->where('instructor_id', $instructorId)
            ->orderBy('title')
            ->get(['id', 'title']);

        $courseId = $request->integer('course_id') ?: null;
        $assignmentId = $request->integer('assignment_id') ?: null;
        $status = $request->query('status');
        $search = trim((string) $request->query('search'));

        // 2. Lấy danh sách bài tập thuộc khóa học được chọn (nếu có) để lọc chi tiết
        $assignments = collect();
        if ($courseId) {
            $assignments = Assignment::query()
                ->whereHas('lesson', fn ($query) => $query->where('course_id', $courseId))
                ->get(['id', 'title']);
        }

        // 3. Truy vấn danh sách bài nộp có phân trang và áp dụng các điều kiện lọc
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

    /**
     * Xem chi tiết một bài nộp bài tập của học viên.
     * 
     * @param Submission $submission Model bài nộp cần xem
     * @param Request $request
     * @return View Giao diện xem bài nộp & form chấm điểm (instructor.submissions.show)
     */
    public function show(Submission $submission, Request $request): View
    {
        // Xác thực Giảng viên có quyền truy cập bài nộp này hay không
        $this->ensureOwned($submission, $request->user()->id);

        $submission->load(['user', 'assignment.lesson.course']);

        return view('instructor.submissions.show', compact('submission'));
    }

    /**
     * Chấm điểm bài tập & Phản hồi cho Học viên.
     * 
     * Quy trình xử lý:
     * 1. Validate điểm số (min: 0, max: điểm tối đa của bài tập) và phản hồi.
     * 2. Ghi nhận lịch sử chấm điểm (Grading History) vào mảng JSON.
     * 3. Cập nhật điểm số, trạng thái (graded / resubmit_required) và thời gian chấm.
     * 4. Gửi thông báo đến tài khoản Học viên.
     * 5. Kiểm tra tự động điều kiện hoàn thành khóa học (CourseCompletionService).
     * 
     * @param Request $request Chứa score, feedback, status
     * @param Submission $submission
     * @return RedirectResponse Quay lại trang chi tiết kèm thông báo thành công
     */
    public function grade(Request $request, Submission $submission): RedirectResponse
    {
        // 1. Phân quyền: Đảm bảo bài nộp thuộc khóa học của giảng viên hiện tại
        $this->ensureOwned($submission, $request->user()->id);

        // 2. Validate dữ liệu chấm điểm đầu vào
        $validated = $request->validate([
            'score' => [
                'required',
                'numeric',
                'min:0',
                'max:' . ($submission->assignment->max_score ?? 100),
            ],
            'feedback' => 'nullable|string|max:5000',
            'status' => 'required|string|in:graded,returned',
        ], [
            'score.required' => 'Vui lòng nhập điểm số.',
            'score.numeric' => 'Điểm số phải là một số.',
            'score.min' => 'Điểm số không được nhỏ hơn 0.',
            'score.max' => 'Điểm số không được vượt quá điểm tối đa của bài tập.',
            'status.required' => 'Vui lòng chọn trạng thái chấm điểm.',
        ]);

        $statusValue = $validated['status'];
        if ($statusValue === 'returned') {
            $statusValue = 'resubmit_required';
        }

        // 3. Ghi vết lịch sử chấm điểm (phục vụ xem lại các lần chấm lại)
        $history = $submission->grading_history ?? [];
        $history[] = [
            'score' => (float) $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => $statusValue,
            'graded_by' => $request->user()->name,
            'graded_at' => now()->toIso8601String(),
        ];

        // 4. Cập nhật thông tin bài nộp
        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => $statusValue,
            'graded_at' => now(),
            'graded_by' => $request->user()->id,
            'grading_history' => $history,
        ]);

        // 4.1 Cập nhật trạng thái hoàn thành bài giảng theo kết quả chấm điểm (Đạt >= passing_score)
        $passingScore = $submission->assignment->passing_score ?? 70;
        $isPassed = $statusValue === 'graded' && ((float) $validated['score']) >= $passingScore;
        
        app(\App\Services\LearningProgressService::class)->recordLessonProgress(
            $submission->user_id,
            $submission->assignment->lesson->course,
            $submission->assignment->lesson,
            0,
            0,
            false,
            $isPassed
        );
        // 5. Gửi thông báo đến Học viên
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

        // 6. Tự động kiểm tra điều kiện hoàn thành khóa học & cấp chứng chỉ nếu đủ điều kiện
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

    /**
     * Hàm trợ lý phân quyền: Đảm bảo Giảng viên chỉ thao tác trên bài nộp thuộc khóa học của chính mình.
     * 
     * @param Submission $submission Model bài nộp
     * @param int $instructorId ID của giảng viên hiện tại
     */
    private function ensureOwned(Submission $submission, int $instructorId): void
    {
        abort_unless(
            $submission->assignment->lesson->course->instructor_id === $instructorId,
            403,
            'Bạn không có quyền thực hiện hành động này.'
        );
    }
}
