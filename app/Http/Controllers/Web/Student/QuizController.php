<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Services\LearningProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Course $course, Lesson $lesson): View
    {
        $this->authorizePublishedLesson($course, $lesson);

        $lesson->loadMissing(['section:id,course_id,title', 'chapter:id,course_id,title', 'quiz.questions.options']);
        $quiz = $this->activeQuiz($lesson);
        $isEnrolled = $this->isEnrolled($course);
        $canBypass = auth()->check() && (
            auth()->user()->isAdmin() || 
            (auth()->user()->isInstructor() && $course->isOwnedBy(auth()->user()))
        );

        abort_unless($isEnrolled || $lesson->is_preview || $canBypass, 403);

        $attemptsCount = auth()->check()
            ? $quiz->attempts()->where('user_id', auth()->id())->count()
            : 0;
        $attemptLimitReached = $quiz->max_attempts !== null && $attemptsCount >= $quiz->max_attempts;
        $canSubmit = auth()->check()
            && auth()->user()->isStudent()
            && $isEnrolled
            && ! $attemptLimitReached;

        return view('courses.quiz', compact(
            'course',
            'lesson',
            'quiz',
            'isEnrolled',
            'canSubmit',
            'attemptsCount',
            'attemptLimitReached'
        ));
    }

    public function submit(Request $request, Course $course, Lesson $lesson, LearningProgressService $progressService): View|RedirectResponse
    {
        $this->authorizePublishedLesson($course, $lesson);

        abort_unless($request->user()?->isStudent(), 403);

        if (! $this->isEnrolled($course)) {
            return redirect()
                ->route('learn.lessons.quiz.show', [$course->slug, $lesson])
                ->with('error', 'Ban can dang ky khoa hoc de nop quiz.');
        }

        $lesson->loadMissing(['quiz.questions.options']);
        $quiz = $this->activeQuiz($lesson);

        if ($quiz->max_attempts !== null && $quiz->attempts()->where('user_id', $request->user()->id)->count() >= $quiz->max_attempts) {
            return redirect()
                ->route('learn.lessons.quiz.show', [$course->slug, $lesson])
                ->with('error', 'Ban da het so lan lam quiz nay.');
        }

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
        ]);

        $graded = app(\App\Services\QuizService::class)->grade($quiz, $validated['answers'] ?? []);

        $attempt = DB::transaction(function () use ($quiz, $request, $graded) {
            $attempt = QuizAttempt::create([
                'user_id' => $request->user()->id,
                'quiz_id' => $quiz->id,
                'score' => $graded['score'],
                'total_score' => $graded['total_score'],
                'percent' => $graded['percent'],
                'passed' => $graded['passed'],
                'answers' => $graded['answers'],
                'started_at' => now(),
                'completed_at' => now(),
            ]);

            foreach ($graded['questions'] as $questionId => $result) {
                if ($result['selected_ids'] === []) {
                    $attempt->attemptAnswers()->create([
                        'question_id' => $questionId,
                        'answer_id' => null,
                        'is_correct' => false,
                    ]);

                    continue;
                }

                foreach ($result['selected_ids'] as $answerId) {
                    $attempt->attemptAnswers()->create([
                        'question_id' => $questionId,
                        'answer_id' => $answerId,
                        'is_correct' => in_array($answerId, $result['correct_ids'], true),
                    ]);
                }
            }

            return $attempt;
        });

        if ($attempt->passed) {
            $progressService->recordLessonProgress(
                $request->user()->id,
                $course,
                $lesson,
                0,
                0,
                true,
            );
        }

        return view('courses.quiz-result', [
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $quiz,
            'attempt' => $attempt,
            'graded' => $graded,
        ]);
    }

    public function submitAjax(
        Request $request,
        Course $course,
        Lesson $lesson,
        LearningProgressService $progressService,
    ): JsonResponse {
        $this->authorizePublishedLesson($course, $lesson);
        abort_unless($lesson->type === 'quiz', 404);
        abort_unless($request->user()?->isStudent(), 403);

        if (! $this->isEnrolled($course)) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng ký khóa học để làm quiz.'], 403);
        }

        $lesson->loadMissing(['quiz.questions.options']);
        $quiz = $this->activeQuiz($lesson);

        if ($quiz->max_attempts !== null && $quiz->attempts()->where('user_id', $request->user()->id)->count() >= $quiz->max_attempts) {
            return response()->json(['success' => false, 'message' => 'Bạn đã hết số lần làm quiz này.'], 422);
        }

        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
        ]);

        $graded = app(\App\Services\QuizService::class)->grade($quiz, $validated['answers'] ?? []);

        $attempt = DB::transaction(function () use ($quiz, $request, $graded) {
            $attempt = QuizAttempt::create([
                'user_id' => $request->user()->id,
                'quiz_id' => $quiz->id,
                'score' => $graded['score'],
                'total_score' => $graded['total_score'],
                'percent' => $graded['percent'],
                'passed' => $graded['passed'],
                'answers' => $graded['answers'],
                'started_at' => now(),
                'completed_at' => now(),
            ]);

            foreach ($graded['questions'] as $questionId => $result) {
                if ($result['selected_ids'] === []) {
                    $attempt->attemptAnswers()->create([
                        'question_id' => $questionId,
                        'answer_id' => null,
                        'is_correct' => false,
                    ]);

                    continue;
                }

                foreach ($result['selected_ids'] as $answerId) {
                    $attempt->attemptAnswers()->create([
                        'question_id' => $questionId,
                        'answer_id' => $answerId,
                        'is_correct' => in_array($answerId, $result['correct_ids'], true),
                    ]);
                }
            }

            return $attempt;
        });

        if ($attempt->passed) {
            $progress = $progressService->recordLessonProgress(
                $request->user()->id,
                $course,
                $lesson,
                0,
                0,
                true,
            );
        } else {
            $progress = null;
        }

        $correctCount = collect($graded['questions'])->filter(fn ($q) => $q['is_correct'])->count();
        $totalQuestions = count($graded['questions']);

        return response()->json([
            'success' => true,
            'attempt' => [
                'id' => $attempt->id,
                'score' => $attempt->score,
                'total_score' => $attempt->total_score,
                'percent' => (float) $attempt->percent,
                'passed' => (bool) $attempt->passed,
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'pass_score' => (int) $quiz->pass_score,
            ],
            'graded' => [
                'questions' => collect($graded['questions'])->map(fn ($result, $questionId) => [
                    'question_id' => (int) $questionId,
                    'selected_ids' => $result['selected_ids'],
                    'correct_ids' => $result['correct_ids'],
                    'is_correct' => $result['is_correct'],
                ])->values(),
            ],
            'course_progress' => $progress['course_progress'] ?? null,
            'lesson_completed' => $progress['lesson_completed'] ?? false,
            'next_lesson_url' => $attempt->passed
                ? $this->nextLessonUrl($course, $lesson)
                : null,
            'attempts_count' => $quiz->attempts()->where('user_id', $request->user()->id)->count(),
            'remaining_attempts' => $quiz->max_attempts !== null
                ? max(0, $quiz->max_attempts - $quiz->attempts()->where('user_id', $request->user()->id)->count())
                : null,
        ]);
    }

    private function nextLessonUrl(Course $course, Lesson $lesson): ?string
    {
        $service = app(\App\Services\LearningPlayerService::class);
        $sections = $service->curriculumSections($course->loadMissing(['courseSections.lessons', 'chapters.lessons']));
        $ordered = $service->orderedLessons($sections);
        $index = $ordered->search(fn (Lesson $l) => (int) $l->id === (int) $lesson->id);

        if ($index === false || $index >= $ordered->count() - 1) {
            return null;
        }

        return route('courses.lessons.show', [$course, $ordered[$index + 1]]);
    }

    private function gradeQuiz(Quiz $quiz, array $submittedAnswers): array
    {
        $score = 0;
        $totalScore = 0;
        $answers = [];
        $questions = [];

        $quiz->loadMissing('questions.options');

        foreach ($quiz->questions as $question) {
            $points = (int) $question->points;
            $totalScore += $points;

            $selectedIds = $this->selectedAnswerIds($submittedAnswers[$question->id] ?? [], $question);
            $correctIds = $question->options
                ->where('is_correct', true)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $questionPassed = $this->questionIsCorrect($question, $selectedIds, $correctIds);

            if ($questionPassed) {
                $score += $points;
            }

            $answers[$question->id] = $selectedIds;
            $questions[$question->id] = [
                'selected_ids' => $selectedIds,
                'correct_ids' => $correctIds,
                'is_correct' => $questionPassed,
            ];
        }

        $percent = $totalScore > 0 ? round(($score / $totalScore) * 100, 2) : 0;

        return [
            'score' => $score,
            'total_score' => $totalScore,
            'percent' => $percent,
            'passed' => $percent >= (int) $quiz->pass_score,
            'answers' => $answers,
            'questions' => $questions,
        ];
    }

    private function selectedAnswerIds(mixed $rawAnswers, QuizQuestion $question): array
    {
        $rawAnswers = is_array($rawAnswers) ? $rawAnswers : [$rawAnswers];
        $validIds = $question->options->pluck('id')->map(fn ($id) => (int) $id)->all();

        return collect($rawAnswers)
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->filter(fn ($id) => in_array($id, $validIds, true))
            ->values()
            ->all();
    }

    private function questionIsCorrect(QuizQuestion $question, array $selectedIds, array $correctIds): bool
    {
        sort($selectedIds);
        sort($correctIds);

        if ($correctIds === []) {
            return false;
        }

        if ($question->type === QuizQuestion::TYPE_MULTIPLE) {
            return $selectedIds === $correctIds;
        }

        return count($selectedIds) === 1 && $selectedIds[0] === $correctIds[0];
    }

    private function activeQuiz(Lesson $lesson): Quiz
    {
        $quiz = $lesson->quiz;

        abort_unless($quiz && $quiz->is_active, 404);

        return $quiz;
    }

    private function authorizePublishedLesson(Course $course, Lesson $lesson): void
    {
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);
        abort_unless($course->status === Course::STATUS_PUBLISHED && (bool) $course->is_published, 404);
    }

    private function isEnrolled(Course $course): bool
    {
        return auth()->check()
            && Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->withLearningAccess()
                ->exists();
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
