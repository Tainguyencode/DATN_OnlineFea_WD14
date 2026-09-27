<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Submission;
use App\Services\LearningProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Controller Xử lý Nộp bài tập dành cho Học viên (Student)
 *
 * Chức năng chính:
 * 1. Đăng tải bài làm tự luận (Text phản hồi hoặc File bài làm đính kèm: PDF, ZIP, RAR, DOCX, Ảnh).
 * 2. Cập nhật tiến độ bài học (Lesson Progress) khi nộp bài thành công.
 * 3. Hỗ trợ nộp lại bài khi Giảng viên gửi yêu cầu làm lại (status = resubmit_required).
 */
class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $assignments = Assignment::query()
            ->whereHas('course.enrollments', fn ($query) => $query
                ->where('user_id', $request->user()->id)
                ->whereIn('status', ['active', 'completed']))
            ->with([
                'course:id,title,slug',
                'lesson:id,course_id,title',
                'submissions' => fn ($query) => $query->where('user_id', $request->user()->id),
            ])
            ->when($request->filled('status'), function ($query) use ($request): void {
                $status = $request->string('status')->toString();
                if ($status === 'not_submitted') {
                    $query->whereDoesntHave('submissions', fn ($submissions) => $submissions->where('user_id', $request->user()->id));
                } else {
                    $query->whereHas('submissions', fn ($submissions) => $submissions
                        ->where('user_id', $request->user()->id)
                        ->where('status', $status));
                }
            })
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->paginate(15)
            ->withQueryString();

        return view('student.assignments.index', compact('assignments'));
    }

    /**
     * Tải tài liệu bài tập về máy và kích hoạt thời gian làm bài (6 giờ) cho lần làm hiện tại.
     *
     * @return mixed
     */
    public function download(Request $request, Course $course, Lesson $lesson)
    {
        $this->assertAssignmentBelongsToCourse($course, $lesson);
        // 1. Kiểm tra học viên đã ghi danh vào khóa học chưa
        $isEnrolled = $course->enrollments()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'completed'])
            ->exists();

        abort_unless($isEnrolled, 403, 'Bạn cần đăng ký khóa học để tải tài liệu bài tập.');

        // 2. Đảm bảo có bản ghi Assignment
        $assignment = $lesson->assignment;
        abort_unless($assignment && (int) $assignment->course_id === (int) $course->id, 404);

        // 3. Lấy attempt mới nhất của học viên hoặc tạo attempt 1
        $submission = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('user_id', $request->user()->id)
            ->orderBy('attempt_number', 'desc')
            ->first();

        if (! $submission) {
            $submission = Submission::create([
                'assignment_id' => $assignment->id,
                'assignment_version_id' => $assignment->published_version_id,
                'user_id' => $request->user()->id,
                'attempt_number' => 1,
                'allowed_attempts' => 2,
                'started_at' => now(), // Bắt đầu tính 6 giờ cho lần 1
                'status' => 'in_progress',
            ]);
        } elseif (! $submission->started_at) {
            // Trường hợp học viên bấm "Làm lại" hoặc Giảng viên cấp lượt mới (started_at đang là null)
            // Timer CHÍNH THỨC bắt đầu khi bấm "Tải tài liệu về"
            $submission->update([
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        // 4. Trả về file tải nếu có
        if ($lesson->document_file && Storage::disk('public')->exists($lesson->document_file)) {
            $ext = strtolower(pathinfo($lesson->document_file, PATHINFO_EXTENSION));
            $downloadName = Str::slug($lesson->title ?: 'bai-tap-thuc-hanh').($ext ? '.'.$ext : '');

            return Storage::disk('public')->download($lesson->document_file, $downloadName);
        }

        return back()->with('success', 'Đã bắt đầu tính thời gian làm bài (6 giờ). Hãy tiến hành làm và nộp bài trước hạn!');
    }

    /**
     * Bắt đầu lần làm lại bài tập thực hành (Retake).
     *
     * Quy tắc quan trọng:
     * 1. Bấm "Làm lại" KHÔNG bắt đầu timer ngay.
     * 2. Timer chỉ bắt đầu khi học viên bấm "Tải tài liệu về" của lần làm mới này.
     * 3. Sử dụng allowed_attempts để kiểm tra số lượt được phép.
     */
    public function retry(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->assertAssignmentBelongsToCourse($course, $lesson);
        $isEnrolled = $course->enrollments()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'completed'])
            ->exists();

        abort_unless($isEnrolled, 403, 'Bạn cần đăng ký khóa học để thực hiện hành động này.');

        $assignment = $lesson->assignment;
        abort_unless($assignment && (int) $assignment->course_id === (int) $course->id, 404, 'Không tìm thấy bài tập thực hành.');

        $latestSubmission = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('user_id', $request->user()->id)
            ->orderBy('attempt_number', 'desc')
            ->first();

        if (! $latestSubmission) {
            return back()->with('error', 'Bạn chưa làm bài tập này lần nào.');
        }

        if ($latestSubmission->isPassed()) {
            return back()->with('error', 'Bài tập của bạn đã ĐẠT (PASS), không thể làm lại.');
        }

        $allowedAttempts = $latestSubmission->allowed_attempts ?? 2;

        if ($latestSubmission->attempt_number >= $allowedAttempts) {
            return back()->with('error', "Bạn đã sử dụng hết lượt làm bài ({$allowedAttempts}/{$allowedAttempts} lần). Vui lòng liên hệ giảng viên nếu cần được cấp thêm lượt.");
        }

        // Kiểm tra xem lần làm hiện tại đã kết thúc chưa (đã được chấm FAIL hoặc đã EXPIRED)
        $isFinished = ($latestSubmission->status === 'graded' && $latestSubmission->result === 'fail') || $latestSubmission->isExpired();

        if (! $isFinished && ! $latestSubmission->submitted_at) {
            return back()->with('error', 'Lần làm bài hiện tại vẫn đang diễn ra.');
        }

        if ($latestSubmission->status === 'submitted') {
            return back()->with('error', 'Bài làm của bạn đang chờ giảng viên chấm điểm.');
        }

        // Tạo bản ghi attempt mới độc lập (started_at = null -> Timer CHƯA bắt đầu)
        $nextAttemptNumber = $latestSubmission->attempt_number + 1;

        Submission::create([
            'assignment_id' => $assignment->id,
            'assignment_version_id' => $assignment->published_version_id,
            'user_id' => $request->user()->id,
            'attempt_number' => $nextAttemptNumber,
            'allowed_attempts' => $allowedAttempts,
            'started_at' => null, // Timer KHÔNG bắt đầu khi chỉ mới bấm "Làm lại"
            'status' => 'in_progress',
            'result' => null,
        ]);

        return back()->with('success', "Đã tạo lần làm lại thứ {$nextAttemptNumber}/{$allowedAttempts}. Vui lòng bấm \"Tải tài liệu về\" để bắt đầu tính 6 giờ làm bài.");
    }

    /**
     * Xử lý nộp bài tập thực hành cho attempt hiện tại.
     *
     * @param  Request  $request  Chứa 'content' (văn bản) hoặc 'file' (tệp đính kèm)
     * @param  Course  $course  Khóa học hiện tại
     * @param  Lesson  $lesson  Bài học hiện tại
     * @return RedirectResponse Quay lại trang bài học kèm thông báo kết quả
     */
    public function submit(Request $request, Course $course, Lesson $lesson, LearningProgressService $progressService): RedirectResponse
    {
        abort_unless((int) $lesson->course_id === (int) $course->id
            || $lesson->section()->where('course_id', $course->id)->exists()
            || $lesson->chapter()->where('course_id', $course->id)->exists(), 404);
        abort_unless($lesson->type === Lesson::TYPE_ASSIGNMENT, 404);

        // 1. Kiểm tra xem học viên đã ghi danh vào khóa học này chưa
        $isEnrolled = $course->enrollments()
            ->where('user_id', $request->user()->id)
            ->withLearningAccess()
            ->exists();

        abort_unless($isEnrolled, 403, 'Bạn cần đăng ký khóa học để thực hiện hành động này.');

        // Assignment phải do giảng viên cấu hình; request học viên không được tự sinh dữ liệu khóa học.
        $assignment = $lesson->assignment;
        abort_unless($assignment && (int) $assignment->course_id === (int) $course->id, 404, 'Không tìm thấy bài tập thực hành.');

        // 3. Lấy attempt mới nhất hiện tại của học viên
        $submission = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('user_id', $request->user()->id)
            ->orderBy('attempt_number', 'desc')
            ->first();

        // 3.1 Bắt buộc phải bấm tải tài liệu trước để bắt đầu làm bài
        if (! $submission || ! $submission->started_at) {
            return back()->with('error', 'Bạn cần bấm "Tải tài liệu về" để bắt đầu thời gian làm bài trước khi nộp bài.');
        }

        // A replay must never mutate a submitted or graded attempt, even after its deadline.
        if (in_array($submission->status, ['submitted', 'graded'], true)) {
            return back()->with('error', 'Bạn đã nộp bài cho lần làm này rồi.');
        }

        // 3.2 Kiểm tra deadline 6 giờ kể từ thời điểm bắt đầu tải tài liệu của lần làm này
        $deadline = $submission->getDeadline();
        if (($deadline && now()->gt($deadline)) || $submission->status === 'expired') {
            Submission::whereKey($submission->id)->whereNotIn('status', ['submitted', 'graded'])->update([
                'status' => 'expired',
                'result' => 'fail',
            ]);

            return back()->with('error', 'Đã hết thời gian làm bài (quá 6 giờ). Bạn không thể nộp bài và lần làm này được tính là FAIL.');
        }

        // 4. Validate dữ liệu đầu vào: bắt buộc phải có 1 trong 2 (Nội dung văn bản HOẶC File đính kèm)
        $allowedTypes = collect(explode(',', (string) ($assignment->allowed_file_types ?: 'pdf,zip,rar,doc,docx,png,jpg,jpeg')))
            ->map(fn (string $type) => strtolower(trim($type)))
            ->filter(fn (string $type) => in_array($type, ['pdf', 'zip', 'rar', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'txt', 'xls', 'xlsx', 'ppt', 'pptx'], true))
            ->unique()
            ->values();
        abort_if($allowedTypes->isEmpty(), 422, 'Bài tập chưa cấu hình định dạng tệp hợp lệ.');
        $maximumFileSize = max(1, min(102400, (int) ($assignment->maximum_file_size ?: 10240)));

        $validated = $request->validate([
            'content' => ['required_without:file', 'nullable', 'string', 'max:5000'],
            'code_language' => ['nullable', 'string', Rule::in(['plaintext', 'php', 'javascript', 'typescript', 'python', 'java', 'c', 'cpp', 'csharp', 'html', 'css', 'sql', 'json', 'bash'])],
            'file' => ['required_without:content', 'nullable', 'file', 'mimes:'.$allowedTypes->implode(','), 'max:'.$maximumFileSize],
        ], [
            'content.required_without' => 'Vui lòng nhập nội dung câu trả lời hoặc tải lên file bài làm.',
            'file.required_without' => 'Vui lòng tải lên file bài làm hoặc nhập nội dung câu trả lời.',
            'file.mimes' => 'File đính kèm chỉ chấp nhận các định dạng: '.$allowedTypes->map(fn ($type) => strtoupper($type))->implode(', ').'.',
            'file.max' => 'Dung lượng file tải lên không được vượt quá '.round($maximumFileSize / 1024, 1).'MB.',
        ]);

        // 5. Quản lý lưu trữ file bài làm (Lưu riêng từng attempt, không ghi đè file các lần trước)
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('assignment-submissions', 'public');
        } elseif ($submission) {
            $filePath = $submission->file_path;
        }

        // 6. Lưu hoặc Cập nhật Bài nộp vào CSDL cho attempt hiện tại
        $saved = Submission::whereKey($submission->id)
            ->whereNotIn('status', ['submitted', 'graded', 'expired'])->update([
            'file_path' => $filePath,
            'content' => $request->input('content'),
            'code_language' => $validated['code_language'] ?? 'plaintext',
            'status' => 'submitted',
            'submitted_at' => now(),
            'result' => null,
            'score' => null,
            'feedback' => null,
            'graded_at' => null,
        ]);
        if (! $saved) {
            if ($request->hasFile('file') && $filePath) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->with('error', 'Bài làm đã được xử lý bởi một yêu cầu khác. Vui lòng tải lại trang.');
        }
        $submission->refresh();

        // 7. Cập nhật tiến độ hoàn thành bài học (chưa đạt cho tới khi giảng viên chấm PASS)
        $progressService->recordLessonProgress(
            $request->user()->id,
            $course,
            $lesson,
            0,
            0,
            false,
            false
        );

        return back()->with('success', "Đã nộp bài tập thực hành (Lần {$submission->attempt_number}) thành công! Vui lòng chờ giảng viên đánh giá.");
    }

    private function assertAssignmentBelongsToCourse(Course $course, Lesson $lesson): void
    {
        abort_unless((int) $lesson->course_id === (int) $course->id
            || $lesson->section()->where('course_id', $course->id)->exists()
            || $lesson->chapter()->where('course_id', $course->id)->exists(), 404);
        abort_unless($lesson->type === Lesson::TYPE_ASSIGNMENT, 404);
    }
}
