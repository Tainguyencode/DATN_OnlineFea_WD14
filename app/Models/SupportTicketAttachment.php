<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SupportTicketAttachment extends Model
{
    public const DISK = 'local';

    protected $fillable = [
        'ticket_id',
        'message_id',
        'user_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(SupportTicketMessage::class, 'message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function existsOnDisk(): bool
    {
        return $this->file_path !== ''
            && Storage::disk(self::DISK)->exists($this->file_path);
    }

    public function absolutePath(): ?string
    {
        if (! $this->existsOnDisk()) {
            return null;
        }

        return Storage::disk(self::DISK)->path($this->file_path);
    }
}
