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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:51200'], // Max 50MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề câu hỏi không được để trống.',
            'title.string' => 'Tiêu đề câu hỏi phải là chuỗi ký tự.',
            'title.max' => 'Tiêu đề câu hỏi không được vượt quá :max ký tự.',
            'content.required' => 'Nội dung chi tiết câu hỏi không được để trống.',
            'content.string' => 'Nội dung chi tiết phải là chuỗi ký tự.',
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
