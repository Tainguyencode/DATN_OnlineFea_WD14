<?php

namespace App\Http\Requests\Admin;

use App\Models\CourseReview;
use App\Models\CourseReviewItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminCourseReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Middleware role:admin đã bảo vệ route — luôn true ở đây
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $checklistKeys = CourseReviewItem::ADMIN_CHECKLIST_KEYS;

        $checklistRules = [];
        foreach ($checklistKeys as $key) {
            $checklistRules["checklist.{$key}.status"] = ['required', Rule::in(['pass', 'fail'])];
            $checklistRules["checklist.{$key}.note"] = ['nullable', 'string', 'max:500'];
        }

        return array_merge($checklistRules, [
            'action' => ['required', Rule::in(CourseReview::ACTIONS)],
            'comment' => [
                Rule::when(
                    in_array($this->input('action'), [
                        CourseReview::ACTION_NEED_REVISION,
                        CourseReview::ACTION_REJECTED,
                    ], true),
                    ['required', 'string', 'max:2000'],
                    ['nullable', 'string', 'max:2000'],
                ),
            ],
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Vui lòng chọn quyết định duyệt.',
            'action.in' => 'Quyết định duyệt không hợp lệ.',
            'comment.required' => 'Lý do / ghi chú bắt buộc khi yêu cầu chỉnh sửa hoặc từ chối.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attrs = [
            'action' => 'Quyết định',
            'comment' => 'Lý do / ghi chú',
        ];

        foreach (CourseReviewItem::ADMIN_CHECKLIST_KEYS as $key) {
            $label = CourseReviewItem::ITEM_LABELS[$key] ?? $key;
            $attrs["checklist.{$key}.status"] = "Kết quả mục \"{$label}\"";
        }

        return $attrs;
    }
}
