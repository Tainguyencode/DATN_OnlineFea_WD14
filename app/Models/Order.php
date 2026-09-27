<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Order extends Model
{
    public function canCancel(): bool
    {
        return $this->status === 'pending';
    }

    protected $fillable = [
        'order_code', 'idempotency_key', 'user_id', 'coupon_id', 'subtotal', 'discount_amount',
        'total_amount', 'status', 'payment_method', 'transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getItemsAttribute($value)
    {
        if ($this->relationLoaded('items')) {
            return $this->getRelation('items');
        }

        if (! is_null($value)) {
            return is_string($value) ? json_decode($value, true) : (array) $value;
        }

        return $this->items()->get();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /** Reserve an immutable gateway reference before making any network request. */
    public function prepareGatewayPayment(string $gateway, string $reference): Payment
    {
        return DB::transaction(function () use ($gateway, $reference): Payment {
            $order = static::query()->lockForUpdate()->findOrFail($this->id);
            if ($order->status !== 'pending' || (float) $order->total_amount <= 0) {
                throw new RuntimeException('Đơn hàng không còn chờ thanh toán.');
            }
            $payment = $order->payment()->lockForUpdate()->first();
            if ($payment && filled($payment->gateway_order_code)) {
                if ($payment->gateway !== $gateway) {
                    throw new RuntimeException('Đơn đã phát hành liên kết thanh toán. Không thể đổi cổng; vui lòng dùng liên kết đã tạo.');
                }

                return $payment;
            }

            $payment ??= new Payment(['order_id' => $order->id]);
            $payment->fill([
                'gateway' => $gateway,
                'gateway_order_code' => $reference,
                'amount' => $order->total_amount,
                'status' => 'pending',
            ])->save();
            $order->update(['payment_method' => $gateway]);

            return $payment;
        });
    }

    /** Call only while holding this order's row lock. */
    public function assertPaymentEditable(): void
    {
        abort_unless($this->status === 'pending', 409, 'Đơn hàng không còn chờ thanh toán.');
        abort_if($this->payment()->whereNotNull('gateway_order_code')->exists(), 409,
            'Đơn đã phát hành liên kết thanh toán; không thể đổi số tiền hoặc hủy trước khi đối soát.');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class)->latestOfMany();
    }
}
