<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'scope',
        'title',
        'current_topic',
        'course_id',
        'lesson_id',
        'learning_path_id',
        'context_data',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'context_data' => 'array',
            'last_activity_at' => 'datetime',
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

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class, 'conversation_id')->orderBy('id');
    }
}
