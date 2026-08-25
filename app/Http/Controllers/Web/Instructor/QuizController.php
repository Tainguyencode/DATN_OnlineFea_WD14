<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreQuizRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(
        private readonly QuizContentService $quizContent,
        private readonly QuizVersioningService $quizVersioning,
    ) {}

    public function show(Course $course, Lesson $lesson): View
    {
        $this->authorizeLesson($course, $lesson);

        if ($lesson->type === Lesson::TYPE_QUIZ) {
            $lesson->setRelation('quiz', $this->quizContent->getOrCreateForLesson($lesson));
        }

        $lesson->loadMissing('quiz');
        $quizIdentity = $lesson->quiz;
        $authoringVersion = $quizIdentity ? $this->quizVersioning->authoringVersion($quizIdentity) : null;
        $publishedVersion = $quizIdentity?->current_published_version_id
            ? $this->quizVersioning->currentPublished($quizIdentity)
            : null;
        $quiz = $quizIdentity && $authoringVersion
            ? $this->quizVersioning->projectVersion($quizIdentity, $authoringVersion)
            : $quizIdentity;
        $reviewUpdate = $quizIdentity && $quizIdentity->current_draft_version_id && $authoringVersion
            ? $this->quizVersioning->contentUpdateForVersion($quizIdentity, $authoringVersion)
            : null;

        return view('instructor.quizzes.show', [
            'course' => $course,
            'lesson' => $lesson,
            'quiz' => $quiz,
            'quizValidation' => $authoringVersion ? $this->quizContent->validateQuizVersion($authoringVersion) : null,
            'questionTypes' => $this->quizContent->questionTypes(),
            'authoringVersion' => $authoringVersion,
            'publishedVersion' => $publishedVersion,
            'reviewUpdate' => $reviewUpdate,
            'mutationsLocked' => $reviewUpdate && ($reviewUpdate->isPending() || $reviewUpdate->isApproved()),
            'desiredIsActive' => $reviewUpdate
                ? (bool) data_get($reviewUpdate->payload, 'desired_is_active', $quizIdentity?->is_active)
                : (bool) $quizIdentity?->is_active,
        ]);
    }

    public function store(StoreQuizRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $this->quizContent->saveMetadata($lesson, $request->validated(), $request->boolean('is_active'));

        return redirect()
            ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
            ->with('success', 'Đã lưu bản nháp Quiz.');
    }

    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeQuiz($quiz);
        $validated = $this->validatedQuestion($request);

        $this->quizContent->createQuestion($quiz, $validated);

        return back()->with('success', 'Đã thêm câu hỏi vào bản nháp Quiz.');
    }

    public function updateQuestion(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $validated = $this->validatedQuestion($request);

        $this->quizContent->updateQuestion($question, $validated);

        return back()->with('success', 'Đã cập nhật câu hỏi trong bản nháp Quiz.');
    }

    public function destroyQuestion(QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $this->quizContent->deleteQuestion($question);

        return back()->with('success', 'Đã gỡ câu hỏi khỏi bản nháp Quiz.');
    }

    public function storeAnswer(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $validated = $this->validatedAnswer($request);

        $this->quizContent->createOption($question, $validated);

        return back()->with('success', 'Đã thêm đáp án vào bản nháp Quiz.');
    }

    public function updateAnswers(Request $request, QuizQuestion $question): RedirectResponse
    {
        $this->authorizeQuestion($question);
        $authoringQuestion = $this->authoringQuestion($question);

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

        $selectedCorrectIds = $authoringQuestion->type === QuizQuestion::TYPE_MULTIPLE
            ? collect($request->input('correct_answers', []))->map(fn ($id) => (int) $id)
            : collect([$request->input('correct_answer')])->map(fn ($id) => (int) $id);

        $this->quizContent->updateOptions(
            $question,
            $validated['answers'],
            $request->input('delete_answers', []),
            $selectedCorrectIds->filter()->values()->all(),
        );

        return back()->with('success', 'Đã lưu đáp án trong bản nháp Quiz.');
    }

    public function updateAnswer(Request $request, QuizOption $answer): RedirectResponse
    {
        $this->authorizeAnswer($answer);
        $validated = $this->validatedAnswer($request);

        $this->quizContent->updateOption($answer, $validated);

        return back()->with('success', 'Đã cập nhật đáp án trong bản nháp Quiz.');
    }

    public function destroyAnswer(QuizOption $answer): RedirectResponse
    {
        $this->authorizeAnswer($answer);
        $this->quizContent->deleteOption($answer);

        return back()->with('success', 'Đã xóa đáp án khỏi bản nháp Quiz.');
    }

    private function validatedQuestion(Request $request): array
    {
        return $request->validate([
            'question_text' => ['required', 'string', 'max:10000'],
            'question_type' => ['required', Rule::in(array_keys($this->quizContent->questionTypes()))],
            'score' => ['required', 'integer', 'min:1', 'max:1000'],
            'explanation' => ['nullable', 'string', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ], [
            'question_text.required' => 'Vui lòng nhập nội dung câu hỏi.',
            'question_text.string' => 'Nội dung câu hỏi phải là chuỗi ký tự.',
            'question_text.max' => 'Nội dung câu hỏi không được vượt quá 10.000 ký tự.',
            'question_type.required' => 'Vui lòng chọn loại câu hỏi.',
            'question_type.in' => 'Loại câu hỏi không hợp lệ.',
            'score.required' => 'Vui lòng nhập số điểm cho câu hỏi.',
            'score.integer' => 'Điểm phải là số nguyên.',
            'score.min' => 'Điểm tối thiểu là 1.',
            'score.max' => 'Điểm tối đa là 1000.',
        ]);
    }

    private function validatedAnswer(Request $request): array
    {
        $validated = $request->validate([
            'answer_text' => ['required', 'string', 'max:5000'],
            'is_correct' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ], [
            'answer_text.required' => 'Vui lòng nhập nội dung đáp án.',
            'answer_text.string' => 'Nội dung đáp án phải là chuỗi ký tự.',
            'answer_text.max' => 'Nội dung đáp án không được vượt quá 5000 ký tự.',
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

    private function authoringQuestion(QuizQuestion $question): QuizQuestion
    {
        $question->loadMissing('quiz');
        $version = $this->quizVersioning->authoringVersion($question->quiz);

        if (! $version) {
            abort(422, 'Quiz chưa có phiên bản nội dung để chỉnh sửa.');
        }

        $projectedQuiz = $this->quizVersioning->projectVersion($question->quiz, $version);
        $projected = $projectedQuiz->questions->firstWhere('id', $question->id);

        abort_unless($projected, 422, 'Câu hỏi không thuộc phiên bản Quiz hiện tại.');

        return $projected;
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
