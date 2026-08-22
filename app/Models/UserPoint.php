<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'source',
        'description',
        'course_id',
        'reference_id',
        'reason',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'user_id' => 'integer',
            'course_id' => 'integer',
            'reference_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
