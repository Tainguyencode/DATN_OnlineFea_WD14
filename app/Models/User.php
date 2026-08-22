<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Services\EmailVerificationService;
use App\Services\RoleSyncService;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name', 'username', 'email', 'password', 'role', 'avatar', 'bio', 'phone',
    'google_id', 'facebook_id', 'github_id', 'microsoft_id',
    'two_factor_enabled', 'two_factor_secret', 'is_active',
    'account_status', 'locked_at', 'locked_reason',
    'reactivation_requested_at', 'reactivation_status', 'reactivation_reason', 'profile_deadline_at',
    'last_login_at', 'last_login_ip', 'password_changed_at',
    'last_learning_at', 'engagement_email_stage', 'last_engagement_sent_at',
    'commission_rate', 'bank_code', 'bank_name', 'bank_account_number', 'bank_account_name',
    'instructor_status', 'submitted_for_review_at', 'approved_at', 'approved_by', 'rejected_reason',
    'needs_admin_review', 'admin_last_reviewed_at',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function studyGroupInvitations(): HasMany
    {
        return $this->hasMany(StudyGroupInvitation::class, 'invited_user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function recentlyViewedCourses(): HasMany
    {
        return $this->hasMany(RecentlyViewedCourse::class);
    }

    public function favoriteCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'wishlists')->withTimestamps();
    }

    public function hasFavoritedCourse(Course|int $course): bool
    {
        $courseId = $course instanceof Course ? $course->id : $course;

        return $this->wishlists()
            ->where('course_id', $courseId)
            ->exists();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function helpfulCourseReviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_helpful')->withTimestamps();
    }

    public function courseReviewsAsReviewer(): HasMany
    {
        return $this->hasMany(CourseReview::class, 'reviewer_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function pushNotifications(): HasMany
    {
        return $this->hasMany(PushNotification::class);
    }

    public function unreadPushNotifications(): HasMany
    {
        return $this->pushNotifications()->where('is_read', false);
    }

    public function twoFactorCodes(): HasMany
    {
        return $this->hasMany(TwoFactorCode::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function lessonNotes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'study_group_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function createdStudyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'creator_id');
    }

    public function studyGroupMessages(): HasMany
    {
        return $this->hasMany(StudyGroupMessage::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(UserPoint::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at');
    }


    public function emailVerificationCodes(): HasMany
    {
        return $this->hasMany(EmailVerificationCode::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        app(EmailVerificationService::class)->sendCode($this);
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function syncPrimaryRole(?string $roleSlug = null): void
    {
        app(RoleSyncService::class)->syncPrimaryRole($this, $roleSlug);
    }

    protected static function booted(): void
    {
        static::saved(function (User $user): void {
            if ($user->wasRecentlyCreated || $user->wasChanged('role')) {
                $user->syncPrimaryRole();
            }
        });
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function lessonComments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    public function instructorCertificates(): HasMany
    {
        return $this->hasMany(InstructorCertificate::class)->orderByDesc('uploaded_at');
    }

    public function instructorProfile(): HasOne
    {
        return $this->hasOne(InstructorProfile::class);
    }

    public function instructorApplication(): HasOne
    {
        return $this->hasOne(InstructorApplication::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'submitted_for_review_at' => 'datetime',
            'approved_at' => 'datetime',
            'locked_at' => 'datetime',
            'reactivation_requested_at' => 'datetime',
            'profile_deadline_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'last_learning_at' => 'datetime',
            'engagement_email_stage' => 'integer',
            'last_engagement_sent_at' => 'datetime',
            'password_changed_at' => 'datetime',
            'commission_rate' => 'decimal:2',
            'needs_admin_review' => 'boolean',
            'admin_last_reviewed_at' => 'datetime',
        ];
    }

    public function scopeNeedsAdminReview($query)
    {
        return $query->where('needs_admin_review', true);
    }

    public function markNeedsAdminReview(): bool
    {
        return $this->update([
            'needs_admin_review' => true,
        ]);
    }

    public function markAdminReviewed(): bool
    {
        return $this->update([
            'needs_admin_review' => false,
            'admin_last_reviewed_at' => now(),
        ]);
    }

    public function isLocked(): bool
    {
        return $this->account_status === 'locked' || $this->account_status === 'suspended';
    }

    public function getInstructorDeadlineAtAttribute(): ?\Carbon\Carbon
    {
        if ($this->profile_deadline_at) {
            return $this->profile_deadline_at;
        }

        if (! $this->email_verified_at) {
            return null;
        }

        return $this->email_verified_at->copy()->addDays(7);
    }

    public function getInstructorDeadlineDaysRemainingAttribute(): int
    {
        $deadline = $this->instructor_deadline_at;
        if (! $deadline) {
            return 7;
        }

        if (now()->greaterThanOrEqualTo($deadline)) {
            return 0;
        }

        return (int) ceil(now()->floatDiffInDays($deadline, false));
    }

    public function isInstructorDeadlineExpired(): bool
    {
        if ($this->role !== 'instructor' || $this->instructor_status === 'approved' || $this->isLocked()) {
            return false;
        }

        $deadline = $this->instructor_deadline_at;
        if (! $deadline) {
            return false;
        }

        return $this->submitted_for_review_at === null && now()->greaterThan($deadline);
    }

    public function lockDueToProfileDeadline(string $reason = 'Bạn chưa hoàn thiện hồ sơ chứng chỉ trong thời hạn 7 ngày.'): void
    {
        $this->update([
            'account_status' => 'locked',
            'locked_at' => now(),
            'locked_reason' => $reason,
            'reactivation_status' => 'none',
        ]);
    }

    public function canRequestReactivation(): bool
    {
        if (! $this->isLocked() || ! $this->locked_at) {
            return false;
        }

        if ($this->reactivation_status === 'pending') {
            return false;
        }

        $cooldownEndsAt = $this->locked_at->copy()->addDays(14);

        return now()->greaterThanOrEqualTo($cooldownEndsAt);
    }

    public function reactivationCooldownDaysRemaining(): int
    {
        if (! $this->locked_at) {
            return 0;
        }

        $cooldownEndsAt = $this->locked_at->copy()->addDays(14);
        if (now()->greaterThanOrEqualTo($cooldownEndsAt)) {
            return 0;
        }

        return (int) ceil(now()->floatDiffInDays($cooldownEndsAt, false));
    }

    public function unlockAccount(string $status = 'active', string $instructorStatus = 'pending'): void
    {
        $this->update([
            'account_status' => 'active',
            'locked_at' => null,
            'locked_reason' => null,
            'reactivation_status' => 'approved',
            'instructor_status' => $instructorStatus,
            'profile_deadline_at' => now()->addDays(7),
        ]);
    }

    public function demoteToStudentDueToExpiry(): void
    {
        $this->lockDueToProfileDeadline();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return in_array($this->role, ['instructor', 'admin'], true);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function hasPermissionTo(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $roleSlugs = $this->roles()->pluck('slug')->push($this->role)->unique();

        return Role::whereIn('slug', $roleSlugs)
            ->whereHas('permissions', fn ($query) => $query->where('slug', $permission))
            ->exists();
    }

    public function dashboardUrl(): string
    {
        if ($this->role === 'instructor') {
            if ($this->isLocked()) {
                return route('instructor.profile');
            }

            return route('instructor.dashboard');
        }

        return match ($this->role) {
            'admin' => route('admin.dashboard'),
            default => route('student.dashboard'),
        };
    }

    public function avatarUrl(): string
    {
        if (! $this->avatar) {
            return 'https://ui-avatars.com/api/?name='.urlencode($this->name ?? 'User').'&background=4f46e5&color=fff';
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        if (Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name ?? 'User').'&background=4f46e5&color=fff';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatarUrl();
    }

    public function getCommissionRate(): float
    {
        if ($this->commission_rate !== null) {
            return (float) $this->commission_rate;
        }

        $default = SystemSetting::get('default_commission_rate', config('course.default_commission_rate', 20.00));

        return (float) $default;
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function getTotalEarningsAttribute(): float
    {
        return (float) \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('courses', 'courses.id', '=', 'order_items.course_id')
            ->where('courses.instructor_id', $this->id)
            ->where('orders.status', 'paid')
            ->sum('order_items.instructor_earning');
    }

    public function getTotalWithdrawnAttribute(): float
    {
        return (float) $this->withdrawals()
            ->where('status', Withdrawal::STATUS_APPROVED)
            ->sum('amount');
    }

    public function getPendingWithdrawalAttribute(): float
    {
        return (float) $this->withdrawals()
            ->where('status', Withdrawal::STATUS_PENDING)
            ->sum('amount');
    }

    public function getAvailableBalanceAttribute(): float
    {
        return max(0, $this->total_earnings - $this->total_withdrawn - $this->pending_withdrawal);
    }
}
