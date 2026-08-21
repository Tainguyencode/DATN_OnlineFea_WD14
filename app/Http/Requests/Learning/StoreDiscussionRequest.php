<?php

namespace App\Http\Requests\Learning;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscussionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled via Policies in Controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'max:51200', 'required_without:content'], // Max 50MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.string' => 'Tiêu đề câu hỏi phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề câu hỏi không được vượt quá :max ký tự.',
            'content.required_without' => 'Vui lòng nhập nội dung câu hỏi hoặc đính kèm tệp tin.',
            'content.string' => 'Nội dung chi tiết phải là chuỗi ký tự.',
            'attachment.required_without' => 'Vui lòng đính kèm tệp tin hoặc nhập nội dung câu hỏi.',
            'attachment.file' => 'Tệp đính kèm không hợp lệ.',
            'attachment.max' => 'Dung lượng tệp đính kèm không được vượt quá 50MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'Tiêu đề câu hỏi',
            'content' => 'Nội dung chi tiết',
            'attachment' => 'Tệp đính kèm',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $course = $this->route('course');
        $lesson = $this->route('lesson');

        $redirectUrl = route('courses.lessons.show', [
            'course' => $course,
            'lesson' => $lesson,
            'tab' => 'qa',
        ]);

        throw (new \Illuminate\Validation\ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($redirectUrl);
    }
}
