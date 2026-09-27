<?php

namespace App\Http\Requests\Instructor;

use App\Models\Category;
use App\Models\Course;
use App\Services\InstructorCourseCategoryAccess;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user()?->isApprovedInstructor()) {
            return false;
        }

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
        /** @var Course|null $course */
        $course = $this->route('course');

        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'objectives' => ['nullable', 'string', 'max:5000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
            'preview_video' => [
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) use ($course): void {
                    $previewVideo = trim((string) $value);
                    if ($previewVideo === '') {
                        return;
                    }

                    if ($course?->isCoursePreviewObjectKey($previewVideo)) {
                        return;
                    }

                    // Existing external links can be retained or removed, but
                    // the new form never accepts a new arbitrary external URL.
                    $scheme = strtolower((string) parse_url($previewVideo, PHP_URL_SCHEME));
                    $isLegacyCurrentValue = $course
                        && hash_equals((string) $course->preview_video, $previewVideo)
                        && in_array($scheme, ['http', 'https'], true)
                        && filter_var($previewVideo, FILTER_VALIDATE_URL);

                    if (! $isLegacyCurrentValue) {
                        $fail('Video giới thiệu phải là video MP4 được tải lên cho đúng khóa học này.');
                    }
                },
            ],
            'price' => ['required', 'numeric', 'multiple_of:1000', 'min:0', 'max:100000000'],
            'discount_price' => ['nullable', 'numeric', 'multiple_of:1000', 'min:0', 'max:100000000', 'lte:price'],
            'level' => ['nullable', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'language' => ['sometimes', 'string', 'max:10'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $categoryId = $this->integer('category_id');

                if (! $categoryId || $validator->errors()->has('category_id')) {
                    return;
                }

                $category = Category::with('parent:id,status')->find($categoryId);

                if (! $category) {
                    return;
                }

                $access = app(InstructorCourseCategoryAccess::class);
                $course = $this->route('course');
                $isKeepingExistingCategory = $course
                    && (int) $course->category_id === $categoryId
                    && $access->canManageCourse($this->user(), $course);

                if (! $isKeepingExistingCategory && ! $access->canTeachCategory($this->user(), $categoryId)) {
                    $validator->errors()->add('category_id', 'Bạn không có quyền tạo khóa học thuộc ngành này.');
                }

                if (! $category->status) {
                    $validator->errors()->add('category_id', 'Danh mục được chọn đang bị tắt.');
                }

                if (! $category->parent_id && $category->children()->active()->exists()) {
                    $validator->errors()->add('category_id', 'Vui lòng chọn danh mục con, không chọn trực tiếp danh mục cha.');
                }

                if ($category->parent_id && ! $category->parent?->status) {
                    $validator->errors()->add('category_id', 'Danh mục cha của danh mục được chọn đang bị tắt.');
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
            'title.required' => 'Vui lòng nhập tên khóa học.',
            'title.string' => 'Tên khóa học phải là chuỗi ký tự.',
            'title.max' => 'Tên khóa học không được vượt quá :max ký tự.',

            'category_id.required' => 'Vui lòng chọn danh mục khóa học.',
            'category_id.exists' => 'Danh mục được chọn không tồn tại.',

            'short_description.string' => 'Mô tả ngắn phải là chuỗi ký tự.',
            'short_description.max' => 'Mô tả ngắn không được vượt quá :max ký tự.',

            'description.string' => 'Mô tả chi tiết phải là chuỗi ký tự.',

            'objectives.string' => 'Mục tiêu khóa học phải là chuỗi ký tự.',

            'thumbnail.image' => 'Ảnh thumbnail phải là file hình ảnh hợp lệ (PNG, JPG, JPEG, WebP, GIF).',
            'thumbnail.mimes' => 'Ảnh thumbnail chỉ chấp nhận định dạng JPG, JPEG, PNG, WebP hoặc GIF.',
            'thumbnail.max' => 'Ảnh thumbnail không được vượt quá 2MB.',

            'preview_video.max' => 'Video giới thiệu không được vượt quá :max ký tự.',

            'price.required' => 'Vui lòng nhập giá gốc khóa học.',
            'price.numeric' => 'Giá gốc phải là một số.',
            'price.multiple_of' => 'Giá gốc phải là bội số của 1.000 VNĐ (ví dụ: 10.000, 50.000, 100.000).',
            'price.min' => 'Giá gốc không được nhỏ hơn :min.',
            'price.max' => 'Giá gốc không được vượt quá 100.000.000 VNĐ.',

            'discount_price.numeric' => 'Giá khuyến mãi phải là một số.',
            'discount_price.multiple_of' => 'Giá khuyến mãi phải là bội số của 1.000 VNĐ.',
            'discount_price.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',
            'discount_price.max' => 'Giá khuyến mãi không được vượt quá 100.000.000 VNĐ.',
            'discount_price.lte' => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.',

            'level.in' => 'Trình độ không hợp lệ. Chọn: Beginner, Intermediate hoặc Advanced.',

            'language.required' => 'Vui lòng chọn ngôn ngữ khóa học.',
            'language.string' => 'Ngôn ngữ phải là chuỗi ký tự.',
            'language.max' => 'Ngôn ngữ không được vượt quá :max ký tự.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'Tên khóa học',
            'category_id' => 'Danh mục',
            'short_description' => 'Mô tả ngắn',
            'description' => 'Mô tả chi tiết',
            'objectives' => 'Mục tiêu khóa học',
            'thumbnail' => 'Ảnh thumbnail',
            'preview_video' => 'Video giới thiệu',
            'price' => 'Giá gốc',
            'discount_price' => 'Giá khuyến mãi',
            'level' => 'Trình độ',
            'language' => 'Ngôn ngữ',
        ];
    }
}
