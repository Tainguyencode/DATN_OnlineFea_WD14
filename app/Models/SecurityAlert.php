<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityAlert extends Model
{
    protected $fillable = [
        'user_id',
        'type', // NEW_DEVICE, MULTIPLE_LOGIN, MULTIPLE_IP, TOKEN_INVALID, SESSION_KICKED
        'ip_address',
        'user_agent',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
