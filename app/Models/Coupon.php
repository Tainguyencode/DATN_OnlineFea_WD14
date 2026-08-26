<?php

namespace App\Models;

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

    /**
     * Kiểm tra xem người dùng đã từng sử dụng mã giảm giá này chưa.
     */
    public function isUsedByUser(int $userId): bool
    {
        return Order::where('user_id', $userId)
            ->where('coupon_id', $this->id)
            ->where('status', 'paid')
            ->exists();
    }
}
