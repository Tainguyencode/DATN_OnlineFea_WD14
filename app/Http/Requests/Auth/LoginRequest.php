<?php

namespace App\Http\Requests\Auth;

use App\Services\CaptchaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
            'captcha_token' => ['required', 'string'],
            'captcha_answer' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Vui lòng nhập email hoặc tên đăng nhập.',
            'identifier.string'   => 'Email hoặc tên đăng nhập không hợp lệ.',
            'identifier.max'      => 'Email hoặc tên đăng nhập không được vượt quá 255 ký tự.',
            'password.required'   => 'Vui lòng nhập mật khẩu.',
            'password.string'     => 'Mật khẩu không hợp lệ.',
            'captcha_token.required'  => 'Phiên xác nhận đã hết hạn, vui lòng tải lại trang.',
            'captcha_answer.required' => 'Vui lòng nhập kết quả phép tính xác nhận.',
            'captcha_answer.string'   => 'Kết quả xác nhận không hợp lệ.',
            'captcha_answer.max'      => 'Kết quả xác nhận không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'identifier'     => 'email / tên đăng nhập',
            'password'       => 'mật khẩu',
            'captcha_answer' => 'mã xác nhận',
        ];
    }

    public function validateCaptcha(): void
    {
        if (! CaptchaService::verify($this->input('captcha_token'), $this->input('captcha_answer'), 'login')) {
            throw ValidationException::withMessages([
                'captcha_answer' => 'Mã xác nhận không chính xác hoặc đã hết hạn.',
            ]);
        }
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => "Bạn đăng nhập quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.",
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('identifier')).'|'.$this->ip());
    }
}
