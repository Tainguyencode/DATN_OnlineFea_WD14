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

        abort_unless($isEnrolled || $lesson->is_preview, 403);

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

        $graded = $this->gradeQuiz($quiz, $validated['answers'] ?? []);

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
            $progressService->recordLessonProgress($request->user()->id, $course, $lesson, 0, true);
        }

        return view('courses.quiz-result', [
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $quiz,
            'attempt' => $attempt,
            'graded' => $graded,
        ]);
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
                ->where('status', 'active')
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
