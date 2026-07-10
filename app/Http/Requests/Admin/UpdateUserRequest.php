<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var \App\Models\User|null $user */
            $user = $this->route('user');
            $authUser = $this->user();

            if (! $user || ! $authUser) {
                return;
            }

            if ($user->id !== $authUser->id) {
                return;
            }

            if ($this->has('toggle_active') && $user->is_active) {
                $validator->errors()->add('error', 'Không thể khóa tài khoản của chính bạn.');
            }

            if ($this->filled('is_active') && ! $this->boolean('is_active')) {
                $validator->errors()->add('is_active', 'Không thể khóa tài khoản của chính bạn.');
            }

            if ($this->filled('role') && $user->role === 'admin' && $this->input('role') !== 'admin') {
                $validator->errors()->add('role', 'Không thể hạ quyền quản trị của chính bạn.');
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->has('toggle_active')) {
            return [];
        }

        if ($this->has('role') && ! $this->hasAny(['name', 'email', 'password', 'phone', 'bio', 'avatar', 'is_active'])) {
            return [
                'role' => ['required', Rule::in(['student', 'instructor', 'admin'])],
            ];
        }

        $userId = $this->route('user')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['sometimes', 'required', Rule::in(['student', 'instructor', 'admin'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên người dùng.',
            'name.max' => 'Tên người dùng không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'role.required' => 'Vui lòng chọn vai trò.',
            'role.in' => 'Vai trò không hợp lệ.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'bio.max' => 'Giới thiệu không được vượt quá 5000 ký tự.',
            'avatar.max' => 'Đường dẫn avatar không được vượt quá 255 ký tự.',
        ];
    }
}
