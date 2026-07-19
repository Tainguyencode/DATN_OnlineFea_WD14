<?php

namespace App\Http\Requests\Learning;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExplainLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'Vui lòng nhập câu hỏi.',
            'question.max' => 'Câu hỏi tối đa 1000 ký tự.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'code' => 'validation',
            'message' => $validator->errors()->first() ?: 'Dữ liệu không hợp lệ.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
