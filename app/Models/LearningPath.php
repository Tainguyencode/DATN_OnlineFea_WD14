<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningPath extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'slug',
        'description',
        'thumbnail',
        'level',
        'target_role',
        'salary_range',
        'estimated_duration',
        'skills',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'learning_path_courses')
            ->withPivot(['sort_order', 'stage_name'])
            ->orderByPivot('sort_order');
    }

    public function aiChatMessages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class);
    }
}
