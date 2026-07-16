<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $source = $this->filled('slug') ? (string) $this->input('slug') : (string) $this->input('name');

        if ($source !== '') {
            $this->merge(['slug' => Str::slug($source)]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'slug' => ['required', 'alpha_dash:ascii', 'max:64', Rule::unique('roles', 'slug')],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên vai trò.',
            'name.unique' => 'Tên vai trò đã tồn tại.',
            'name.max' => 'Tên vai trò không được vượt quá :max ký tự.',
            'slug.required' => 'Vui lòng nhập slug hoặc tên vai trò hợp lệ.',
            'slug.alpha_dash' => 'Slug chỉ được chứa chữ, số, dấu gạch ngang hoặc gạch dưới.',
            'slug.unique' => 'Slug vai trò đã tồn tại.',
            'slug.max' => 'Slug không được vượt quá :max ký tự.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',
            'permissions.array' => 'Danh sách quyền không hợp lệ.',
            'permissions.*.exists' => 'Một quyền được chọn không tồn tại.',
        ];
    }
}
