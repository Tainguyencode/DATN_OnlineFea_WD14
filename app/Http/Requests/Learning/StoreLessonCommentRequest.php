<?php

namespace App\Http\Requests\Learning;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled via Policy in Controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:3000'],
            'parent_id' => ['nullable', 'integer', 'exists:lesson_comments,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Nội dung bình luận không được để trống.',
            'content.string' => 'Nội dung bình luận phải là chuỗi ký tự.',
            'content.max' => 'Nội dung bình luận không được vượt quá 3000 ký tự.',
            'parent_id.exists' => 'Bình luận cấp cha không tồn tại.',
        ];
    }
}
