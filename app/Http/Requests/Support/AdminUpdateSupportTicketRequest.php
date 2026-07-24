<?php

namespace App\Http\Requests\Support;

use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var SupportTicket $ticket */
        $ticket = $this->route('ticket');

        return $this->user()?->can('manage', $ticket) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(SupportTicketStatus::values())],
            'priority' => ['nullable', Rule::in(SupportTicketPriority::values())],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'admin')],
        ];
    }
}
