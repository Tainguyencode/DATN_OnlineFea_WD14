<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyGroup extends Model
{
    protected $fillable = [
        'course_id',
        'creator_id',
        'name',
        'description',
        'max_members',
    ];

    protected function casts(): array
    {
        return [
            'course_id' => 'integer',
            'creator_id' => 'integer',
            'max_members' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'study_group_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function studyGroupMembers(): HasMany
    {
        return $this->hasMany(StudyGroupMember::class);
    }

    public function isFull(): bool
    {
        return $this->members()->count() >= $this->max_members;
    }

    public function hasMember(int $userId): bool
    {
        return $this->members()->where('users.id', $userId)->exists();
    }
}
