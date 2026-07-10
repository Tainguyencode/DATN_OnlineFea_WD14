<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Khi tạo mới → luôn cho phép (middleware auth đã bảo vệ)
        // Khi cập nhật → kiểm tra quyền sở hữu
        if ($this->route('course')) {
            /** @var Course $course */
            $course = $this->route('course');

            return $course->isOwnedBy($this->user());
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'category_id'       => ['nullable', Rule::exists('categories', 'id')],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'objectives'        => ['nullable', 'string'],
            'thumbnail'         => ['nullable', 'image', 'max:2048'],
            'preview_video'     => ['nullable', 'string', 'max:2048'],
            'price'             => ['required', 'numeric', 'min:0', 'max:999999999'],
            'discount_price'    => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'level'             => ['nullable', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'language'          => ['required', 'string', 'max:10'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'        => 'Vui lòng nhập tên khóa học.',
            'title.string'          => 'Tên khóa học phải là chuỗi ký tự.',
            'title.max'             => 'Tên khóa học không được vượt quá :max ký tự.',

            'category_id.exists'    => 'Danh mục được chọn không tồn tại.',

            'short_description.string' => 'Mô tả ngắn phải là chuỗi ký tự.',
            'short_description.max'    => 'Mô tả ngắn không được vượt quá :max ký tự.',

            'description.string'    => 'Mô tả chi tiết phải là chuỗi ký tự.',

            'objectives.string'     => 'Mục tiêu khóa học phải là chuỗi ký tự.',

            'thumbnail.image'       => 'Ảnh thumbnail phải là file hình ảnh (PNG, JPG, WebP).',
            'thumbnail.max'         => 'Ảnh thumbnail không được vượt quá 2MB.',

            'preview_video.string'  => 'Link video giới thiệu phải là chuỗi ký tự.',
            'preview_video.max'     => 'Link video giới thiệu không được vượt quá :max ký tự.',

            'price.required'        => 'Vui lòng nhập giá gốc khóa học.',
            'price.numeric'         => 'Giá gốc phải là một số.',
            'price.min'             => 'Giá gốc không được nhỏ hơn :min.',
            'price.max'             => 'Giá gốc không được vượt quá :max.',

            'discount_price.numeric' => 'Giá khuyến mãi phải là một số.',
            'discount_price.min'     => 'Giá khuyến mãi không được nhỏ hơn :min.',
            'discount_price.lte'     => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',

            'level.in'              => 'Trình độ không hợp lệ. Chọn: Beginner, Intermediate hoặc Advanced.',

            'language.required'     => 'Vui lòng chọn ngôn ngữ khóa học.',
            'language.string'       => 'Ngôn ngữ phải là chuỗi ký tự.',
            'language.max'          => 'Ngôn ngữ không được vượt quá :max ký tự.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title'             => 'Tên khóa học',
            'category_id'       => 'Danh mục',
            'short_description' => 'Mô tả ngắn',
            'description'       => 'Mô tả chi tiết',
            'objectives'        => 'Mục tiêu khóa học',
            'thumbnail'         => 'Ảnh thumbnail',
            'preview_video'     => 'Video giới thiệu',
            'price'             => 'Giá gốc',
            'discount_price'    => 'Giá khuyến mãi',
            'level'             => 'Trình độ',
            'language'          => 'Ngôn ngữ',
        ];
    }
}
