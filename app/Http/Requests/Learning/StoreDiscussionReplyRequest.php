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
            'content.required' => 'Nội dung phản hồi không được để trống.',
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

        $redirectUrl = route('courses.lessons.show', [
            'course' => $discussion->lesson->course,
            'lesson' => $discussion->lesson,
            'discussion_id' => $discussion->id,
            'tab' => 'qa',
        ]);

        throw (new \Illuminate\Validation\ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($redirectUrl);
    }
}
