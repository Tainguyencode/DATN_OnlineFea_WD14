<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyRewardLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_key',
        'user_id',
        'rank',
        'coupon_id',
        'user_coupon_id',
        'discount_value',
        'discount_type',
        'granted_at',
    ];

    protected function casts(): array
    {
        return [
            'granted_at' => 'datetime',
            'discount_value' => 'decimal:2',
            'rank' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function userCoupon(): BelongsTo
    {
        return $this->belongsTo(UserCoupon::class);
    }
}
