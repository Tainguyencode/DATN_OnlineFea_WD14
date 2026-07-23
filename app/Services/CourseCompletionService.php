<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\PushNotification;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Notifications\CertificateIssuedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CourseCompletionService
{
    public function check(Enrollment $enrollment, int $userId): array
    {
        $course = $enrollment->course()->with(['lessons.quiz', 'lessons.assignment'])->first();
        $missing = [];

        $allLessons = Lesson::query()
            ->where(function ($query) use ($course) {
                $query->where('course_id', $course->id)
                    ->orWhereHas('section', fn ($q) => $q->where('course_id', $course->id))
                    ->orWhereHas('chapter', fn ($q) => $q->where('course_id', $course->id));
            })
            ->get();

        // 1. Xem 100% video
        $videoLessons = $allLessons->where('type', 'video');
        foreach ($videoLessons as $lesson) {
            $progress = LessonProgress::where('user_id', $userId)->where('lesson_id', $lesson->id)->first();
            if (! $progress || ! $progress->is_completed) {
                $missing[] = "Video \"{$lesson->title}\" chưa hoàn thành.";
            }
        }

        // 2. Hoàn thành 100% quiz
        $quizLessons = $allLessons->where('type', 'quiz');
        foreach ($quizLessons as $lesson) {
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
                $missing[] = "Bài trắc nghiệm \"{$lesson->title}\" chưa đạt điểm yêu cầu.";
            }
        }

        $eligible = $missing === [] && $enrollment->hasLearningAccess();
        $completedAt = $enrollment->completed_at;

        if ($eligible) {
            if (! $completedAt) {
                $completedAt = now();
                $enrollment->update([
                    'completed_at' => $completedAt,
                    'status' => Enrollment::STATUS_COMPLETED,
                ]);

                $certificate = $this->issueCertificate($userId, $course);

                PushNotification::create([
                    'user_id' => $userId,
                    'title' => 'Hoàn thành khóa học',
                    'message' => "Chúc mừng! Bạn đã hoàn thành khóa học \"{$course->title}\".",
                    'type' => $certificate ? 'certificate_issued' : 'course_completed',
                    'url' => $certificate
                        ? route('student.certificates')
                        : route('student.dashboard'),
                    'is_read' => false,
                ]);
            } elseif ($enrollment->status !== Enrollment::STATUS_COMPLETED) {
                $enrollment->update([
                    'status' => Enrollment::STATUS_COMPLETED,
                ]);
            }
        }

        return [
            'eligible' => $eligible,
            'progress_percent' => (float) $enrollment->progress_percent,
            'missing_requirements' => $missing,
            'completed_at' => $completedAt?->toIso8601String(),
        ];
    }

    private function issueCertificate(int $userId, Course $course): ?Certificate
    {
        if (! $course->certificate_enabled) {
            return null;
        }

        $certificate = Certificate::firstOrCreate(
            ['user_id' => $userId, 'course_id' => $course->id],
            [
                'certificate_code' => 'FEA-'.strtoupper(Str::random(8)),
                'issued_at' => now(),
            ]
        );

        $wasRecentlyCreated = $certificate->wasRecentlyCreated;

        app(CertificatePdfService::class)->ensureStored($certificate);
        $certificate->refresh();

        if ($wasRecentlyCreated) {
            $this->sendCertificateEmail($userId, $course, $certificate);
        }

        return $certificate;
    }

    private function sendCertificateEmail(int $userId, Course $course, Certificate $certificate): void
    {
        try {
            $user = User::find($userId);
            if (! $user) {
                return;
            }

            $user->notify(new CertificateIssuedNotification($course, $certificate));
        } catch (Throwable $exception) {
            Log::warning('Certificate email failed; certificate remains issued.', [
                'user_id' => $userId,
                'course_id' => $course->id,
                'certificate_id' => $certificate->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
