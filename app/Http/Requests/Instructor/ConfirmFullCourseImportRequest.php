<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmFullCourseImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isInstructor() ?? false;
    }

    public function rules(): array
    {
        return [
            // The batch token is deliberately the only client-controlled input.
            'batch_token' => ['required', 'uuid'],
        ];
    }
}
