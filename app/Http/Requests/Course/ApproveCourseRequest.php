<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class ApproveCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'checklist' => ['required', 'array'],
            'publish_immediately' => ['sometimes', 'boolean'],
        ];
    }
}
