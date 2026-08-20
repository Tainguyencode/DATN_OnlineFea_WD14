<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResubmitInstructorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'instructor' && $this->user()->instructor_status === 'rejected';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s().]{8,20}$/', 'unique:users,phone,'.$this->user()->id],
            'specialty' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'string', 'max:2000'],
            'bio' => ['required', 'string', 'max:2000'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'certificates' => ['nullable', 'array', 'max:10'],
            'certificates.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'agree_information' => ['accepted'],
            'agree_terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng (8–20 ký tự số).',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'specialty.required' => 'Vui lòng nhập lĩnh vực chuyên môn.',
            'experience.required' => 'Vui lòng nhập kinh nghiệm giảng dạy/làm việc.',
            'bio.required' => 'Vui lòng giới thiệu bản thân.',
            'linkedin_url.url' => 'Địa chỉ LinkedIn không hợp lệ.',
            'github_url.url' => 'Địa chỉ GitHub không hợp lệ.',
            'website_url.url' => 'Địa chỉ Website không hợp lệ.',
            'cv.file' => 'Tệp CV không hợp lệ.',
            'cv.mimes' => 'CV phải ở định dạng PDF.',
            'cv.max' => 'Dung lượng CV tối đa là 5MB.',
            'certificate.file' => 'Tệp chứng chỉ không hợp lệ.',
            'certificate.mimes' => 'Chứng chỉ chỉ chấp nhận định dạng PDF, JPG, JPEG hoặc PNG.',
            'certificate.max' => 'Dung lượng chứng chỉ không được vượt quá 5MB.',
            'certificates.array' => 'Danh sách tệp không hợp lệ.',
            'certificates.max' => 'Bạn chỉ có thể tải lên tối đa 10 tệp cùng một lúc.',
            'certificates.*.file' => 'Tệp chứng chỉ không hợp lệ.',
            'certificates.*.mimes' => 'Mỗi chứng chỉ chỉ chấp nhận định dạng PDF, JPG, JPEG hoặc PNG.',
            'certificates.*.max' => 'Dung lượng mỗi chứng chỉ không được vượt quá 5MB.',
            'agree_information.accepted' => 'Vui lòng cam kết các thông tin là chính xác.',
            'agree_terms.accepted' => 'Bạn phải đọc và đồng ý với Điều khoản dành cho Giảng viên.',
        ];
    }
}
