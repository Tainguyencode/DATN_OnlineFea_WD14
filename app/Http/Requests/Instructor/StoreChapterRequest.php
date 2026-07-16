<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;

class StoreChapterRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Course $course */
        $course = $this->route('course');

        return $course->isOwnedBy($this->user());
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('section')) {
            $this->errorBag = 'updateSection_'.$this->route('section')->id;
        } else {
            $this->errorBag = 'storeSection';
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên chương.',
            'title.string' => 'Tên chương phải là chuỗi ký tự.',
            'title.max' => 'Tên chương không được vượt quá :max ký tự.',

            'description.string' => 'Mô tả chương phải là chuỗi ký tự.',
            'description.max' => 'Mô tả chương không được vượt quá :max ký tự.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'Tên chương',
            'description' => 'Mô tả chương',
        ];
    }
}
