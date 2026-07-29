<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'amount',
        'bank_code',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'transaction_ref',
        'admin_note',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sinh URL mã VietQR Napas247 phục vụ Admin chuyển tiền thật trực tiếp.
     */
    public function getVietQrUrlAttribute(): string
    {
        $bankCode = urlencode($this->bank_code ?? 'MB');
        $accNo = urlencode($this->bank_account_number ?? '');
        $amount = (int) $this->amount;
        $accountName = urlencode($this->bank_account_name ?? '');
        $addInfo = urlencode("RUT TIEN MAGV " . $this->user_id . " REQ" . $this->id);

        return "https://img.vietqr.io/image/{$bankCode}-{$accNo}-compact2.png?amount={$amount}&addInfo={$addInfo}&accountName={$accountName}";
    }

    /**
     * Lấy nhãn tiếng Việt cho trạng thái.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Chờ duyệt',
            self::STATUS_APPROVED => 'Đã chuyển tiền',
            self::STATUS_REJECTED => 'Từ chối',
            default => 'Không xác định',
        };
    }
}
