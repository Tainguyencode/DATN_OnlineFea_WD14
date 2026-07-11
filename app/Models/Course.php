<?php

namespace App\Models;

use App\Data\CourseSubmissionCheckResult;
use App\Services\CourseSubmissionValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_NEED_REVISION = 'need_revision';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_ARCHIVED = 'archived';

    /** @deprecated Use STATUS_SUBMITTED */
    public const STATUS_PENDING = 'submitted';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SUBMITTED,
        self::STATUS_NEED_REVISION,
        self::STATUS_APPROVED,
        self::STATUS_PUBLISHED,
        self::STATUS_REJECTED,
        self::STATUS_ARCHIVED,
    ];

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Nháp',
        self::STATUS_SUBMITTED => 'Đã gửi duyệt',
        self::STATUS_NEED_REVISION => 'Cần chỉnh sửa',
        self::STATUS_APPROVED => 'Đã duyệt',
        self::STATUS_PUBLISHED => 'Đã xuất bản',
        self::STATUS_REJECTED => 'Bị từ chối',
        self::STATUS_ARCHIVED => 'Đã ẩn',
    ];

    public const MIN_LESSON_COUNT = 5;

    public const MIN_VIDEO_DURATION_MINUTES = 30;

    protected $fillable = [
        'instructor_id', 'category_id', 'title', 'slug', 'short_description',
        'description', 'objectives', 'thumbnail', 'preview_video', 'price',
        'discount_price', 'sale_price', 'level', 'language', 'status', 'is_published',
        'reject_reason', 'rejection_reason', 'rating_avg', 'rating_count',
        'enrollment_count', 'duration_minutes', 'tags', 'is_featured',
        'published_at', 'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'rating_avg' => 'decimal:2',
            'tags' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'submitted_at' => 'datetime',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('sort_order');
    }

    public function courseSections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('sort_order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function courseReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class)->orderByDesc('reviewed_at')->orderByDesc('id');
    }

    public function latestCourseReview(): ?CourseReview
    {
        return $this->courseReviews()->first();
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->sale_price ?? $this->price);
    }

    public function isOwnedBy(User $user): bool
    {
        return (int) $this->instructor_id === (int) $user->id;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && (bool) $this->is_published;
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->wishlists()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function rejectionReasonText(): ?string
    {
        return $this->reject_reason ?: $this->rejection_reason;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function canBeSubmittedForReview(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_NEED_REVISION,
            self::STATUS_REJECTED,
        ], true);
    }

    public function isAwaitingAdminReview(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function totalVideoDurationSeconds(): int
    {
        return (int) $this->lessons()
            ->get(['duration_seconds', 'duration'])
            ->sum(fn (Lesson $lesson) => (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0));
    }

    public function totalVideoDurationMinutes(): int
    {
        return (int) floor($this->totalVideoDurationSeconds() / 60);
    }

    public function lessonCount(): int
    {
        return $this->lessons()->count();
    }

    public function submissionCheck(): CourseSubmissionCheckResult
    {
        return app(CourseSubmissionValidator::class)->validate($this);
    }

    public function isReadyForSubmission(): bool
    {
        return $this->submissionCheck()->passes();
    }
}
