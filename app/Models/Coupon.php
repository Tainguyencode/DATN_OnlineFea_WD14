<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'creator_type', 'instructor_id', 'course_id', 'type', 'value', 'min_order_amount', 'max_uses',
        'used_count', 'starts_at', 'expires_at', 'is_active', 'is_private',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'is_private' => 'boolean',
        ];
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Chỉ lấy các mã còn khả dụng và chưa từng được học viên sử dụng.
     */
    public function scopeAvailableToUser(Builder $query, int $userId): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) use ($userId): void {
                $query->where('is_private', false)
                    ->orWhereHas('userCoupons', fn (Builder $grant) => $grant
                        ->where('user_id', $userId)->whereNull('used_at'));
            })
            ->whereDoesntHave('userCoupons', function (Builder $query) use ($userId): void {
                $query->where('user_id', $userId)->whereNotNull('used_at');
            })
            ->whereDoesntHave('orders', function (Builder $query) use ($userId): void {
                $query->where('user_id', $userId)->where('status', 'paid');
            })
            ->where(function (Builder $query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
            });
    }

    public function isInstructorCoupon(): bool
    {
        return $this->creator_type === 'instructor';
    }

    public function isAdminCoupon(): bool
    {
        return ! $this->isInstructorCoupon();
    }

    public function isPercentType(): bool
    {
        return $this->type === 'percent';
    }

    /**
     * Tính số tiền giảm giá dựa trên tổng tiền đủ điều kiện.
     */
    public function calculateDiscount(float $eligibleSubtotal): float
    {
        if ($eligibleSubtotal <= 0) {
            return 0;
        }

        return $this->isPercentType()
            ? $eligibleSubtotal * ($this->value / 100)
            : min($this->value, $eligibleSubtotal);
    }

    public function isEligibleForCourse(Course $course): bool
    {
        if ($this->isAdminCoupon()) {
            if ($this->course_id && (int) $this->course_id !== (int) $course->id) {
                return false;
            }

            return true;
        }

        if ($this->isInstructorCoupon()) {
            if ((int) $this->instructor_id !== (int) $course->instructor_id) {
                return false;
            }
            if ($this->course_id && (int) $this->course_id !== (int) $course->id) {
                return false;
            }

            return true;
        }

        return false;
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function canBeUsedBy(int $userId): bool
    {
        return $this->isValid()
            && ! $this->isUsedByUser($userId)
            && (! $this->is_private || $this->userCoupons()
                ->where('user_id', $userId)->whereNull('used_at')->exists());
    }

    /**
     * Kiểm tra xem người dùng đã từng sử dụng mã giảm giá này chưa.
     */
    public function isUsedByUser(int $userId): bool
    {
        return $this->userCoupons()
            ->where('user_id', $userId)
            ->whereNotNull('used_at')
            ->exists()
            || Order::where('user_id', $userId)
            ->where('coupon_id', $this->id)
            ->where('status', 'paid')
            ->exists();
    }
}
