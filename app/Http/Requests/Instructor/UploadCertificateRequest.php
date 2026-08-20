<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class UploadCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && $user->role === 'instructor' && $user->instructor_status !== 'approved';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.file' => 'Tệp chứng chỉ không hợp lệ.',
            'file.mimes' => 'Chứng chỉ chỉ chấp nhận định dạng PDF, JPG, JPEG hoặc PNG.',
            'file.max' => 'Dung lượng mỗi chứng chỉ không được vượt quá 5MB.',
            'files.array' => 'Danh sách tệp không hợp lệ.',
            'files.max' => 'Bạn chỉ có thể tải lên tối đa 10 tệp cùng một lúc.',
            'files.*.file' => 'Tệp chứng chỉ không hợp lệ.',
            'files.*.mimes' => 'Mỗi chứng chỉ chỉ chấp nhận định dạng PDF, JPG, JPEG hoặc PNG.',
            'files.*.max' => 'Dung lượng mỗi chứng chỉ không được vượt quá 5MB.',
            'title.max' => 'Tên chứng chỉ không được vượt quá 255 ký tự.',
        ];
    }
}
