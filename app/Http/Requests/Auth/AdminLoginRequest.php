<?php

namespace App\Http\Requests\Auth;

use App\Support\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'remember' => $this->boolean('remember'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $identifier = trim((string) $this->input('identifier'));

            if ($identifier === '') {
                return;
            }

            if (! filter_var($identifier, FILTER_VALIDATE_EMAIL) && ! PhoneNumber::isValid($identifier)) {
                $validator->errors()->add('identifier', 'Email hoặc số điện thoại không hợp lệ.');
            }
        });
    }
}
