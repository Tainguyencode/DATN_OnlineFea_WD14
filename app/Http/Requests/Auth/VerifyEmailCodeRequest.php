<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã xác thực.',
            'code.digits' => 'Mã xác thực phải gồm đúng 6 chữ số.',
        ];
    }
}
