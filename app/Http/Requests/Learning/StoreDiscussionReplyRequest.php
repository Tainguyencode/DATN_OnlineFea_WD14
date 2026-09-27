<?php

namespace App\Http\Requests\Learning;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreDiscussionReplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['nullable', 'string', 'max:10000', 'required_without:attachment'],
            'reply_to_message_id' => ['nullable', 'integer'],
            'reply_to_key' => ['nullable', 'string', 'regex:/^(discussion|reply):[1-9][0-9]*$/'],
            'lesson_id' => ['nullable', 'integer', Rule::exists('lessons', 'id')],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mkv,pdf,doc,docx,xls,xlsx,zip,rar,txt', 'max:51200', 'required_without:content'], // Max 50MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required_without' => 'Vui lòng nhập nội dung phản hồi hoặc đính kèm tệp.',
            'content.string' => 'Nội dung phản hồi phải là chuỗi văn bản.',
            'attachment.required_without' => 'Vui lòng đính kèm tệp hoặc nhập nội dung phản hồi.',
            'attachment.file' => 'Tệp đính kèm không hợp lệ.',
            'attachment.mimes' => 'Tệp đính kèm phải thuộc định dạng hình ảnh, video, PDF, Word, Excel, ZIP, RAR hoặc TXT.',
            'attachment.max' => 'Tệp đính kèm không được vượt quá 50MB.',
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
        $discussion = $this->route('discussion');

        $redirectUrl = url()->previous();
        if (! $redirectUrl && $discussion?->lesson) {
            $redirectUrl = route('courses.lessons.show', [
                'course' => $discussion->lesson->course,
                'lesson' => $discussion->lesson,
                'discussion_id' => $discussion->id,
                'tab' => 'qa',
            ]);
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($redirectUrl);
    }
}
