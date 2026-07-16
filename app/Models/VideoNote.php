<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoNote extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'timestamp_seconds',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'timestamp_seconds' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
