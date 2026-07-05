<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Course $course, Lesson $lesson): View
    {
        $this->authorizeLesson($course, $lesson);

        if ($lesson->type === 'quiz' && ! $lesson->quiz()->exists()) {
            $lesson->quiz()->create([
                'title' => $lesson->title,
                'pass_score' => 70,
                'time_limit_minutes' => null,
                'max_attempts' => null,
                'is_active' => true,
            ]);
        }

        $lesson->loadMissing(['quiz.questions.options']);

        return view('instructor.quizzes.show', [
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $lesson->quiz,
            'questionTypes' => $this->questionTypes(),
        ]);
    }

    public function store(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'pass_score' => ['required', 'integer', 'min:0', 'max:100'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $quiz = $lesson->quiz()->with('questions.options')->first();
        if (! $quiz || ! $this->quizHasCorrectAnswers($quiz)) {
            return back()
                ->withErrors(['quiz' => 'Moi cau hoi can co it nhat 1 dap an dung truoc khi luu quiz.'])
                ->withInput();
        }

        DB::transaction(function () use ($lesson, $validated, $request) {
            Quiz::updateOrCreate(
                ['lesson_id' => $lesson->id],
                [
                    ...$validated,
                    'is_active' => $request->boolean('is_active'),
                ]
            );

            if ($lesson->type !== 'quiz') {
                $lesson->update(['type' => 'quiz']);
            }
        });

        return redirect()
            ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
            ->with('success', 'Da luu thong tin quiz cho bai hoc.');
    }

    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeQuiz($quiz);
        $validated = $this->validatedQuestion($request);

        DB::transaction(function () use ($quiz, $validated) {
            $question = $quiz->questions()->create([
                'question' => $validated['question_text'],
                'type' => QuizQuestion::storageTypeFromRequest($validated['question_type']),
                'points' => $validated['score'],
                'explanation' => $validated['explanation'] ?? null,
                'sort_order' => $validated['sort_order'] ?? $quiz->questions()->count(),
            ]);

            if ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $this->ensureTrueFalseOptions($question);
            }
        });

        return back()->with('success', 'Da them cau hoi.');
    }

    public function updateQuestion(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $validated = $this->validatedQuestion($request);

        DB::transaction(function () use ($question, $validated) {
            $question->update([
                'question' => $validated['question_text'],
                'type' => QuizQuestion::storageTypeFromRequest($validated['question_type']),
                'points' => $validated['score'],
                'explanation' => $validated['explanation'] ?? null,
                'sort_order' => $validated['sort_order'] ?? $question->sort_order,
            ]);

            if ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $this->ensureTrueFalseOptions($question);
            }

            if (in_array($question->type, [QuizQuestion::TYPE_SINGLE, QuizQuestion::TYPE_TRUE_FALSE], true)) {
                $this->enforceSingleCorrectAnswer($question);
            }
        });

        return back()->with('success', 'Da cap nhat cau hoi.');
    }

    public function destroyQuestion(QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $question->delete();

        return back()->with('success', 'Da xoa cau hoi.');
    }

    public function storeAnswer(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $validated = $this->validatedAnswer($request);

        if ($question->type === QuizQuestion::TYPE_TRUE_FALSE && $question->options()->count() >= 2) {
            return back()->with('error', 'Cau hoi dung/sai chi can 2 dap an.');
        }

        DB::transaction(function () use ($question, $validated) {
            if ($validated['is_correct'] && $question->type !== QuizQuestion::TYPE_MULTIPLE) {
                $question->options()->update(['is_correct' => false]);
            }

            $question->options()->create([
                'option_text' => $validated['answer_text'],
                'is_correct' => $validated['is_correct'],
                'sort_order' => $validated['sort_order'] ?? $question->options()->count(),
            ]);
        });

        return back()->with('success', 'Da them dap an.');
    }

    public function updateAnswers(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $question->loadMissing('options');

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*.answer_text' => ['required', 'string', 'max:5000'],
            'answers.*.sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'correct_answer' => ['nullable', 'integer'],
            'correct_answers' => ['nullable', 'array'],
            'correct_answers.*' => ['integer'],
            'delete_answers' => ['nullable', 'array'],
            'delete_answers.*' => ['integer'],
        ]);

        $answerIds = $question->options->pluck('id')->map(fn ($id) => (int) $id)->all();
        $deleteIds = collect($request->input('delete_answers', []))
            ->map(fn ($id) => (int) $id)
            ->intersect($answerIds)
            ->values()
            ->all();
        $remainingIds = array_values(array_diff($answerIds, $deleteIds));

        if ($remainingIds === []) {
            return back()
                ->withErrors(['answers' => 'Cau hoi can giu lai it nhat 1 dap an.'])
                ->withInput();
        }

        $selectedCorrectIds = $question->type === QuizQuestion::TYPE_MULTIPLE
            ? collect($request->input('correct_answers', []))->map(fn ($id) => (int) $id)
            : collect([$request->input('correct_answer')])->map(fn ($id) => (int) $id);

        $selectedCorrectIds = $selectedCorrectIds
            ->intersect($remainingIds)
            ->values()
            ->all();

        if ($selectedCorrectIds === []) {
            return back()
                ->withErrors(['answers' => 'Hay chon it nhat 1 dap an dung cho cau hoi nay.'])
                ->withInput();
        }

        if ($question->type !== QuizQuestion::TYPE_MULTIPLE && count($selectedCorrectIds) !== 1) {
            return back()
                ->withErrors(['answers' => 'Cau hoi mot lua chon chi duoc co 1 dap an dung.'])
                ->withInput();
        }

        DB::transaction(function () use ($question, $validated, $deleteIds, $remainingIds, $selectedCorrectIds) {
            if ($deleteIds !== []) {
                $question->options()->whereIn('id', $deleteIds)->delete();
            }

            foreach ($validated['answers'] as $answerId => $answerData) {
                $answerId = (int) $answerId;

                if (! in_array($answerId, $remainingIds, true)) {
                    continue;
                }

                $question->options()->whereKey($answerId)->update([
                    'option_text' => $answerData['answer_text'],
                    'sort_order' => $answerData['sort_order'] ?? 0,
                    'is_correct' => in_array($answerId, $selectedCorrectIds, true),
                ]);
            }
        });

        return back()->with('success', 'Da luu dap an cho cau hoi.');
    }

    public function updateAnswer(Request $request, QuizOption $answer): RedirectResponse
    {
        $this->authorizeAnswer($answer);
        $validated = $this->validatedAnswer($request);

        DB::transaction(function () use ($answer, $validated) {
            $answer->loadMissing('question');

            if ($validated['is_correct'] && $answer->question->type !== QuizQuestion::TYPE_MULTIPLE) {
                $answer->question->options()->whereKeyNot($answer->id)->update(['is_correct' => false]);
            }

            $answer->update([
                'option_text' => $validated['answer_text'],
                'is_correct' => $validated['is_correct'],
                'sort_order' => $validated['sort_order'] ?? $answer->sort_order,
            ]);
        });

        return back()->with('success', 'Da cap nhat dap an.');
    }

    public function destroyAnswer(QuizOption $answer): RedirectResponse
    {
        $this->authorizeAnswer($answer);
        $answer->delete();

        return back()->with('success', 'Da xoa dap an.');
    }

    private function validatedQuestion(Request $request): array
    {
        return $request->validate([
            'question_text' => ['required', 'string', 'max:10000'],
            'question_type' => ['required', Rule::in(array_keys($this->questionTypes()))],
            'score' => ['required', 'integer', 'min:1', 'max:1000'],
            'explanation' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);
    }

    private function validatedAnswer(Request $request): array
    {
        $validated = $request->validate([
            'answer_text' => ['required', 'string', 'max:5000'],
            'is_correct' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $validated['is_correct'] = $request->boolean('is_correct');

        return $validated;
    }

    private function authorizeLesson(Course $course, Lesson $lesson): void
    {
        abort_unless($course->isOwnedBy(auth()->user()), 403);
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);
    }

    private function authorizeQuiz(Quiz $quiz): void
    {
        $quiz->loadMissing('lesson.course');

        abort_unless($quiz->lesson?->course?->isOwnedBy(auth()->user()), 403);
    }

    private function authorizeQuestion(QuizQuestion $question): void
    {
        $question->loadMissing('quiz.lesson.course');

        abort_unless($question->quiz?->lesson?->course?->isOwnedBy(auth()->user()), 403);
    }

    private function authorizeAnswer(QuizOption $answer): void
    {
        $answer->loadMissing('question.quiz.lesson.course');

        abort_unless($answer->question?->quiz?->lesson?->course?->isOwnedBy(auth()->user()), 403);
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

    private function ensureTrueFalseOptions(QuizQuestion $question): void
    {
        if ($question->options()->exists()) {
            return;
        }

        $question->options()->createMany([
            ['option_text' => 'Dung', 'is_correct' => true, 'sort_order' => 0],
            ['option_text' => 'Sai', 'is_correct' => false, 'sort_order' => 1],
        ]);
    }

    private function enforceSingleCorrectAnswer(QuizQuestion $question): void
    {
        $correctAnswer = $question->options()
            ->where('is_correct', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if (! $correctAnswer) {
            return;
        }

        $question->options()
            ->where('id', '!=', $correctAnswer->id)
            ->update(['is_correct' => false]);
    }

    private function quizHasCorrectAnswers(Quiz $quiz): bool
    {
        $quiz->loadMissing('questions.options');

        if ($quiz->questions->isEmpty()) {
            return false;
        }

        return $quiz->questions->every(
            fn (QuizQuestion $question) => $question->options->where('is_correct', true)->isNotEmpty()
        );
    }

    private function questionTypes(): array
    {
        return [
            'single_choice' => 'single_choice',
            'multiple_choice' => 'multiple_choice',
            'true_false' => 'true_false',
        ];
    }
}
