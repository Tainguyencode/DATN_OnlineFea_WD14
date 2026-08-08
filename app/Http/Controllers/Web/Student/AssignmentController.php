<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Submission;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    /**
     * Xử lý nộp bài tập tự luận cho bài học.
     * 
     * Quy trình xử lý:
     * 1. Kiểm tra học viên đã đăng ký khóa học (Enrollment active/completed) chưa.
     * 2. Kiểm tra bài học có gắn Assignment tự luận hay không.
     * 3. Kiểm tra xem học viên đã nộp chưa (tránh nộp đè bài đã được chấm).
     * 4. Validate file upload (mimes: pdf, zip, rar, docx, png, jpg; max: 10MB) hoặc văn bản câu trả lời.
     * 5. Lưu file vào storage/app/public/assignment-submissions và cập nhật DB (updateOrCreate).
     * 6. Đánh dấu bài học này là Hoàn thành (is_completed = true).
     * 
     * @param Request $request Chứa 'content' (văn bản) hoặc 'file' (tệp đính kèm)
     * @param Course $course Khóa học hiện tại
     * @param Lesson $lesson Bài học hiện tại
     * @return RedirectResponse Quay lại trang bài học kèm thông báo kết quả
     */
    public function submit(Request $request, Course $course, Lesson $lesson, \App\Services\LearningProgressService $progressService): RedirectResponse
    {
        // 1. Kiểm tra xem học viên đã ghi danh vào khóa học này chưa
        $isEnrolled = $course->enrollments()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'completed'])
            ->exists();

        abort_unless($isEnrolled, 403, 'Bạn cần đăng ký khóa học để thực hiện hành động này.');

        // 2. Đảm bảo bài học hiện tại có bài tập tự luận (tự động tạo nếu thiếu để tránh lỗi 404)
        $assignment = $lesson->assignment;
        if (!$assignment) {
            $assignment = \App\Models\Assignment::create([
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
                'title' => $lesson->title ?? 'Bài tập thực hành',
                'description' => 'Hãy thực hiện yêu cầu của bài tập tự luận dưới đây.',
                'max_score' => 100,
            ]);
        }

        // 3. Kiểm tra trạng thái bài nộp hiện tại (nếu đang chờ chấm hoặc đã chấm thành công thì không cho nộp đè)
        $submission = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($submission && in_array($submission->status, ['submitted', 'graded'])) {
            return back()->with('error', 'Bạn đã nộp bài tập này rồi và bài làm đang được chấm hoặc đã chấm.');
        }

        // 4. Validate dữ liệu đầu vào: bắt buộc phải có 1 trong 2 (Nội dung văn bản HOẶC File đính kèm)
        $request->validate([
            'content' => 'required_without:file|nullable|string|max:5000',
            'file' => 'required_without:content|nullable|file|mimes:pdf,zip,rar,doc,docx,png,jpg,jpeg|max:10240', // Tối đa 10MB
        ], [
            'content.required_without' => 'Vui lòng nhập nội dung câu trả lời hoặc tải lên file bài làm.',
            'file.required_without' => 'Vui lòng tải lên file bài làm hoặc nhập nội dung câu trả lời.',
            'file.mimes' => 'File đính kèm chỉ chấp nhận các định dạng: PDF, ZIP, RAR, DOC, DOCX, PNG, JPG, JPEG.',
            'file.max' => 'Dung lượng file tải lên không được vượt quá 10MB.',
        ]);

        // 5. Quản lý lưu trữ file bài làm
        $filePath = null;
        if ($request->hasFile('file')) {
            // Xóa file cũ trong bộ nhớ nếu học viên nộp lại
            if ($submission && $submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $filePath = $request->file('file')->store('assignment-submissions', 'public');
        } elseif ($submission) {
            $filePath = $submission->file_path; // Giữ lại file cũ nếu chỉ cập nhật văn bản
        }

        // 6. Lưu hoặc Cập nhật Bài nộp vào CSDL
        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $request->user()->id,
            ],
            [
                'file_path' => $filePath,
                'content' => $request->input('content'),
                'status' => 'submitted',
                'submitted_at' => now(),
                'score' => null,
                'feedback' => null,
                'graded_at' => null,
            ]
        );

        // 7. Cập nhật tiến độ hoàn thành bài học và đồng bộ tiến độ khóa học (chưa hoàn thành cho tới khi được chấm đạt)
        $progressService->recordLessonProgress(
            $request->user()->id,
            $course,
            $lesson,
            0,
            0,
            false,
            false
        );

        return back()->with('success', 'Đã nộp bài tập tự luận thành công!');
    }
}
