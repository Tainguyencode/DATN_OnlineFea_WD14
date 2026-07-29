<?php

namespace App\Models;

use App\Enums\SupportTicketCategory;
use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'subject',
        'message',
        'category',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'last_replied_at',
        'last_replied_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => SupportTicketStatus::class,
            'priority' => SupportTicketPriority::class,
            'category' => SupportTicketCategory::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_replied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function lastReplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_replied_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->orderBy('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }

    public function isOwnedBy(User $user): bool
    {
        return (int) $this->user_id === (int) $user->id;
    }

    public function isClosedLike(): bool
    {
        return in_array($this->status, [SupportTicketStatus::Resolved, SupportTicketStatus::Closed], true);
    }

    public function hasStaffReply(): bool
    {
        return $this->messages()
            ->whereHas('user', fn ($q) => $q->where('role', 'admin'))
            ->exists();
    }
}
