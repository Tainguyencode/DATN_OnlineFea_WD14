<?php

namespace App\Http\Requests\Review;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Review::class, $this->route('course')]) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['comment' => trim((string) $this->input('comment'))]);
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:2000', 'not_regex:/<[^>]*>/'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.*' => 'Vui lòng chọn mức đánh giá từ 1 đến 5 sao.',
            'comment.required' => 'Vui lòng nhập nội dung đánh giá.',
            'comment.min' => 'Nội dung đánh giá phải có ít nhất 10 ký tự.',
            'comment.max' => 'Nội dung đánh giá không được vượt quá 2.000 ký tự.',
            'comment.not_regex' => 'Nội dung đánh giá không được chứa thẻ HTML.',
        ];
    }
}
