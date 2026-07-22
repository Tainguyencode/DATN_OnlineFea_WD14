<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAccessLog extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'ip_address',
        'user_agent',
        'browser',
        'platform',
        'device',
        'watch_started_at',
        'watch_ended_at',
        'watch_duration'
    ];

    protected $casts = [
        'watch_started_at' => 'datetime',
        'watch_ended_at' => 'datetime',
        'watch_duration' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
