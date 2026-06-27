<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningPath extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'thumbnail', 'level', 'course_ids'];

    protected function casts(): array
    {
        return [
            'course_ids' => 'array',
        ];
    }
}
