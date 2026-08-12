<?php

namespace App\Http\Requests\Instructor;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isInstructor();
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id;
        $instructorId = auth()->id();

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                $this->input('type') === 'percent' ? 'max:100' : 'max:999999999',
            ],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'course_id' => [
                'nullable',
                'integer',
                Rule::exists('courses', 'id')->where(fn ($query) => $query->where('instructor_id', $instructorId)),
            ],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại trên hệ thống.',
            'code.alpha_dash' => 'Mã giảm giá chỉ được bao gồm chữ cái, chữ số, dấu gạch ngang và gạch dưới.',
            'type.required' => 'Vui lòng chọn loại giảm giá.',
            'type.in' => 'Loại giảm giá không hợp lệ.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
            'value.numeric' => 'Giá trị giảm phải là một số.',
            'value.min' => 'Giá trị giảm phải lớn hơn 0.',
            'value.max' => 'Giá trị giảm theo phần trăm không được vượt quá 100%.',
            'course_id.exists' => 'Khóa học được chọn không hợp lệ hoặc không thuộc sở hữu của bạn.',
            'expires_at.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu.',
        ];
    }
}
