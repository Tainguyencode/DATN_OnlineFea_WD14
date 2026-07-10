<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class RejectCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:'.config('course.reject_reason_min_length', 10), 'max:2000'],
            'checklist' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'comment' => trim((string) $this->input('comment', $this->input('reject_reason', $this->input('reason')))),
        ]);
    }
}
