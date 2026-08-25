<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use App\Models\CourseSection;
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
        $title = $this->input('title');
        $description = $this->input('description');

        $this->merge([
            'title' => is_string($title) ? trim($title) : $title,
            'description' => is_string($description)
                ? (trim($description) !== '' ? trim($description) : null)
                : $description,
        ]);

        $section = $this->route('section');
        if ($section) {
            $sectionId = $section instanceof CourseSection
                ? $section->id
                : (is_object($section) ? ($section->id ?? '') : $section);
            $this->errorBag = 'updateSection_'.$sectionId;
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
            'description' => [
                'nullable',
                'string',
                'max:1000',
                static function (string $attribute, mixed $value, callable $fail): void {
                    if (CourseSection::descriptionContainsMarkup($value)) {
                        $fail('Mô tả chương chỉ được chứa văn bản thuần, không chứa HTML hoặc mã Blade.');
                    }
                },
            ],
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
