<?php

namespace App\Http\Requests\Learning;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:10000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,pdf,doc,docx,xls,xlsx,zip,rar,txt', 'max:51200', 'required_without:content'], // Max 50MB
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
            'attachment.mimes' => 'Tệp đính kèm phải thuộc định dạng hình ảnh, video, PDF, Word, Excel, ZIP, RAR hoặc TXT.',
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
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $course = $this->route('course');
        $lesson = $this->route('lesson');

        $redirectUrl = route('courses.lessons.show', [
            'course' => $course,
            'lesson' => $lesson,
            'tab' => 'qa',
        ]);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($redirectUrl);
    }
}
