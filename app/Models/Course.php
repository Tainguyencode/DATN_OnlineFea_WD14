<?php

namespace App\Models;

use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @deprecated Use CourseStatus enum */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending_review';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    public const STATUSES = [
        'draft', 'pending_review', 'approved', 'rejected', 'published', 'suspended', 'archived',
    ];

    protected $fillable = [
        'instructor_id', 'category_id', 'title', 'slug', 'short_description',
        'description', 'objectives', 'target_audience', 'requirements',
        'thumbnail', 'preview_video', 'price',
        'discount_price', 'sale_price', 'level', 'language', 'status', 'is_published',
        'reject_reason', 'rejection_reason', 'rating_avg', 'rating_count',
        'enrollment_count', 'duration_minutes', 'tags', 'is_featured',
        'published_at', 'submitted_at', 'approved_at', 'suspended_at', 'submission_count',
        'required_video_percent', 'required_lesson_percent', 'minimum_quiz_score',
        'require_all_quizzes', 'require_all_assignments', 'certificate_enabled',
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
            'require_all_quizzes' => 'boolean',
            'require_all_assignments' => 'boolean',
            'certificate_enabled' => 'boolean',
            'published_at' => 'datetime',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
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
        return $this->hasMany(CourseReview::class)->orderByDesc('submission_number');
    }

    public function latestCourseReview(): ?CourseReview
    {
        return $this->courseReviews()->first();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
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

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED], true);
    }

    public function statusEnum(): CourseStatus
    {
        return CourseStatus::from($this->status);
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

    public function requiredVideoPercent(): int
    {
        return (int) ($this->required_video_percent ?? config('course.default_required_video_percent'));
    }

    public function requiredLessonPercent(): int
    {
        return (int) ($this->required_lesson_percent ?? config('course.default_required_lesson_percent'));
    }

    public function minimumQuizScore(): int
    {
        return (int) ($this->minimum_quiz_score ?? config('course.default_minimum_quiz_score'));
    }

    public function curriculumSections()
    {
        return $this->courseSections->isNotEmpty()
            ? $this->courseSections
            : $this->chapters;
    }

    public function firstLesson(): ?Lesson
    {
        $this->loadMissing([
            'courseSections' => fn ($q) => $q->orderBy('sort_order'),
            'courseSections.lessons' => fn ($q) => $q->orderBy('sort_order'),
            'chapters' => fn ($q) => $q->orderBy('sort_order'),
            'chapters.lessons' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        foreach ($this->curriculumSections() as $section) {
            $lesson = $section->lessons->first();
            if ($lesson) {
                return $lesson;
            }
        }

        return $this->lessons()->orderBy('sort_order')->first();
    }

    public function learningEntryUrl(): ?string
    {
        $lesson = $this->firstLesson();

        return $lesson
            ? route('courses.lessons.show', [$this, $lesson])
            : null;
    }
}
