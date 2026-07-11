<?php

namespace App\Http\Requests\Auth;

use App\Services\CaptchaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc'],
            'captcha_token' => ['required', 'string'],
            'captcha_answer' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'captcha_token.required' => 'Phiên xác nhận đã hết hạn, vui lòng tải lại trang.',
            'captcha_answer.required' => 'Vui lòng nhập kết quả phép tính xác nhận.',
        ];
    }

    public function validateCaptcha(): void
    {
        if (! CaptchaService::verify($this->input('captcha_token'), $this->input('captcha_answer'), 'forgot-password')) {
            throw ValidationException::withMessages([
                'captcha_answer' => 'Mã xác nhận không chính xác hoặc đã hết hạn.',
            ]);
        }
    }
}
