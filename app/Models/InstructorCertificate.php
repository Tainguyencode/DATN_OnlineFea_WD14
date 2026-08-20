<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'title',
        'status',
        'rejection_reason',
        'uploaded_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }

        return $bytes > 0 ? $bytes . ' B' : 'N/A';
    }

    public function isImage(): bool
    {
        return in_array(strtolower($this->mime_type ?? ''), ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'], true)
            || preg_match('/\.(jpg|jpeg|png|webp)$/i', $this->file_path);
    }

    public function isPdf(): bool
    {
        return strtolower($this->mime_type ?? '') === 'application/pdf'
            || preg_match('/\.pdf$/i', $this->file_path);
    }
}
