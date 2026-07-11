<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('slug')) {
            $this->merge(['slug' => Str::slug((string) $this->input('slug'))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $category = $this->route('category');
                $categoryId = $category?->id;
                $parentId = $this->integer('parent_id') ?: null;

                if (! $parentId) {
                    if ($category && $category->courses()->exists()) {
                        $validator->errors()->add(
                            'parent_id',
                            'Danh mục đang có khóa học nên không thể chuyển thành danh mục cha.',
                        );
                    }

                    return;
                }

                if ($categoryId && (int) $parentId === (int) $categoryId) {
                    $validator->errors()->add('parent_id', 'Danh mục không thể chọn chính nó làm danh mục cha.');

                    return;
                }

                $parent = Category::find($parentId);

                if (! $parent) {
                    return;
                }

                if ($parent->parent_id) {
                    $validator->errors()->add('parent_id', 'Chỉ hỗ trợ tối đa hai cấp danh mục, không thể chọn danh mục con làm cha.');
                }

                if ($category && $category->children()->exists()) {
                    $validator->errors()->add('parent_id', 'Danh mục đang có danh mục con nên không thể chuyển xuống làm danh mục con.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá :max ký tự.',
            'parent_id.exists' => 'Danh mục cha không tồn tại.',
            'slug.max' => 'Slug không được vượt quá :max ký tự.',
            'description.max' => 'Mô tả không được vượt quá :max ký tự.',
            'icon.max' => 'Icon không được vượt quá :max ký tự.',
            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị không được âm.',
        ];
    }
}
