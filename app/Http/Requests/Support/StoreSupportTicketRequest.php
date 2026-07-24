<?php

namespace App\Http\Requests\Support;

use App\Enums\SupportTicketCategory;
use App\Enums\SupportTicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\SupportTicket::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::in(SupportTicketCategory::values())],
            'priority' => ['nullable', Rule::in(SupportTicketPriority::values())],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,txt,doc,docx,zip'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Vui lòng nhập tiêu đề.',
            'message.required' => 'Vui lòng mô tả vấn đề.',
            'category.required' => 'Vui lòng chọn loại vấn đề.',
            'attachments.*.max' => 'Mỗi file đính kèm tối đa 5MB.',
            'attachments.*.mimes' => 'Định dạng file không được hỗ trợ.',
        ];
    }
}
