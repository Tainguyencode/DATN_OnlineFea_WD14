<?php

namespace App\Http\Requests\Learning;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    public function rules(): array
    {
        return [
            'last_position_seconds' => ['nullable', 'integer', 'min:0'],
            'furthest_position_seconds' => ['nullable', 'integer', 'min:0'],
            'played_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'watched_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'video_duration_seconds' => ['nullable', 'numeric', 'min:0'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'client_updated_at' => ['nullable', 'date'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}
