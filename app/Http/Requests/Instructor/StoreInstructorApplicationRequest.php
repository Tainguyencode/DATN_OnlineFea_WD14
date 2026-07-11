<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstructorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudent() ?? false;
    }

    public function rules(): array
    {
        return [
            'expertise' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'string', 'max:5000'],
            'introduction' => ['required', 'string', 'max:5000'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'intro_video_url' => ['nullable', 'url', 'max:500'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_number' => ['required', 'string', 'max:50'],
            'bank_account_name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'expertise.required' => 'Vui lòng nhập chuyên môn.',
            'experience.required' => 'Vui lòng nhập kinh nghiệm.',
            'introduction.required' => 'Vui lòng nhập giới thiệu bản thân.',
            'cv.required' => 'Vui lòng tải lên CV.',
            'bank_name.required' => 'Vui lòng nhập tên ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
        ];
    }
}
