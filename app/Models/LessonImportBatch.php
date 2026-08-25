<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonImportBatch extends Model
{
    public const STATUS_PREVIEWED = 'previewed';

    public const STATUS_IMPORTING = 'importing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'token',
        'user_id',
        'course_id',
        'section_id',
        'original_filename',
        'file_sha256',
        'template_version',
        'canonical_payload',
        'validation_report',
        'row_count',
        'valid_count',
        'warning_count',
        'error_count',
        'status',
        'imported_count',
        'expires_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'template_version' => 'integer',
            'canonical_payload' => 'array',
            'validation_report' => 'array',
            'row_count' => 'integer',
            'valid_count' => 'integer',
            'warning_count' => 'integer',
            'error_count' => 'integer',
            'imported_count' => 'integer',
            'expires_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }
}
