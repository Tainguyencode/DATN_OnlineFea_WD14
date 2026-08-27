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
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function downloadSampleTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="mau_cau_hoi_quiz.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Nội dung câu hỏi',
                'Loại câu hỏi (single_choice / multiple_choice / true_false)',
                'Điểm',
                'Giải thích đáp án',
                'Đáp án 1',
                'Đáp án 2',
                'Đáp án 3',
                'Đáp án 4',
                'Đáp án đúng (ví dụ: 1 hoặc 1,2 hoặc 1,3)',
            ]);

            fputcsv($handle, [
                'Lập trình Python là gì?',
                'single_choice',
                '1',
                'Python là ngôn ngữ lập trình bậc cao phổ biến.',
                'Ngôn ngữ lập trình',
                'Hệ điều hành',
                'Cơ sở dữ liệu',
                'Trình duyệt web',
                '1',
            ]);

            fputcsv($handle, [
                'Các kiểu dữ liệu cơ bản trong Python bao gồm những gì?',
                'multiple_choice',
                '2',
                'int, float và str là các kiểu dữ liệu tích hợp sẵn trong Python.',
                'int',
                'float',
                'str',
                'HTML',
                '1,2,3',
            ]);

            fputcsv($handle, [
                'HTML có phải là một ngôn ngữ lập trình không?',
                'true_false',
                '1',
                'HTML là ngôn ngữ đánh dấu siêu văn bản, không phải ngôn ngữ lập trình.',
                'Đúng',
                'Sai',
                '',
                '',
                '2',
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    public function importQuestions(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorizeQuiz($quiz);

        $request->validate([
            'import_file' => ['required', 'file', 'max:5120'],
        ], [
            'import_file.required' => 'Vui lòng chọn tệp Excel hoặc CSV để tải lên.',
            'import_file.file' => 'Tệp nhập không hợp lệ.',
            'import_file.max' => 'Dung lượng tệp tối đa là 5MB.',
        ]);

        $file = $request->file('import_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['csv', 'txt', 'xlsx', 'xls'], true)) {
            return back()->with('error', 'Tệp phải có định dạng .csv hoặc .xlsx / .xls.')->withInput();
        }

        $rows = $this->readRowsFromFile($file->getPathname());

        if (empty($rows)) {
            return back()->with('error', 'Không thể đọc nội dung tệp hoặc tệp rỗng. Vui lòng kiểm tra lại.')->withInput();
        }

        $importedCount = 0;

        DB::transaction(function () use ($quiz, $rows, &$importedCount) {
            foreach ($rows as $index => $row) {
                if ($index === 0 && (str_contains(mb_strtolower($row[0] ?? ''), 'nội dung') || str_contains(mb_strtolower($row[0] ?? ''), 'câu hỏi'))) {
                    continue;
                }

                $questionText = trim($row[0] ?? '');
                if ($questionText === '') {
                    continue;
                }

                $rawType = trim($row[1] ?? 'single_choice');
                $type = $this->normalizeQuestionType($rawType);
                $score = max(1, (int) ($row[2] ?? 1));
                $explanation = trim($row[3] ?? '') ?: null;

                $options = [];
                for ($col = 4; $col <= 7; $col++) {
                    if (isset($row[$col])) {
                        $optText = trim($row[$col]);
                        if ($optText !== '') {
                            $options[] = $optText;
                        }
                    }
                }

                $rawCorrect = trim($row[8] ?? '');
                $correctIndexes = $this->parseCorrectAnswers($rawCorrect, count($options), $type);

                $optionData = collect($options)
                    ->values()
                    ->map(function (string $option, int $optionIndex) use ($correctIndexes): array {
                        return [
                            'option_text' => $option,
                            'is_correct' => in_array($optionIndex + 1, $correctIndexes, true)
                                || ($correctIndexes === [] && $optionIndex === 0),
                            'sort_order' => $optionIndex,
                        ];
                    })
                    ->all();

                if ($type === QuizQuestion::TYPE_TRUE_FALSE) {
                    $correctIndex = in_array(2, $correctIndexes, true) ? 1 : 0;
                    $optionData = [
                        [
                            'option_text' => 'Đúng',
                            'identity' => 'TRUE',
                            'is_correct' => $correctIndex === 0,
                            'sort_order' => 0,
                        ],
                        [
                            'option_text' => 'Sai',
                            'identity' => 'FALSE',
                            'is_correct' => $correctIndex === 1,
                            'sort_order' => 1,
                        ],
                    ];
                }

                if ($optionData === []) {
                    continue;
                }

                $this->quizContent->createQuestion($quiz, [
                    'question_text' => $questionText,
                    'question_type' => $type,
                    'score' => $score,
                    'explanation' => $explanation,
                ], $optionData);

                $importedCount++;
            }
        });

        if ($importedCount === 0) {
            return back()->with('error', 'Không tìm thấy câu hỏi hợp lệ trong tệp. Vui lòng sử dụng đúng định dạng file mẫu.')->withInput();
        }

        return back()->with('success', "Đã nhập thành công {$importedCount} câu hỏi từ tệp vào Quiz!");
    }

    private function readRowsFromFile(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [];
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if ($firstLine !== false) {
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        while (($data = fgetcsv($handle, 10000, $delimiter)) !== false) {
            if (array_filter($data, fn ($cell) => trim((string) $cell) !== '') !== []) {
                $rows[] = array_map(fn ($val) => trim((string) $val), $data);
            }
        }

        fclose($handle);

        return $rows;
    }

    private function normalizeQuestionType(string $raw): string
    {
        $lower = mb_strtolower(trim($raw));

        if (str_contains($lower, 'multiple') || str_contains($lower, 'nhiều') || $lower === 'multiple_choice' || $lower === '2') {
            return QuizQuestion::TYPE_MULTIPLE;
        }

        if (str_contains($lower, 'true') || str_contains($lower, 'đúng') || str_contains($lower, 'sai') || $lower === 'true_false' || $lower === '3') {
            return QuizQuestion::TYPE_TRUE_FALSE;
        }

        return QuizQuestion::TYPE_SINGLE;
    }

    private function parseCorrectAnswers(string $raw, int $optionsCount, string $type): array
    {
        if ($raw === '') {
            return [1];
        }

        $parts = preg_split('/[;,\s]+/', $raw);
        $result = [];

        foreach ($parts as $p) {
            $p = trim($p);
            if (is_numeric($p)) {
                $idx = (int) $p;
                if ($idx >= 1 && $idx <= 20) {
                    $result[] = $idx;
                }
            } elseif (mb_strtolower($p) === 'đúng' || mb_strtolower($p) === 'true') {
                $result[] = 1;
            } elseif (mb_strtolower($p) === 'sai' || mb_strtolower($p) === 'false') {
                $result[] = 2;
            }
        }

        return array_unique($result);
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
