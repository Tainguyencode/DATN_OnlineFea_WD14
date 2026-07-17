<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'comment',
        'status',
        'helpful_count',
        'instructor_reply',
        'replied_by',
        'replied_at',
        'moderated_by',
        'moderated_at',
        'moderation_note',
        'verified_purchase',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'helpful_count' => 'integer',
            'status' => ReviewStatus::class,
            'verified_purchase' => 'boolean',
            'replied_at' => 'datetime',
            'moderated_at' => 'datetime',
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

    public function replier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function helpfulRecords(): HasMany
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    public function helpfulUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_helpful')->withTimestamps();
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReviewStatus::Approved->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', ReviewStatus::Pending->value);
    }

    public function scopeRating($query, ?int $rating)
    {
        return $rating ? $query->where('rating', $rating) : $query;
    }

    public function scopeMostHelpful($query)
    {
        return $query->orderByDesc('helpful_count')->orderByDesc('created_at');
    }
}
