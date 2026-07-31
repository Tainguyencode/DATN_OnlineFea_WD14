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

class AssignmentController extends Controller
{
    public function submit(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        // 1. Check enrollment
        $isEnrolled = $course->enrollments()
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'completed'])
            ->exists();

        abort_unless($isEnrolled, 403, 'Bạn cần đăng ký khóa học để thực hiện hành động này.');

        // 2. Check assignment exists
        $assignment = $lesson->assignment;
        abort_unless($assignment, 404, 'Bài học này không có bài tập tự luận.');

        // 3. Check existing submission
        $submission = Submission::query()
            ->where('assignment_id', $assignment->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($submission && in_array($submission->status, ['submitted', 'graded'])) {
            return back()->with('error', 'Bạn đã nộp bài tập này rồi và bài làm đang được chấm hoặc đã chấm.');
        }

        // 4. Validate
        $request->validate([
            'content' => 'required_without:file|nullable|string|max:5000',
            'file' => 'required_without:content|nullable|file|mimes:pdf,zip,rar,doc,docx,png,jpg,jpeg|max:10240', // max 10MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($submission && $submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }
            $filePath = $request->file('file')->store('assignment-submissions', 'public');
        } elseif ($submission) {
            $filePath = $submission->file_path; // Keep old file if only text updated
        }

        // 5. Save submission
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

        // 6. Mark lesson progress as completed
        LessonProgress::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
                'last_watched_at' => now(),
            ]
        );

        return back()->with('success', 'Đã nộp bài tập tự luận thành công!');
    }
}
