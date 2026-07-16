<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\QuizQuestion;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Course $course */
        $course = $this->route('course');
        return $course && $course->isOwnedBy($this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'pass_score' => ['required', 'integer', 'min:0', 'max:100'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên bài trắc nghiệm.',
            'title.string' => 'Tên bài trắc nghiệm phải là chuỗi ký tự.',
            'title.max' => 'Tên bài trắc nghiệm không được vượt quá 255 ký tự.',
            
            'description.string' => 'Mô tả phải là chuỗi ký tự.',
            'description.max' => 'Mô tả không được vượt quá 5000 ký tự.',
            
            'pass_score.required' => 'Vui lòng nhập điểm đạt.',
            'pass_score.integer' => 'Điểm đạt phải là số nguyên.',
            'pass_score.min' => 'Điểm đạt không được nhỏ hơn 0.',
            'pass_score.max' => 'Điểm đạt không được lớn hơn 100.',
            
            'time_limit_minutes.integer' => 'Thời gian làm bài phải là số nguyên.',
            'time_limit_minutes.min' => 'Thời gian làm bài tối thiểu là 1 phút.',
            'time_limit_minutes.max' => 'Thời gian làm bài tối đa là 1440 phút.',
            
            'max_attempts.integer' => 'Số lần làm tối đa phải là số nguyên.',
            'max_attempts.min' => 'Số lần làm tối đa tối thiểu là 1.',
            'max_attempts.max' => 'Số lần làm tối đa không được vượt quá 100.',
            
            'is_active.boolean' => 'Trạng thái hoạt động không hợp lệ.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            /** @var Lesson $lesson */
            $lesson = $this->route('lesson');
            
            if (!$lesson) {
                return;
            }

            $quiz = $lesson->quiz()->with('questions.options')->first();
            
            if (! $quiz) {
                $validator->errors()->add('quiz', 'Vui lòng thêm câu hỏi trước khi lưu quiz.');
                return;
            }

            if ($quiz->questions->count() < 5) {
                $validator->errors()->add('quiz', 'Quiz phải có ít nhất 5 câu hỏi trước khi lưu.');
            }

            foreach ($quiz->questions as $question) {
                if ($question->type !== QuizQuestion::TYPE_TRUE_FALSE && $question->options->count() < 3) {
                    $validator->errors()->add('quiz', 'Mỗi câu hỏi (trừ Đúng/Sai) phải có ít nhất 3 đáp án.');
                    break;
                }
            }

            if (! $this->quizHasCorrectAnswers($quiz)) {
                $validator->errors()->add('quiz', 'Mỗi câu hỏi cần có ít nhất 1 đáp án đúng trước khi lưu quiz.');
            }
        });
    }

    private function quizHasCorrectAnswers($quiz): bool
    {
        if ($quiz->questions->isEmpty()) {
            return false;
        }

        return $quiz->questions->every(
            fn (QuizQuestion $question) => $question->options->where('is_correct', true)->isNotEmpty()
        );
    }
}
