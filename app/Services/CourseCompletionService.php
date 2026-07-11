<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\PushNotification;
use App\Models\QuizAttempt;
use App\Models\AssignmentSubmission;
use Illuminate\Support\Str;

class CourseCompletionService
{
    public function check(Enrollment $enrollment, int $userId): array
    {
        $course = $enrollment->course()->with(['lessons.quiz', 'lessons.assignment'])->first();
        $missing = [];

        $requiredPercent = $course->requiredLessonPercent();
        if ((float) $enrollment->progress_percent < $requiredPercent) {
            $missing[] = "Hoàn thành ít nhất {$requiredPercent}% bài học bắt buộc.";
        }

        $requiredLessons = Lesson::query()
            ->where(function ($query) use ($course) {
                $query->where('course_id', $course->id)
                    ->orWhereHas('section', fn ($q) => $q->where('course_id', $course->id))
                    ->orWhereHas('chapter', fn ($q) => $q->where('course_id', $course->id));
            })
            ->where('is_required', true)
            ->get();

        foreach ($requiredLessons->where('type', 'video') as $lesson) {
            $progress = LessonProgress::where('user_id', $userId)->where('lesson_id', $lesson->id)->first();
            $threshold = $course->requiredVideoPercent();
            if (! $progress || (float) $progress->progress_percent < $threshold) {
                $missing[] = "Video \"{$lesson->title}\" chưa đạt {$threshold}%.";
            }
        }

        if ($course->require_all_quizzes) {
            foreach ($requiredLessons->where('type', 'quiz') as $lesson) {
                $quiz = $lesson->quiz;
                if (! $quiz) {
                    continue;
                }
                $passed = QuizAttempt::query()
                    ->where('user_id', $userId)
                    ->where('quiz_id', $quiz->id)
                    ->where('passed', true)
                    ->exists();
                if (! $passed) {
                    $missing[] = "Quiz \"{$lesson->title}\" chưa đạt điểm yêu cầu.";
                }
            }
        }

        if ($course->require_all_assignments) {
            foreach ($requiredLessons->where('type', 'assignment') as $lesson) {
                $assignment = $lesson->assignment;
                if (! $assignment || ! $assignment->is_required) {
                    continue;
                }
                $submission = AssignmentSubmission::query()
                    ->where('user_id', $userId)
                    ->where('assignment_id', $assignment->id)
                    ->latest()
                    ->first();
                if (! $submission || $submission->status === 'draft') {
                    $missing[] = "Bài tập \"{$lesson->title}\" chưa nộp.";
                } elseif ($submission->status === 'graded' && ! $submission->isPassing()) {
                    $missing[] = "Bài tập \"{$lesson->title}\" chưa đạt điểm.";
                }
            }
        }

        $eligible = $missing === [] && $enrollment->hasLearningAccess();
        $completedAt = $enrollment->completed_at;

        if ($eligible && ! $completedAt) {
            $completedAt = now();
            $enrollment->update([
                'completed_at' => $completedAt,
            ]);

            if ($course->certificate_enabled) {
                $this->issueCertificate($userId, $course);
            }

            PushNotification::create([
                'user_id' => $userId,
                'title' => 'Hoàn thành khóa học',
                'message' => "Chúc mừng! Bạn đã hoàn thành khóa học \"{$course->title}\".",
                'type' => 'certificate_issued',
                'url' => route('student.certificates'),
                'is_read' => false,
            ]);
        }

        return [
            'eligible' => $eligible,
            'progress_percent' => (float) $enrollment->progress_percent,
            'missing_requirements' => $missing,
            'completed_at' => $completedAt?->toIso8601String(),
        ];
    }

    private function issueCertificate(int $userId, Course $course): void
    {
        Certificate::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $course->id],
            [
                'certificate_code' => 'FEA-'.strtoupper(Str::random(8)),
                'issued_at' => now(),
            ]
        );
    }
}
