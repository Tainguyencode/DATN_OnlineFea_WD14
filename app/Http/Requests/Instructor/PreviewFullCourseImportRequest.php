<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class PreviewFullCourseImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['file' => ['required', 'file', 'extensions:xlsx', 'mimes:xlsx', 'max:5120']];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file Excel để xem trước.',
            'file.extensions' => 'Chỉ hỗ trợ file có phần mở rộng .xlsx.',
            'file.mimes' => 'Nội dung file phải là workbook XLSX hợp lệ.',
            'file.max' => 'Dung lượng file Excel tối đa là 5MB.',
        ];
    }
}
