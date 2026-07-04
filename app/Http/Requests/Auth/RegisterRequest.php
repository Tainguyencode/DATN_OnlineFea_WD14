<?php

namespace App\Http\Requests\Auth;

use App\Services\CaptchaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
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
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash:ascii', 'min:3', 'max:32', 'unique:users,username'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s().]{8,20}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role' => ['required', 'in:student,instructor'],
            'terms' => ['accepted'],
            'captcha_token' => ['required', 'string'],
            'captcha_answer' => ['required', 'string', 'max:10'],
        ];
    }

    public function validateCaptcha(): void
    {
        if (! CaptchaService::verify($this->input('captcha_token'), $this->input('captcha_answer'), 'register')) {
            throw ValidationException::withMessages([
                'captcha_answer' => 'Mã xác nhận không chính xác hoặc đã hết hạn.',
            ]);
        }
    }
}
