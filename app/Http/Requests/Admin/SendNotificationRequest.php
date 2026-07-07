<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'url' => ['nullable', 'string', 'max:500'],
            'audience' => ['required', Rule::in(['all', 'students', 'instructors', 'students_instructors', 'course'])],
            'course_id' => ['nullable', 'integer', 'exists:courses,id', 'required_if:audience,course'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề thông báo.',
            'message.required' => 'Vui lòng nhập nội dung thông báo.',
            'audience.required' => 'Vui lòng chọn đối tượng nhận thông báo.',
            'course_id.required_if' => 'Vui lòng chọn khóa học khi gửi theo khóa học.',
        ];
    }
}
