<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionParticipant extends Model
{
    protected $fillable = [
        'discussion_id',
        'user_id',
        'role',
        'last_read_at',
        'unread_count',
    ];

    protected function casts(): array
    {
        return [
            'last_read_at' => 'datetime',
            'unread_count' => 'integer',
        ];
    }

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
