<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class ReplyReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('reply', $this->route('review')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['instructor_reply' => trim((string) $this->input('instructor_reply'))]);
    }

    public function rules(): array
    {
        return [
            'instructor_reply' => ['required', 'string', 'min:2', 'max:1500', 'not_regex:/<[^>]*>/'],
        ];
    }
}
