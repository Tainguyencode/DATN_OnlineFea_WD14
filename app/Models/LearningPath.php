<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
