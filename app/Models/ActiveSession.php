<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'device_id',
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device_name',
        'is_active',
        'last_activity'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
