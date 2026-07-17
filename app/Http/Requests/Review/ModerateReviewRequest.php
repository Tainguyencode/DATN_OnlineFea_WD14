<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class ModerateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('moderate', $this->route('review')) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('moderation_note')) {
            $this->merge(['moderation_note' => trim((string) $this->input('moderation_note'))]);
        }
    }

    public function rules(): array
    {
        $required = $this->routeIs('admin.student-reviews.reject', 'admin.student-reviews.hide');

        return [
            'moderation_note' => [$required ? 'required' : 'nullable', 'string', 'min:5', 'max:1000', 'not_regex:/<[^>]*>/'],
        ];
    }
}
