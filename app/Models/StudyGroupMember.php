<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyGroupMember extends Model
{
    protected $fillable = [
        'study_group_id',
        'user_id',
        'role',
    ];

    protected function casts(): array
    {
        return [
            'study_group_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function studyGroup(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
