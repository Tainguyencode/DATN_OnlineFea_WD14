<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLearningPathRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'target_role' => ['nullable', 'string', 'max:255'],
            'salary_range' => ['nullable', 'string', 'max:255'],
            'estimated_duration' => ['nullable', 'string', 'max:255'],
            'skills_input' => ['nullable', 'string', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
            'courses' => ['nullable', 'array'],
            'courses.*' => ['exists:courses,id'],
            'sort_orders' => ['nullable', 'array'],
            'stage_names' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tên lộ trình học tập.',
            'title.string' => 'Tên lộ trình học tập phải là chuỗi ký tự.',
            'title.max' => 'Tên lộ trình học tập không được vượt quá 255 ký tự.',
            'level.required' => 'Vui lòng chọn cấp độ phù hợp cho lộ trình.',
            'level.in' => 'Cấp độ được chọn không hợp lệ.',
            'target_role.max' => 'Vị trí việc làm mục tiêu không được vượt quá 255 ký tự.',
            'salary_range.max' => 'Mức lương dự kiến không được vượt quá 255 ký tự.',
            'estimated_duration.max' => 'Thời lượng ước tính không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả lộ trình không được vượt quá 3000 ký tự.',
            'skills_input.max' => 'Danh sách kỹ năng không được vượt quá 1000 ký tự.',
            'courses.array' => 'Danh sách khóa học không hợp lệ.',
            'courses.*.exists' => 'Khóa học được chọn không tồn tại trên hệ thống.',
        ];
    }
}
