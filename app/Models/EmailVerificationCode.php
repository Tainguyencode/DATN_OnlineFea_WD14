<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailVerificationCode extends Model
{
    public const MAX_ATTEMPTS = 5;

    public const EXPIRY_MINUTES = 10;

    public const RESEND_COOLDOWN_SECONDS = 60;

    protected $fillable = [
        'user_id',
        'code_hash',
        'expires_at',
        'used_at',
        'attempt_count',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'last_sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->used_at === null
            && $this->expires_at->isFuture()
            && $this->attempt_count < self::MAX_ATTEMPTS;
    }
}
