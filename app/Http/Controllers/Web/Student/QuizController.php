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
use App\Services\QuizService;
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
        $availability = null;
        if (auth()->user()?->isStudent() && $isEnrolled) {
            $attempt = $attemptService->findInProgress($course, $lesson, auth()->user());
            $availability = $attemptService->attemptAvailability($quiz, auth()->user());
            if (! $attempt && $availability['has_remaining_attempts']) {
                $attempt = $attemptService->startOrResume($course, $lesson, auth()->user());
            }
            if ($attempt) {
                $quiz = $attemptService->projectQuiz($attempt);
            }
        }

        $availability ??= auth()->user()?->isStudent()
            ? $attemptService->attemptAvailability($lesson->quiz, auth()->user())
            : null;
        $attemptsCount = $availability['attempts_used'] ?? 0;
        $attemptLimitReached = $availability ? ! $availability['has_remaining_attempts'] : false;
        $canSubmit = $attempt !== null;
        $canStart = auth()->user()?->isStudent() && $isEnrolled && ($availability['has_remaining_attempts'] ?? false);

        $player = app(LearningPlayerService::class)->buildPlayerContext($course, $lesson, auth()->user(), $canBypass);
        $quizContext = $player['quizContext'];
        abort_unless($quizContext, 404);

        return view('courses.quiz-fullscreen', compact('course', 'lesson', 'quizContext'));
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

    public function saveProgress(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless($request->user()?->isStudent(), 403);
        if (! $this->isEnrolled($course)) {
            return response()->json(['success' => false, 'message' => 'Enrollment is required.'], 403);
        }

        $attemptId = (int) $request->input('attempt_id');
        $answers = $request->input('answers', []);
        if (is_string($answers)) {
            $answers = json_decode($answers, true) ?: [];
        }
        $remainingSeconds = $request->has('remaining_seconds') ? (int) $request->input('remaining_seconds') : null;

        $attempt = app(QuizAttemptService::class)->saveProgress(
            $course,
            $lesson,
            $request->user(),
            $attemptId,
            $answers,
            $remainingSeconds
        );

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'remaining_seconds' => $attempt->remaining_seconds,
        ]);
    }

    public function terminate(Request $request, Course $course, Lesson $lesson, LearningProgressService $progressService): JsonResponse
    {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless($request->user()?->isStudent(), 403);
        if (! $this->isEnrolled($course)) {
            return response()->json(['success' => false, 'message' => 'Enrollment is required.'], 403);
        }

        $attemptId = (int) $request->input('attempt_id');
        $reason = (string) $request->input('reason', QuizAttempt::REASON_PAGE_EXIT);
        $answers = $request->input('answers', []);
        if (is_string($answers)) {
            $answers = json_decode($answers, true) ?: [];
        }
        $remainingSeconds = $request->has('remaining_seconds') ? (int) $request->input('remaining_seconds') : null;

        $attemptService = app(QuizAttemptService::class);
        $termination = $attemptService->terminate(
            $course,
            $lesson,
            $request->user(),
            $attemptId,
            $reason,
            $answers,
            $remainingSeconds,
            $request->ip(),
            $request->userAgent()
        );

        $attempt = $termination['attempt'];
        $quiz = $attemptService->projectQuiz($attempt);
        $policy = $attemptService->reviewPolicy($attempt, $request->user());

        if ($termination['completed_now']) {
            $this->recordAttemptProgress($request, $course, $lesson, $quiz, $attempt, $progressService);
        }

        return response()->json([
            'success' => true,
            'terminated' => true,
            'reason' => $attempt->termination_reason,
            'reason_label' => $attempt->getTerminationReasonLabel(),
            'attempt' => [
                'id' => $attempt->id,
                'score' => $attempt->score,
                'total_score' => $attempt->total_score,
                'percent' => (float) $attempt->percent,
                'passed' => (bool) $attempt->passed,
                'review_url' => route('learn.lessons.quiz.attempts.show', [$course->slug, $lesson, $attempt]),
                'result_url' => route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]),
            ],
            'review_mode' => $policy['review_mode'],
            'attempts_count' => $policy['attempts_used'],
            'remaining_attempts' => $policy['remaining_attempts'],
        ]);
    }

    public function submit(Request $request, Course $course, Lesson $lesson, LearningProgressService $progressService): View|RedirectResponse|JsonResponse
    {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless($request->user()?->isStudent(), 403);
        if (! $this->isEnrolled($course)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Enrollment is required to submit this quiz.'], 403);
            }
            return redirect()->route('learn.lessons.quiz.show', [$course->slug, $lesson])->with('error', 'Enrollment is required to submit this quiz.');
        }

        [$attempt, $quiz, $graded, $completedNow] = $this->gradeBoundAttempt($request, $course, $lesson);
        if ($completedNow) {
            $this->recordAttemptProgress($request, $course, $lesson, $quiz, $attempt, $progressService);
        }

        if ($request->expectsJson()) {
            $attemptService = app(QuizAttemptService::class);
            $policy = $attemptService->reviewPolicy($attempt, $request->user());
            $gradedPayload = app(QuizService::class)->submissionFeedback($graded, $policy['review_mode']);

            return response()->json([
                'success' => true,
                'attempt' => [
                    'id' => $attempt->id,
                    'score' => $attempt->score,
                    'total_score' => $attempt->total_score,
                    'percent' => (float) $attempt->percent,
                    'passed' => (bool) $attempt->passed,
                    'pass_score' => $attempt->quizVersion?->pass_score ?? $quiz->pass_score,
                    'quiz_version_id' => $attempt->quiz_version_id,
                ],
                'graded' => $gradedPayload,
                'review_mode' => $policy['review_mode'],
                'remaining_attempts' => $policy['remaining_attempts'],
                'quiz' => [
                    'id' => $quiz->id,
                    'version' => $quiz->version,
                ],
            ]);
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
        $policy = $attemptService->reviewPolicy($attempt, $request->user());
        $gradedPayload = app(QuizService::class)->submissionFeedback($graded, $policy['review_mode']);

        return response()->json([
            'success' => true,
            'attempt' => [
                'id' => $attempt->id,
                'quiz_version_id' => $attempt->quiz_version_id,
                'score' => $attempt->score,
                'total_score' => $attempt->total_score,
                'percent' => (float) $attempt->percent,
                'passed' => (bool) $attempt->passed,
                'correct_count' => collect($graded['questions'])
                    ->reject(fn ($question) => $question['is_excluded'] ?? false)
                    ->filter(fn ($question) => $question['is_correct'])
                    ->count(),
                'total_questions' => count($graded['questions']),
                'pass_score' => (int) $quiz->pass_score,
                'review_url' => route('learn.lessons.quiz.attempts.show', [$course->slug, $lesson, $attempt]),
                'result_url' => route('learn.lessons.quiz.result', [$course->slug, $lesson, $attempt]),
            ],
            'graded' => $gradedPayload,
            'review_mode' => $policy['review_mode'],
            'course_progress' => $progress['course_progress'] ?? null,
            'lesson_completed' => $progress['lesson_completed'] ?? (bool) LessonProgress::query()
                ->where('user_id', $request->user()->id)
                ->where('lesson_id', $lesson->id)
                ->value('is_completed'),
            'next_lesson_url' => $this->nextLessonUrl($course, $lesson),
            'attempts_count' => $policy['attempts_used'],
            'remaining_attempts' => $policy['remaining_attempts'],
        ]);
    }

    public function recordFocusViolation(Request $request, Course $course, Lesson $lesson, QuizAttempt $attempt): JsonResponse
    {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless((int) $attempt->user_id === (int) $request->user()?->id, 403);
        abort_unless($attempt->status === 'in_progress', 409);
        abort_unless($attempt->quiz?->lesson_id === $lesson->id, 404);

        $attempt->increment('focus_violation_count');

        return response()->json(['count' => $attempt->fresh()->focus_violation_count]);
    }

    public function reviewAttempt(
        Request $request,
        Course $course,
        Lesson $lesson,
        QuizAttempt $attempt,
        QuizService $quizService,
    ): View {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless($lesson->type === 'quiz', 404);

        $quiz = $lesson->quiz()->firstOrFail();
        abort_unless((int) $attempt->quiz_id === (int) $quiz->id, 404);

        $user = $request->user();
        $canAccess = $user && (
            (int) $attempt->user_id === (int) $user->id ||
            $user->isAdmin() ||
            ($user->isInstructor() && $course->isOwnedBy($user))
        );

        abort_unless($canAccess, 403, 'Bạn không có quyền xem lại bài làm này.');

        $policy = app(QuizAttemptService::class)->reviewPolicy($attempt, $user);
        $review = $quizService->buildAttemptReview($attempt, $policy);
        $quiz = $review['quiz'];

        return view('courses.quiz-result', [
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $quiz,
            'attempt' => $review['attempt'],
            'review' => $review,
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
        $canBypass = $user && ($user->isAdmin() || ($user->isInstructor() && $course->isOwnedBy($user)));

        if (! $canBypass) {
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
            Enrollment::firstOrCreate([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ], [
                'status' => Enrollment::STATUS_ACTIVE,
                'progress_percent' => 0,
                'enrolled_at' => now(),
            ]);

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
