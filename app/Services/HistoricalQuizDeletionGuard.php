<?php

namespace App\Services;

use App\Exceptions\HistoricalQuizDeletionException;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;

class HistoricalQuizDeletionGuard
{
    public const MESSAGE = 'Không thể xóa nội dung này vì đã có lịch sử làm bài của học viên.';

    public function hasHistoricalAttemptsForLesson(Lesson|int $lesson): bool
    {
        return QuizAttempt::query()->whereHas('quiz', fn ($query) => $query->where('lesson_id', $this->id($lesson)))->exists();
    }

    public function hasHistoricalAttemptsForSection(CourseSection|int $section): bool
    {
        return QuizAttempt::query()->whereHas('quiz.lesson', fn ($query) => $query->where('section_id', $this->id($section)))->exists();
    }

    public function hasHistoricalAttemptsForCourse(Course|int $course): bool
    {
        return QuizAttempt::query()->whereHas('quiz.lesson', fn ($query) => $query->where('course_id', $this->id($course)))->exists();
    }

    public function assertLessonCanBeHardDeleted(Lesson|int $lesson): void
    {
        if ($this->hasHistoricalAttemptsForLesson($lesson)) {
            throw new HistoricalQuizDeletionException(self::MESSAGE);
        }
    }

    public function assertSectionCanBeHardDeleted(CourseSection|int $section): void
    {
        if ($this->hasHistoricalAttemptsForSection($section)) {
            throw new HistoricalQuizDeletionException(self::MESSAGE);
        }
    }

    public function assertCourseCanBeHardDeleted(Course|int $course): void
    {
        if ($this->hasHistoricalAttemptsForCourse($course)) {
            throw new HistoricalQuizDeletionException(self::MESSAGE);
        }
    }

    public function assertQuizCanBeHardDeleted(Quiz $quiz): void
    {
        if ($quiz->attempts()->exists() || $quiz->versions()->whereIn('status', ['published', 'superseded'])->exists()) {
            throw new HistoricalQuizDeletionException(self::MESSAGE);
        }
    }

    public function assertQuestionCanBeHardDeleted(QuizQuestion $question): void
    {
        if ($question->attemptAnswers()->exists() || $question->versions()->where('status', 'published')->exists()) {
            throw new HistoricalQuizDeletionException(self::MESSAGE);
        }
    }

    public function assertOptionCanBeHardDeleted(QuizOption $option): void
    {
        if ($option->attemptAnswers()->exists()) {
            throw new HistoricalQuizDeletionException(self::MESSAGE);
        }
    }

    private function id(object|int $model): int
    {
        return is_int($model) ? $model : (int) $model->getKey();
    }
}
