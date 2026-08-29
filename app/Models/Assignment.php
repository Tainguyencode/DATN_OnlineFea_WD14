<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'due_days',
        'max_score',
        'passing_score',
        'is_required',
        'allowed_file_types',
        'maximum_file_size',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'max_score' => 'integer',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(AssignmentVersion::class);
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->belongsTo(AssignmentVersion::class, 'published_version_id');
    }

    public function draftVersion(): BelongsTo
    {
        return $this->belongsTo(AssignmentVersion::class, 'draft_version_id');
    }
}
