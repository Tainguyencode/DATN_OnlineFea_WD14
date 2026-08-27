<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'reason',
        'bank_code',
        'bank_account_number',
        'bank_account_name',
        'status',
        'refund_method',
        'transaction_reference',
        'admin_note',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * VietQR chứa sẵn tài khoản nhận, số tiền và nội dung hoàn tiền.
     */
    public function getVietQrUrlAttribute(): string
    {
        $bankCode = rawurlencode((string) $this->bank_code);
        $accountNumber = rawurlencode((string) $this->bank_account_number);
        $amount = (int) $this->amount;
        $accountName = rawurlencode((string) $this->bank_account_name);
        $content = rawurlencode('HOAN TIEN DH '.($this->order?->order_code ?? $this->order_id));

        return "https://img.vietqr.io/image/{$bankCode}-{$accountNumber}-compact2.png?amount={$amount}&addInfo={$content}&accountName={$accountName}";
    }
}
