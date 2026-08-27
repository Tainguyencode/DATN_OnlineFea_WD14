<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\LearningPlayerService;
use App\Services\LearningProgressService;
use App\Services\PointService;
use App\Services\QuizAttemptResultService;
use App\Services\QuizAttemptService;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Course $course, Lesson $lesson): View
    {
        $this->authorizePublishedLesson($course, $lesson);
        $lesson->loadMissing(['quiz.currentPublishedVersion.questionMappings.questionVersion.options']);
        $quiz = $this->activeQuiz($lesson);
        $isEnrolled = $this->isEnrolled($course);
        $canBypass = auth()->check() && (auth()->user()->isAdmin() || (auth()->user()->isInstructor() && $course->isOwnedBy(auth()->user())));
        abort_unless($isEnrolled || $lesson->is_preview || $canBypass, 403);

        $attempt = null;
        $attemptService = app(QuizAttemptService::class);
        if (auth()->user()?->isStudent() && $isEnrolled) {
            $attempt = $attemptService->findInProgress($course, $lesson, auth()->user());
            $completedAttempts = $attemptService->completedAttemptsCount($quiz, auth()->user());
            if (! $attempt && ($quiz->max_attempts === null || $completedAttempts < $quiz->max_attempts)) {
                $attempt = $attemptService->startOrResume($course, $lesson, auth()->user());
            }
            if ($attempt) {
                $quiz = $attemptService->projectQuiz($attempt);
            }
        }

        $attemptsCount = auth()->user()?->isStudent() ? $attemptService->completedAttemptsCount($quiz, auth()->user()) : 0;
        $attemptLimitReached = $quiz->max_attempts !== null && $attemptsCount >= $quiz->max_attempts;
        $canSubmit = $attempt !== null;
        $canStart = auth()->user()?->isStudent() && $isEnrolled && (! $attemptLimitReached || $attempt !== null);

        return view('courses.quiz', compact('course', 'lesson', 'quiz', 'isEnrolled', 'attempt', 'canStart', 'canSubmit', 'attemptsCount', 'attemptLimitReached'));
    }

    public function start(Request $request, Course $course, Lesson $lesson): JsonResponse|RedirectResponse
    {
        $this->authorizePublishedLesson($course, $lesson);
        $attempt = app(QuizAttemptService::class)->startOrResume($course, $lesson, $request->user());
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'attempt' => [
                'id' => $attempt->id,
                'quiz_version_id' => $attempt->quiz_version_id,
                'started_at' => $attempt->started_at?->toIso8601String(),
            ]]);
        }

        return redirect()->route('learn.lessons.quiz.show', [$course->slug, $lesson]);
    }

    public function submit(Request $request, Course $course, Lesson $lesson, LearningProgressService $progressService): View|RedirectResponse
    {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless($request->user()?->isStudent(), 403);
        if (! $this->isEnrolled($course)) {
            return redirect()->route('learn.lessons.quiz.show', [$course->slug, $lesson])->with('error', 'Enrollment is required to submit this quiz.');
        }

        [$attempt, $quiz, $graded, $completedNow] = $this->gradeBoundAttempt($request, $course, $lesson);
        if ($completedNow) {
            $this->recordAttemptProgress($request, $course, $lesson, $quiz, $attempt, $progressService);
        }

        return redirect()->route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]);
    }

    public function result(Course $course, Lesson $lesson, QuizAttempt $attempt, QuizAttemptResultService $resultService): View
    {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless(auth()->user()?->isStudent(), 403);

        $result = $resultService->forLearner($course, $lesson, auth()->user(), $attempt);

        return view('courses.quiz-attempt-result', compact('course', 'lesson', 'result'));
    }

    public function submitAjax(Request $request, Course $course, Lesson $lesson, LearningProgressService $progressService): JsonResponse
    {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless($request->user()?->isStudent(), 403);
        if (! $this->isEnrolled($course)) {
            return response()->json(['success' => false, 'message' => 'Enrollment is required to submit this quiz.'], 403);
        }

        [$attempt, $quiz, $graded, $completedNow] = $this->gradeBoundAttempt($request, $course, $lesson);
        $progress = $completedNow
            ? $this->recordAttemptProgress($request, $course, $lesson, $quiz, $attempt, $progressService)
            : [];
        $attemptService = app(QuizAttemptService::class);
        $completedAttempts = $attemptService->completedAttemptsCount($quiz, $request->user());

        return response()->json([
            'success' => true,
            'attempt' => [
                'id' => $attempt->id,
                'quiz_version_id' => $attempt->quiz_version_id,
                'score' => $attempt->score,
                'total_score' => $attempt->total_score,
                'percent' => (float) $attempt->percent,
                'passed' => (bool) $attempt->passed,
                'correct_count' => collect($graded['questions'])->filter(fn ($question) => $question['is_correct'])->count(),
                'total_questions' => count($graded['questions']),
                'pass_score' => (int) $quiz->pass_score,
            ],
            'graded' => ['questions' => collect($graded['questions'])->map(fn ($result, $questionId) => [
                'question_id' => (int) $questionId,
                'selected_ids' => $result['selected_ids'],
                'correct_ids' => $result['correct_ids'],
                'is_correct' => $result['is_correct'],
            ])->values()],
            'course_progress' => $progress['course_progress'] ?? null,
            'lesson_completed' => $progress['lesson_completed'] ?? (bool) LessonProgress::query()
                ->where('user_id', $request->user()->id)
                ->where('lesson_id', $lesson->id)
                ->value('is_completed'),
            'next_lesson_url' => $this->nextLessonUrl($course, $lesson),
            'attempts_count' => $completedAttempts,
            'remaining_attempts' => $quiz->max_attempts === null ? null : max(0, $quiz->max_attempts - $completedAttempts),
        ]);
    }

    /** @return array{0: QuizAttempt, 1: Quiz, 2: array<string, mixed>, 3: bool} */
    private function gradeBoundAttempt(Request $request, Course $course, Lesson $lesson): array
    {
        $validated = $request->validate([
            'attempt_id' => ['required', 'integer'],
            'answers' => ['nullable', 'array'],
        ]);
        $attemptService = app(QuizAttemptService::class);
        $submission = $attemptService->submit(
            $course,
            $lesson,
            $request->user(),
            (int) $validated['attempt_id'],
            $validated['answers'] ?? [],
        );
        $attempt = $submission['attempt'];
        $quiz = $attemptService->projectQuiz($attempt);

        return [$attempt, $quiz, $submission['graded'], $submission['completed_now']];
    }

    private function recordAttemptProgress(Request $request, Course $course, Lesson $lesson, Quiz $quiz, QuizAttempt $attempt, LearningProgressService $progressService): array
    {
        $progress = $progressService->recordLessonProgress($request->user()->id, $course, $lesson);
        app(PointService::class)->awardQuizPoints($request->user()->id, $quiz, (float) $attempt->percent, $course->id);

        return $progress;
    }

    private function nextLessonUrl(Course $course, Lesson $lesson): ?string
    {
        $service = app(LearningPlayerService::class);
        $ordered = $service->orderedLessons($service->curriculumSections($course->loadMissing(['courseSections.lessons', 'chapters.lessons'])));
        $index = $ordered->search(fn (Lesson $item) => (int) $item->id === (int) $lesson->id);

        return $index === false || $index >= $ordered->count() - 1 ? null : route('courses.lessons.show', [$course, $ordered[$index + 1]]);
    }

    private function activeQuiz(Lesson $lesson): Quiz
    {
        $quiz = $lesson->quiz;
        abort_unless($quiz && app(QuizContentService::class)->isEffectivelyActive($quiz), 404);

        return app(QuizVersioningService::class)->projectVersion($quiz, app(QuizVersioningService::class)->currentPublished($quiz));
    }

    private function authorizePublishedLesson(Course $course, Lesson $lesson): void
    {
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);
        $user = auth()->user();
        if (! ($user && ($user->isAdmin() || ($user->isInstructor() && $course->isOwnedBy($user))))) {
            abort_unless($course->isPublished(), 404);
        }
    }

    private function isEnrolled(Course $course): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->isAdmin() || ($user->isInstructor() && $course->isOwnedBy($user))) {
            Enrollment::firstOrCreate(['user_id' => $user->id, 'course_id' => $course->id], ['status' => Enrollment::STATUS_ACTIVE, 'progress_percent' => 0, 'enrolled_at' => now()]);

            return true;
        }

        return Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->withLearningAccess()->exists();
    }

    private function lessonBelongsToCourse(Course $course, Lesson $lesson): bool
    {
        if ((int) $lesson->course_id === (int) $course->id) {
            return true;
        }
        if ($lesson->section_id && $lesson->section()->where('course_id', $course->id)->exists()) {
            return true;
        }

        return $lesson->chapter_id && $lesson->chapter()->where('course_id', $course->id)->exists();
    }
}
