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
            'avatar'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'phone'           => ['required', 'string', 'regex:/^[0-9+\-\s().]{8,20}$/'],
            'password'        => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'terms'           => ['accepted'],
            'captcha_token'   => ['required', 'string'],
            'captcha_answer'  => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image'            => 'Tệp tải lên phải là hình ảnh.',
            'avatar.mimes'            => 'Ảnh đại diện chỉ chấp nhận định dạng JPG, JPEG, PNG hoặc WebP.',
            'avatar.max'              => 'Ảnh đại diện không được vượt quá 2MB.',
            'name.required'           => 'Vui lòng nhập họ và tên.',
            'name.string'             => 'Họ và tên không hợp lệ.',
            'name.max'                => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required'          => 'Vui lòng nhập địa chỉ email.',
            'email.email'             => 'Địa chỉ email không đúng định dạng.',
            'email.max'               => 'Email không được vượt quá 255 ký tự.',
            'email.unique'            => 'Email này đã được sử dụng, vui lòng chọn email khác.',
            'phone.required'          => 'Vui lòng nhập số điện thoại.',
            'phone.string'            => 'Số điện thoại không hợp lệ.',
            'phone.regex'             => 'Số điện thoại không đúng định dạng (8–20 ký tự số).',
            'password.required'       => 'Vui lòng nhập mật khẩu.',
            'password.confirmed'      => 'Xác nhận mật khẩu không khớp.',
            'password.min'            => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.mixed'          => 'Mật khẩu phải có cả chữ hoa và chữ thường.',
            'password.numbers'        => 'Mật khẩu phải chứa ít nhất một chữ số.',
            'password.symbols'        => 'Mật khẩu phải chứa ít nhất một ký tự đặc biệt.',
            'terms.accepted'          => 'Vui lòng đồng ý với điều khoản sử dụng để tiếp tục.',
            'captcha_token.required'  => 'Phiên xác nhận đã hết hạn, vui lòng tải lại trang.',
            'captcha_answer.required' => 'Vui lòng nhập kết quả phép tính xác nhận.',
            'captcha_answer.string'   => 'Kết quả xác nhận không hợp lệ.',
            'captcha_answer.max'      => 'Kết quả xác nhận không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'avatar'         => 'ảnh đại diện',
            'name'           => 'họ và tên',
            'email'          => 'email',
            'phone'          => 'số điện thoại',
            'password'       => 'mật khẩu',
            'terms'          => 'điều khoản sử dụng',
            'captcha_answer' => 'mã xác nhận',
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
