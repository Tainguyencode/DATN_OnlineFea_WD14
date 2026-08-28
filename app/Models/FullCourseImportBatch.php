<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FullCourseImportBatch extends Model
{
    public const STATUS_PREVIEWED = 'previewed';

    protected $fillable = [
        'token', 'user_id', 'original_filename', 'file_sha256', 'canonical_payload',
        'validation_report', 'row_count', 'valid_count', 'warning_count', 'error_count',
        'status', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'canonical_payload' => 'array',
            'validation_report' => 'array',
            'row_count' => 'integer',
            'valid_count' => 'integer',
            'warning_count' => 'integer',
            'error_count' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
