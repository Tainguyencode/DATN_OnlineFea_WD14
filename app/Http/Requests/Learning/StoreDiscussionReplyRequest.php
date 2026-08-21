<?php

namespace App\Http\Requests\Learning;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['nullable', 'string', 'required_without:attachment'],
            'reply_to_message_id' => ['nullable', 'integer'],
            'attachment' => ['nullable', 'file', 'max:51200', 'required_without:content'], // Max 50MB
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
            'attachment.max' => 'Tệp đính kèm không được vượt quá 50MB.',
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

        throw (new \Illuminate\Validation\ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($redirectUrl);
    }
}
