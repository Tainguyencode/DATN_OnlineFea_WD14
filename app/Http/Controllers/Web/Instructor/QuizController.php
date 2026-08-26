<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreQuizRequest;
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
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function store(StoreQuizRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $validated = $request->validated();

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
            $existingCount = $quiz->questions()->count();

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

                if ($type === QuizQuestion::TYPE_TRUE_FALSE && empty($options)) {
                    $options = ['Đúng', 'Sai'];
                }

                if (empty($options)) {
                    continue;
                }

                $question = $quiz->questions()->create([
                    'question' => $questionText,
                    'type' => $type,
                    'points' => $score,
                    'explanation' => $explanation,
                    'sort_order' => $existingCount + $importedCount,
                ]);

                foreach ($options as $optIdx => $optText) {
                    $isCorrect = in_array($optIdx + 1, $correctIndexes, true);

                    if (empty($correctIndexes) && $optIdx === 0) {
                        $isCorrect = true;
                    }

                    $question->options()->create([
                        'option_text' => $optText,
                        'is_correct' => $isCorrect,
                        'sort_order' => $optIdx,
                    ]);
                }

                if (in_array($question->type, [QuizQuestion::TYPE_SINGLE, QuizQuestion::TYPE_TRUE_FALSE], true)) {
                    $this->enforceSingleCorrectAnswer($question);
                }

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
            ['option_text' => 'Đúng', 'is_correct' => true, 'sort_order' => 0],
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
