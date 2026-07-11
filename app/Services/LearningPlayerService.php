<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Collection;

class LearningPlayerService
{
    public function buildPlayerContext(
        Course $course,
        Lesson $lesson,
        ?User $user,
        bool $canBypassVisibility,
    ): array {
        $course->loadMissing([
            'instructor:id,name,avatar,bio',
            'category:id,name,slug',
            'courseSections' => fn ($q) => $q->orderBy('sort_order'),
            'courseSections.lessons' => fn ($q) => $q->orderBy('sort_order'),
            'chapters' => fn ($q) => $q->orderBy('sort_order'),
            'chapters.lessons' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        $lesson->loadMissing([
            'section:id,course_id,title,sort_order',
            'chapter:id,course_id,title,sort_order',
            'quiz.questions.options',
        ]);

        $enrollment = $user
            ? Enrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->withLearningAccess()
                ->first()
            : null;

        $isEnrolled = (bool) $enrollment;
        $canAccessLesson = $canBypassVisibility || $isEnrolled || $lesson->is_preview;

        $sections = $this->curriculumSections($course);
        $orderedLessons = $this->orderedLessons($sections);
        $progressMap = $user ? $this->progressMap($user->id, $course, $orderedLessons) : [];
        $quizStatusMap = $user ? $this->quizStatusMap($user->id, $orderedLessons) : [];

        $lessonItems = $this->buildLessonItems(
            $sections,
            $course,
            $lesson,
            $progressMap,
            $quizStatusMap,
            $canBypassVisibility,
            $isEnrolled,
        );

        $navigation = $this->navigation($orderedLessons, $lesson, $course);

        $currentProgress = $progressMap[$lesson->id] ?? null;
        $quizContext = $lesson->type === 'quiz'
            ? $this->buildQuizContext($course, $lesson, $user, $isEnrolled, $quizStatusMap[$lesson->id] ?? 'not_started')
            : null;

        return [
            'enrollment' => $enrollment,
            'isEnrolled' => $isEnrolled,
            'canAccessLesson' => $canAccessLesson,
            'courseProgress' => (float) ($enrollment?->progress_percent ?? 0),
            'requiredVideoPercent' => $course->requiredVideoPercent(),
            'sections' => $lessonItems['sections'],
            'currentSectionId' => $lesson->section_id ?? $lesson->chapter_id,
            'navigation' => $navigation,
            'lessonProgress' => $currentProgress,
            'lessonState' => $this->lessonState(
                $lesson,
                $currentProgress,
                $quizStatusMap[$lesson->id] ?? null,
                $canAccessLesson,
            ),
            'quizContext' => $quizContext,
            'totalLessons' => $orderedLessons->count(),
            'completedLessons' => collect($progressMap)->filter(fn ($p) => (bool) ($p['is_completed'] ?? false))->count(),
        ];
    }

    public function curriculumSections(Course $course): Collection
    {
        return $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;
    }

    public function orderedLessons(Collection $sections): Collection
    {
        return $sections
            ->flatMap(fn ($section) => $section->lessons)
            ->values();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function progressMap(int $userId, Course $course, Collection $lessons): array
    {
        $lessonIds = $lessons->pluck('id');

        return LessonProgress::query()
            ->where('user_id', $userId)
            ->where('course_id', $course->id)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id')
            ->map(fn (LessonProgress $progress) => [
                'watched_seconds' => (int) $progress->watched_seconds,
                'progress_percent' => (float) $progress->progress_percent,
                'is_completed' => (bool) $progress->is_completed,
                'completed_at' => $progress->completed_at?->toIso8601String(),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public function quizStatusMap(int $userId, Collection $lessons): array
    {
        $quizLessonIds = $lessons->where('type', 'quiz')->pluck('id');

        if ($quizLessonIds->isEmpty()) {
            return [];
        }

        $quizIdByLessonId = Lesson::query()
            ->whereIn('id', $quizLessonIds)
            ->with('quiz:id,lesson_id')
            ->get()
            ->mapWithKeys(fn (Lesson $lesson) => [$lesson->id => $lesson->quiz?->id])
            ->filter();

        $quizIds = $quizIdByLessonId->values();

        if ($quizIds->isEmpty()) {
            return $quizLessonIds->mapWithKeys(fn ($id) => [$id => 'not_started'])->all();
        }

        $attempts = QuizAttempt::query()
            ->where('user_id', $userId)
            ->whereIn('quiz_id', $quizIds)
            ->orderByDesc('completed_at')
            ->get()
            ->groupBy('quiz_id');

        $statusMap = [];

        foreach ($quizIdByLessonId as $lessonId => $quizId) {
            $userAttempts = $attempts->get($quizId, collect());

            if ($userAttempts->contains(fn (QuizAttempt $a) => $a->passed)) {
                $statusMap[$lessonId] = 'passed';
            } elseif ($userAttempts->isNotEmpty()) {
                $statusMap[$lessonId] = 'failed';
            } else {
                $statusMap[$lessonId] = 'not_started';
            }
        }

        return $statusMap;
    }

    public function lessonState(
        Lesson $lesson,
        ?array $progress,
        ?string $quizStatus,
        bool $canAccess,
    ): string {
        if (! $canAccess) {
            return 'locked';
        }

        if ($lesson->type === 'quiz') {
            return match ($quizStatus) {
                'passed' => 'completed',
                'failed' => 'in_progress',
                default => $progress && ($progress['is_completed'] ?? false) ? 'completed' : 'available',
            };
        }

        if ($progress && ($progress['is_completed'] ?? false)) {
            return 'completed';
        }

        if ($progress && (($progress['progress_percent'] ?? 0) > 0 || ($progress['watched_seconds'] ?? 0) > 0)) {
            return 'in_progress';
        }

        return 'available';
    }

    public function formatDuration(?int $seconds): ?string
    {
        $seconds = (int) ($seconds ?? 0);
        if ($seconds <= 0) {
            return null;
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }

    public function lessonTypeLabel(string $type): string
    {
        return match ($type) {
            'video' => 'Video',
            'document' => 'Bài đọc',
            'quiz' => 'Quiz',
            'assignment' => 'Bài tập',
            default => ucfirst($type),
        };
    }

    /**
     * @return array{sections: list<array<string, mixed>>}
     */
    private function buildLessonItems(
        Collection $sections,
        Course $course,
        Lesson $currentLesson,
        array $progressMap,
        array $quizStatusMap,
        bool $canBypass,
        bool $isEnrolled,
    ): array {
        $builtSections = [];

        foreach ($sections as $section) {
            $sectionLessons = [];
            $sectionCompleted = 0;

            foreach ($section->lessons as $sectionLesson) {
                $canAccess = $canBypass || $isEnrolled || $sectionLesson->is_preview;
                $progress = $progressMap[$sectionLesson->id] ?? null;
                $quizStatus = $quizStatusMap[$sectionLesson->id] ?? null;
                $state = $this->lessonState($sectionLesson, $progress, $quizStatus, $canAccess);

                if ($state === 'completed') {
                    $sectionCompleted++;
                }

                $sectionLessons[] = [
                    'id' => $sectionLesson->id,
                    'title' => $sectionLesson->title,
                    'type' => $sectionLesson->type,
                    'type_label' => $this->lessonTypeLabel($sectionLesson->type),
                    'duration' => $this->formatDuration($sectionLesson->duration_seconds ?: $sectionLesson->duration),
                    'is_preview' => (bool) $sectionLesson->is_preview,
                    'is_current' => (int) $sectionLesson->id === (int) $currentLesson->id,
                    'state' => $state,
                    'quiz_status' => $quizStatus,
                    'url' => $canAccess
                        ? route('courses.lessons.show', [$course, $sectionLesson])
                        : null,
                ];
            }

            $totalDuration = $section->lessons->sum(fn (Lesson $l) => (int) ($l->duration_seconds ?: $l->duration ?: 0));

            $builtSections[] = [
                'id' => $section->id,
                'title' => $section->title,
                'description' => $section->description ?? null,
                'lessons' => $sectionLessons,
                'completed_count' => $sectionCompleted,
                'total_count' => count($sectionLessons),
                'duration_label' => $this->formatDuration($totalDuration),
                'is_open' => collect($sectionLessons)->contains(fn ($l) => $l['is_current']),
            ];
        }

        return ['sections' => $builtSections];
    }

    /**
     * @return array{previous: ?array<string, mixed>, next: ?array<string, mixed>}
     */
    private function navigation(Collection $orderedLessons, Lesson $currentLesson, Course $course): array
    {
        $index = $orderedLessons->search(fn (Lesson $l) => (int) $l->id === (int) $currentLesson->id);

        $previous = $index !== false && $index > 0
            ? $this->navigationItem($orderedLessons[$index - 1], $course)
            : null;

        $next = $index !== false && $index < $orderedLessons->count() - 1
            ? $this->navigationItem($orderedLessons[$index + 1], $course)
            : null;

        return [
            'previous' => $previous,
            'next' => $next,
        ];
    }

    private function navigationItem(Lesson $lesson, Course $course): array
    {
        return [
            'id' => $lesson->id,
            'title' => $lesson->title,
            'type' => $lesson->type,
            'url' => route('courses.lessons.show', [$course, $lesson]),
        ];
    }

    private function buildQuizContext(
        Course $course,
        Lesson $lesson,
        ?User $user,
        bool $isEnrolled,
        string $quizStatus,
    ): ?array {
        $quiz = $lesson->quiz;

        if (! $quiz || ! $quiz->is_active) {
            return null;
        }

        $attemptsCount = $user
            ? $quiz->attempts()->where('user_id', $user->id)->count()
            : 0;

        $attemptLimitReached = $quiz->max_attempts !== null && $attemptsCount >= $quiz->max_attempts;
        $bestAttempt = $user
            ? $quiz->attempts()->where('user_id', $user->id)->orderByDesc('percent')->first()
            : null;

        $questions = $quiz->questions->map(fn ($question) => [
            'id' => $question->id,
            'question' => $question->question,
            'type' => $question->type,
            'form_type' => $question->form_type,
            'points' => (int) $question->points,
            'options' => $question->options->map(fn ($option) => [
                'id' => $option->id,
                'text' => $option->option_text,
            ])->values()->all(),
        ])->values()->all();

        return [
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'pass_score' => (int) $quiz->pass_score,
            'time_limit_minutes' => $quiz->time_limit_minutes,
            'max_attempts' => $quiz->max_attempts,
            'attempts_count' => $attemptsCount,
            'attempt_limit_reached' => $attemptLimitReached,
            'can_take' => $user?->isStudent() && $isEnrolled && ! $attemptLimitReached,
            'quiz_status' => $quizStatus,
            'submit_url' => route('courses.lessons.quiz.submit', [$course, $lesson]),
            'total_questions' => count($questions),
            'total_points' => $quiz->questions->sum('points'),
            'questions' => $questions,
            'best_percent' => $bestAttempt ? (float) $bestAttempt->percent : null,
            'remaining_attempts' => $quiz->max_attempts !== null
                ? max(0, $quiz->max_attempts - $attemptsCount)
                : null,
        ];
    }
}
